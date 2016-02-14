<?php
// config
return $_SERVER['HTTP_HOST'] == 'wiki.dev.xxx.com' ? 
	array(
		//'配置项'=>'配置值'
		'SESSION_PREFIX' => 'wiki.dev.xxx.com',
		'DB_TYPE' => 'mysql', // 数据库类型
		'DB_HOST' => 'localhost',
		'DB_NAME' => '',
		'mg_USER' => '',
		'DB_PWD' => '',
		'DB_PORT' => 3306, // dev端口
		'DB_PREFIX' => 'tb_', // 数据库表前缀
		'DB_CHARSET' => 'utf8', // 字符集
		'DB_DEBUG' => true, // 数据库调试模式 开启后可以记录SQL日志
		'URL_MODEL' => 0, //0普通模式 1Path模式
		'URL_CASE_INSENSITIVE' => false, //url区分大小写
		'URL_HTML_SUFFIX' => '', //url伪静态后缀
		'URL_ROUTER_ON' => false,
		'URL_ROUTE_RULES' => array(
		)
	) : ($_SERVER['HTTP_HOST'] == 'wiki.xxx.com' ? 
		array(
			//'配置项'=>'配置值'
			'SESSION_PREFIX' => 'wiki.xx.com',
			'DB_TYPE' => 'mysql', // 数据库类型
			'DB_HOST' => 'localhost',
			'DB_NAME' => '',
			'DB_USER' => '',
			'DB_PWD' => '',
			'DB_PORT' => 3306, // 线上端口
			'DB_PREFIX' => 'tb_', // 数据库表前缀
			'DB_CHARSET' => 'utf8', // 字符集
			'DB_DEBUG' => true, // 数据库调试模式 开启后可以记录SQL日志
			'URL_MODEL' => 0, //0普通模式 1Path模式
			'URL_CASE_INSENSITIVE' => false, //url区分大小写
			'URL_HTML_SUFFIX' => '', //url伪静态后缀
			'URL_ROUTER_ON' => false,
			'URL_ROUTE_RULES' => array(
			)
		) : array(
			
		));

