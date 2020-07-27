<?php

namespace Swordev\Mutex;

class FileMutexTest extends \PHPUnit\Framework\TestCase
{

	function testGetKey(): void
	{
		$mutex = new FileMutex(__CLASS__ . '_' . __FUNCTION__);
		$this->assertEquals($mutex->getKey(), __CLASS__ . '_' . __FUNCTION__);
	}

	function testGetBaseName(): void
	{
		$this->assertEquals(
			(new FileMutex('key'))->getBaseName(),
			'key.lock'
		);
		$this->assertEquals(
			(new FileMutex('k e y'))->getBaseName(),
			'k+e+y.lock'
		);
		$this->assertEquals(
			(new FileMutex(__CLASS__ . '_' . __FUNCTION__))->getBaseName(),
			'Swordev%5CMutex%5CFileMutexTest_testGetBaseName.lock'
		);
	}

	function testGetDirName(): void
	{
		$mutex = new FileMutex('');

		$this->assertEquals($mutex->getDirName(), sys_get_temp_dir());

		$mutex->dirName = 'dir1';
		$this->assertEquals($mutex->getDirName(), 'dir1');

		$mutex->dirName = null;
		$this->assertEquals($mutex->getDirName(), sys_get_temp_dir());

		FileMutex::$defaultsDirName = 'dir2';
		$this->assertEquals($mutex->getDirName(), 'dir2');

		FileMutex::$defaultsDirName = null;
		$this->assertEquals($mutex->getDirName(), sys_get_temp_dir());
	}

	function testGetAbsolutePath(): void
	{
		$mutex = new FileMutex(__CLASS__ . '_' . __FUNCTION__);
		$baseName = 'Swordev%5CMutex%5CFileMutexTest_testGetAbsolutePath.lock';
		$this->assertEquals($mutex->getAbsolutePath(), sys_get_temp_dir() . DIRECTORY_SEPARATOR . $baseName);
	}

	function testIsLock(): void
	{
		$mutex = new FileMutex(__CLASS__ . '_' . __FUNCTION__);
		$this->assertFalse($mutex->isLocked());
	}

	function testLock_W(): void
	{
		$mutex = new FileMutex('key');
		$this->assertTrue($mutex->writeLock());
		$this->assertTrue($mutex->isLocked());
	}

	function testLock_R(): void
	{
		$mutex = new FileMutex('key');
		$this->assertTrue($mutex->readLock());
		$this->assertTrue($mutex->isLocked());
	}

	function testLock_WR(): void
	{
		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');
		$this->assertTrue($mutex1->writeLock());
		$this->assertFalse($mutex2->readLock());
		$this->assertTrue($mutex1->isLocked());
		$this->assertFalse($mutex2->isLocked());
	}

	function testLock_WW(): void
	{
		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');
		$this->assertTrue($mutex1->writeLock());
		$this->assertFalse($mutex2->writeLock());
		$this->assertTrue($mutex1->isLocked());
		$this->assertFalse($mutex2->isLocked());
	}

	function testLock_RW(): void
	{
		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');
		$this->assertTrue($mutex1->readLock());
		$this->assertFalse($mutex2->writeLock());
		$this->assertTrue($mutex1->isLocked());
		$this->assertFalse($mutex2->isLocked());
	}

	function testLock_RR(): void
	{
		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');
		$this->assertTrue($mutex1->readLock());
		$this->assertTrue($mutex2->readLock());
		$this->assertTrue($mutex1->isLocked());
		$this->assertTrue($mutex2->isLocked());
	}

	function testLock_RW_retry(): void
	{

		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');

		$this->assertTrue($mutex1->readLock());
		$this->assertFalse($mutex2->writeLock());

		$this->assertTrue($mutex1->unlock());
		$this->assertTrue($mutex2->writeLock());
	}

	function testLock_RW_relock(): void
	{

		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');

		$this->assertTrue($mutex1->readLock());
		$this->assertTrue($mutex2->readLock());
		$this->assertTrue($mutex1->unlock());
		$this->assertTrue($mutex2->unlock());

		$this->assertTrue($mutex2->writeLock());
		$this->assertTrue($mutex2->writeLock());
		$this->assertTrue($mutex1->unlock());
		$this->assertTrue($mutex2->unlock());
	}

	function testUnlock_RW(): void
	{
		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');

		$this->assertTrue($mutex1->readLock());
		$this->assertTrue($mutex1->isLocked());
		$this->assertTrue($mutex1->unlock());
		$this->assertFalse($mutex1->isLocked());

		$this->assertTrue($mutex2->writeLock());
		$this->assertTrue($mutex2->isLocked());
		$this->assertTrue($mutex2->unlock());
		$this->assertFalse($mutex2->isLocked());
	}

	function testDestructor_RW(): void
	{
		$mutex1 = new FileMutex('key');
		$mutex2 = new FileMutex('key');

		$this->assertTrue($mutex1->readLock());
		$this->assertTrue($mutex1->isLocked());
		$mutex1 = null;

		$this->assertTrue($mutex2->writeLock());
		$this->assertTrue($mutex2->isLocked());
		$mutex2 = null;
	}
}
