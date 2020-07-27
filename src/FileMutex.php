<?php

namespace Swordev\Mutex;

use Exception;

class FileMutex extends AbstractMutex
{

	static ?string $defaultsDirName;
	static int $defaultsTryDelay = 500;

	public ?string $dirName;
	public ?int $tryDelay;

	protected string $baseName;
	/**
	 * @var resource|null
	 */
	protected $fileHandle = null;

	function __construct(string $key)
	{
		parent::__construct($key);
		$this->baseName = urlencode($this->key) . '.lock';
	}

	function getBaseName(): string
	{
		return $this->baseName;
	}

	function getDirName(): string
	{
		return $this->dirName ?? static::$defaultsDirName ?? sys_get_temp_dir();
	}

	function getAbsolutePath(): string
	{
		return $this->getDirName() . DIRECTORY_SEPARATOR . $this->getBaseName();
	}

	/**
	 * @param array<mixed> $error
	 */
	static function isPermissionDeniedError(array $error): bool
	{
		$pattern = 'Permission denied';
		return substr_compare($error['message'], $pattern, -strlen($pattern)) === 0;
	}

	function lock(bool $writeMode, int $timeout): bool
	{

		if ($this->fileHandle)
			$this->unlock();

		$fileHandle = @fopen($this->getAbsolutePath(), 'w+');

		if (!$fileHandle) {

			/**
			 * @var array<mixed>
			 */
			$error = error_get_last();

			if (static::isPermissionDeniedError($error))
				return false;

			throw new Exception($error['message']);
		}

		$this->fileHandle = $fileHandle;

		$start = microtime(true);
		$flags = $writeMode ? (LOCK_EX | LOCK_NB) : (LOCK_SH | LOCK_NB);
		$tryDelay = (int) (($this->tryDelay ?? static::$defaultsTryDelay) / 1000);

		while (1) {

			$this->locked = flock($this->fileHandle, $flags);

			if ($this->locked || !$timeout)
				break;

			$ellapsed = microtime(true) - $start;

			if ($ellapsed >= $timeout)
				break;

			if ($tryDelay)
				sleep($tryDelay);
		}

		if (!$this->locked)
			$this->unlock();

		return $this->locked;
	}

	function unlock(): bool
	{

		if ($this->fileHandle) {
			if (!fclose($this->fileHandle))
				return false;
			$this->fileHandle = null;
			$this->locked = false;
			@unlink($this->getAbsolutePath());
		}

		return true;
	}
}
