<?php

namespace Swordev\Mutex;

interface MutexInterface
{
	function __construct(string $key);
	function getKey(): string;
	function lock(bool $writeMode, int $timeout): bool;
	function unlock(): bool;
	function writeLock(int $timeout = null): bool;
	function readLock(int $timeout = null): bool;
	function isLocked(): bool;
}
