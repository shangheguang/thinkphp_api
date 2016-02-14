<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
header("content-type:text/html;charset=utf-8");
// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<')) die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
if ($_SERVER['HTTP_HOST'] == 'wiki.dev.xxxx.com') {
	error_reporting(1);
	define('APP_DEBUG', true);
} else if ($_SERVER['HTTP_HOST'] == 'wiki.xxxx.com') {
	error_reporting(0);
	define('APP_DEBUG', false);
} else {
	die('config error!!!');
}

// 定义应用目录
define('APP_PATH', './Application/');

// 引入ThinkPHP入口文件
$lib_tp = dirname(__FILE__) . '/../../lib/ThinkPHP/thinkphp_3.2.3_full/ThinkPHP.php';
require_once($lib_tp);
