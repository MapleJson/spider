<?php

declare (strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use app\book\BookSpider;

// 检查扩展
if (!extension_loaded('pcntl')) {
    exit("Please install pcntl extension. See http://doc3.workerman.net/install/install.html\n");
}
if (!extension_loaded('posix')) {
    exit("Please install posix extension. See http://doc3.workerman.net/install/install.html\n");
}
// 标记是全局启动
define('GLOBAL_START', 1);

// 加载所有start.php，以便启动所有服务
new BookSpider();
// 运行所有服务
Worker::runAll();