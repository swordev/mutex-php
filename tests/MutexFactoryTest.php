<?php

namespace Swordev\Mutex;

class MutexFactoryTest extends \PHPUnit\Framework\TestCase
{

	function testCreate(): void
	{
		$mutex = MutexFactory::create(FileMutex::class, 'key');
		$this->assertTrue($mutex instanceof FileMutex);
		$this->assertEquals($mutex->getKey(), 'key');
	}
}
