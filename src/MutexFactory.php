<?php

namespace Swordev\Mutex;

abstract class MutexFactory
{
	static function create(string $className, string $key): MutexInterface
	{
		return new $className($key);
	}
}
