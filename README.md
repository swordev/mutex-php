# Mutex
> Read-write concurrency control

## Installation

```sh
composer require @swordev/mutex
```

## Usage

### File read/write lock

```php
use Swordev\Mutex\FileMutex;

$mutex1 = new FileMutex('key');
$mutex2 = new FileMutex('key');

$mutex1->readLock(); // true
$mutex2->writeLock(); // false

$mutex1->unlock(); // true
$mutex2->writeLock(); // true
```

### Timeout

```php
use Swordev\Mutex\FileMutex;

$mutex = new FileMutex('key');
$mutex->writeLock(5000);
```

### Contextual lock

```php
use Swordev\Mutex\FileMutex;

class Foo {
	function method() {
		$mutex = new FileMutex(__CLASS__ . '|' . __FUNCTION__);
		$mutex->writeLock();
		// ...
	}
}
```

### Mutex factory

```php
use Swordev\Mutex\MutexFactory;

$mutex = new MutexFactory::create(FileMutex::class, 'key');
```

## Development

### Test

```sh
composer run test
```

### Analyse

```sh
composer run analyse
```

## Author

Juanra GM - https://github.com/juanrgm

Distributed under the MIT license.

[https://github.com/swordev/mutex-php](https://github.com/swordev/mutex-php)
