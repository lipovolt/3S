<?php
return array(
	//定义URL格式
	'URL_MODEL' => 1,

	//开启调试模式
	'APP_DEBUG' => True,

	//开启应用分组
	'APP_GROUP_LIST' => 'Index,Product,Purchase,Sale,Storage,Ussw',
	'DEFAULT_GROUP' => 'Index',

	//定义数据库连接参数
	'DB_HOST' => '127.0.0.1',
	'DB_USER' => 'root',
	'DB_PWD' => '',
	'DB_NAME' => '3S',
	'DB_PREFIX' => '3S_',
	/*'DB_HOST' => 'host386.hostmonster.com',
	'DB_USER' => 'lipovolt_3s',
	'DB_PWD' => '@ShenYangOber12',
	'DB_NAME' => 'lipovolt_3s',
	'DB_PREFIX' => 'lipovolt_3s_',*/

	// 开启字段类型验证
	'DB_FIELDTYPE_CHECK'=>True, 

	//点语法默认解析
	'TMPL_VAR_IDENTIFY' => 'array',

	//默认过滤函数
	'DEFAUL_FILTER' => 'htmlspecialchars',

	//自定义SESSION 数据库存储
	'SESSION_TYPE' => 'Db',

	// 加载扩展配置文件
	'LOAD_EXT_CONFIG' => 'db,ui,importTemplate', 

);
?>