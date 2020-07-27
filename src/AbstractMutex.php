<?php

namespace Swordev\Mutex;

abstract class AbstractMutex implements MutexInterface
{

	static int $defaultsTimeout = 0;

	protected string $key;
	protected bool $locked = false;

	abstract function lock(bool $writeMode, int $timeout): bool;
	abstract function unlock(): bool;

	function __construct(string $key)
	{
		$this->key = $key;
	}

	function __destruct()
	{
		$this->unlock();
	}

	function getKey(): string
	{
		return $this->key;
	}

	function isLocked(): bool
	{
		return $this->locked;
	}

	function writeLock(int $timeout = null): bool
	{
		return $this->lock(true, $timeout ?? static::$defaultsTimeout);
	}

	function readLock(int $timeout = null): bool
	{
		return $this->lock(false, $timeout ?? static::$defaultsTimeout);
	}
}
