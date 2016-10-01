<?php
return array(
	//定义URL格式
	'URL_MODEL' => 1,

	//开启应用分组
	'APP_GROUP_LIST' => 'Admin,Index,Product,Purchase,Sale,Storage,Ussw,Szsw',
	'DEFAULT_GROUP' => 'Index',

	//定义数据库连接参数
	'DB_HOST' => 'localhost',
	'DB_USER' => 'lipovolt_3s',
	'DB_PWD' => '@ShenYangOber12',
	'DB_NAME' => 'lipovolt_3s',
	'DB_PREFIX' => 'lipovolt_3s_',

	// 开启字段类型验证
	'DB_FIELDTYPE_CHECK'=>True, 

	//点语法默认解析
	'TMPL_VAR_IDENTIFY' => 'array',

	//默认过滤函数
	'DEFAUL_FILTER' => 'htmlspecialchars',

	//自定义SESSION 数据库存储
	'SESSION_TYPE' => 'Db',
	'SESSION_OPTIONS' => array('expire' =>10800),

	// 加载扩展配置文件
	'LOAD_EXT_CONFIG' => 'db,ui,importTemplate', 

	//RBAC配置项
	'RBAC_SUPERADMIN' => 'admin',
	'ADMIN_AUTH_KEY' => 'superadmin',
	'USER_AUTH_ON' => true,
	'USER_AUTH_TYPE' =>1,
	'USER_AUTH_KEY' => 'uid',
	'NOT_AUTH_MODULE' => 'Product,Index,Purchase,Restock,Supplier,GgsUsswSale,SzSale,WinitDeSale,WinitUsSale,Storage,Inbound,Outbound,Postage',
	'NOT_AUTH_ACTION' => 'login,logout',
	'RBAC_ROLE_TABLE' => '3s_role',
	'RBAC_USER_TABLE' => '3s_role_user',
	'RBAC_ACCESS_TABLE' => '3s_access',
	'RBAC_NODE_TABLE' => '3s_node',

);
?>