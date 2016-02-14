<?php
// config
return $_SERVER['HTTP_HOST'] == 'wiki.dev.xxxx.com' ? array(
		//'配置项'=>'配置值'
		'DB_TYPE' => 'mysql', // 数据库类型
		'DB_HOST' => 'localhost',
		'DB_NAME' => '',
		'DB_USER' => '',
		'DB_PWD' => '',
		'DB_PORT' => 3307, // 端口
		'DB_PREFIX' => 'tb_', // 数据库表前缀
		'DB_CHARSET' => 'utf8', // 字符集
		'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志
		'SESSION_PREFIX' => '',
		'URL_MODEL' => 0,
		'URL_CASE_INSENSITIVE' => TRUE, //url不区分大小写
		"URL_HTML_SUFFIX" => '', //url伪静态后缀
		'URL_ROUTER_ON' => false,
		'URL_ROUTE_RULES' => array(
			':id\d' => 'Home/Item/Show?item_id=:1'
		)
	) : array(
		//'配置项'=>'配置值'
		'DB_TYPE' => 'mysql', // 数据库类型
		'DB_HOST' => 'localhost',
		'DB_NAME' => '',
		'DB_USER' => '',
		'DB_PWD' => '',
		'DB_PORT' => 3306, // 端口
		'DB_PREFIX' => 'tb_', // 数据库表前缀
		'DB_CHARSET' => 'utf8', // 字符集
		'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志
		'SESSION_PREFIX' => '',
		'URL_MODEL' => 0,
		'URL_CASE_INSENSITIVE' => TRUE, //url不区分大小写
		"URL_HTML_SUFFIX" => '', //url伪静态后缀
		'URL_ROUTER_ON' => false,
		'URL_ROUTE_RULES' => array(
			':id\d' => 'Home/Item/Show?item_id=:1'
		)
	);
	