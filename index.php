<?php
// 定义应用目录
define('APP_PATH', __DIR__ . '/apps/');

// 应用日志目录
define ( 'LOG_PATH', __DIR__ . '/logs/' );

// 缓存目录
define ( 'RUNTIME_PATH', __DIR__ . '/runtime/' );
define ( 'CACHE_PATH', RUNTIME_PATH . '/cache/' );
define ( 'TEMP_PATH', RUNTIME_PATH . '/temp/' );

// 第三方类库目录
define ( 'VENDOR_PATH', __DIR__ . '/vendor/' );

// 加载框架引导文件
require __DIR__ . '/core/start.php';
