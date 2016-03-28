<?php
return array(
	//定义URL格式
	'URL_NODEL' => 2,

	//开启调试模式
	'APP_DEBUG' => True,

	//开启应用分组
	'APP_GROUP_LIST' => 'Index,Admin,Product,Storage,Ussw,Winit',
	'DEFAULT_GROUP' => 'Index',

	//定义数据库连接参数
	'DB_HOST' => '127.0.0.1',
	'DB_USER' => 'root',
	'DB_PWD' => '',
	'DB_NAME' => '3S',
	'DB_PREFIX' => '3S_',

	//点语法默认解析
	//'TMPL_VAR_IDENTIFY' => 'array',

	//默认过滤函数
	'DEFAUL_FILTER' => 'htmlspecialchars',

	//自定义SESSION 数据库存储
	'SESSION_TYPE' => 'Db',
);
?>