<?php
return array(
	//meta_data
	'DB_METADATA' => 'metadata',
	'DB_METADATA_ID' => 'id',
	'DB_METADATA_EURTOUSD' => 'eur_usd',
	'DB_METADATA_USDTORMB' => 'usd_rmb',
	'DB_METADATA_EURTORMB' => 'eur_rmb',
	'DB_METADATA_DEMWST' => 'de_mwst',

	//product
	'DB_PRODUCT' => 'product',
	'DB_PRODUCT_ID' => 'id',
	'DB_PRODUCT_SKU' => 'sku',
	'DB_PRODUCT_CNAME' => 'cname',
	'DB_PRODUCT_ENAME' => 'ename',
	'DB_PRODUCT_PRICE' => 'price',
	'DB_PRODUCT_WEIGHT' => 'weight',
	'DB_PRODUCT_LENGTH' => 'length',
	'DB_PRODUCT_WIDTH' => 'width',
	'DB_PRODUCT_HEIGHT' => 'height',
	'DB_PRODUCT_BATTERY' => 'battery', //不带电，内置电，纯电
	'DB_PRODUCT_TODE' => 'tode', //空运，海运，无
	'DB_PRODUCT_TOUS' => 'tous', //空运，海运，无
	'DB_PRODUCT_DETARIFF' => 'detariff',
	'DB_PRODUCT_USTARIFF' => 'ustariff',
	'DB_PRODUCT_INCOMING_DAY' => 'incoming_day',
	'DB_PRODUCT_MANAGER' => 'manager',
	'DB_PRODUCT_SUPPLIER' => 'supplier',
	'DB_PRODUCT_GGS_USSW_SALE_PRICE' => 'ggs_ussw_sale_price',
	'DB_PRODUCT_RC_WINIT_US_SALE_PRICE' => 'rc_winit_us_sale_price',
	'DB_PRODUCT_RC_WINIT_DE_SALE_PRICE' => 'rc_winit_de_sale_price',
	'DB_PRODUCT_EBAY_COM_BEST_MATCH' => 'ebay_com_best',
	'DB_PRODUCT_EBAY_COM_PRICE_LOWEST' => 'ebay_com_cheapest',
	'DB_PRODUCT_EBAY_DE_BEST_MATCH' => 'ebay_de_best',
	'DB_PRODUCT_EBAY_DE_PRICE_LOWEST' => 'ebay_de_cheapest',

	//session
	'DB_SESSION'=> 'session',
	'DB_SESSION_ID' => 'session_id',
	'DB_SESSION_EXPIRE' =>'session_expire',
	'DB_SESSION_DATA' => 'session_data',

	//user
	'DB_3S_USER' => 'user',
	'DB_3S_USER_ID' => 'id',
	'DB_3S_USER_USERNAME' => 'username',
	'DB_3S_USER_PASSWORD' => 'password',
	'DB_3S_USER_LOGINIP' => 'loginip',
	'DB_3S_USER_LOGINTIME' => 'logintime',
	'DB_3S_USER_LOCK' => 'lock',
	'DB_3s_USER_EMAIL' => 'email',

	//us_inventory
	'DB_US_INVENTORY' => 'us_inventory',
	'DB_US_INVENTORY_ID' => 'us_inventory_id',
	'DB_US_INVENTORY_SKU' => 'us_inventory_sku',
	'DB_US_INVENTORY_POSITION' => 'us_inventory_position',
	'DB_US_INVENTORY_CINVENTORY' => 'us_inventory_cinventory',
	'DB_US_INVENTORY_AINVENTORY' => 'us_inventory_ainventory',
	'DB_US_INVENTORY_OINVENTORY' => 'us_inventory_oinventory',
	'DB_US_INVENTORY_iINVENTORY' => 'us_inventory_iinventory',
	'DB_US_INVENTORY_CSALES' => 'us_inventory_csales',

	//ussw_inbound
	/*创建usswInbound表
    CREATE TABLE IF NOT EXISTS `3s_ussw_inbound`(
    `id` smallint(6) unsigned primary key NOT NULL AUTO_INCREMENT,
    `date` date default null,
    `way` varchar(10) default null,
    `pQuantity` smallint(6) default 0,
    `weight` decimal(5,2) default 0,
    `volume` decimal(8,5) default 0,
    `volumeWeight` decimal(5,2) default 0,
    `iQuantity` smallint(6) default 0,
    `status` varchar(10) default null
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;        
    */
	'DB_USSW_INBOUND' => 'ussw_inbound',
	'DB_USSW_INBOUND_ID' => 'id',
	'DB_USSW_INBOUND_DATE' => 'date',
	'DB_USSW_INBOUND_SHIPPING_WAY' => 'shipping_way',  //空运，海运
	'DB_USSW_INBOUND_STATUS' => 'status', //产品已导入，装箱已导入，待入库，已入库

	//ussw_inbound_package
	/*
	CREATE TABLE IF NOT EXISTS `3s_ussw_inbound_package` (
	`id` smallint(6) unsigned primary key NOT NULL AUTO_INCREMENT,
	`inbound_id` smallint(6) default 0,
	`package_number` varchar(10) default null,
	`confirme` tinyint(1) default 0,
	`weight` decimal(10,2) default 0,
	`length` decimal(10,2) default 0,
	`width`  decimal(10,2) default 0,
	`height`  decimal(10,2) default 0
	)ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_USSW_INBOUND_PACKAGE' => 'ussw_inbound_package',
	'DB_USSW_INBOUND_PACKAGE_ID' => 'id',
	'DB_USSW_INBOUND_PACKAGE_IOID' => 'inbound_id',
	'DB_USSW_INBOUND_PACKAGE_NUMBER' => 'package_number',
	'DB_USSW_INBOUND_PACKAGE_CONFIRM' => 'confirme',
	'DB_USSW_INBOUND_PACKAGE_WEIGHT' => 'weight',
	'DB_USSW_INBOUND_PACKAGE_LENGTH' => 'length',
	'DB_USSW_INBOUND_PACKAGE_WIDTH' => 'width',
	'DB_USSW_INBOUND_PACKAGE_HEIGHT' => 'height',
	

	//ussw_inbound_item
	/*创建美国自建仓入库产品明细表
    create table if not exists `3s_ussw_inbound_item` (
    `id` smallint(6) unsigned primary key not null auto_increment,
    `inbound_id` smallint(6),
    `package_number` varchar(10) default null,
    `restock_id` smallint(6),
    `sku` varchar(10),
    `declare_quantity` smallint(6),
    `confirmed_quantity` smallint(6)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_USSW_INBOUND_ITEM' => 'ussw_inbound_item',
	'DB_USSW_INBOUND_ITEM_ID' => 'id',
	'DB_USSW_INBOUND_ITEM_IOID' => 'inbound_id',
	'DB_USSW_INBOUND_ITEM_PACKAGE_NUMBER' => 'package_number',
	'DB_USSW_INBOUND_ITEM_RESTOCK_ID' => 'restock_id',
	'DB_USSW_INBOUND_ITEM_SKU' => 'sku',
	'DB_USSW_INBOUND_ITEM_DQUANTITY' => 'declare_quantity',
	'DB_USSW_INBOUND_ITEM_CQUANTITY' => 'confirmed_quantity',

	//ussw_outbound
	/*创建美国仓出库表
	create table if not exists `3s_ussw_outbound`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`market` varchar(10) default null,
	`market_no` varchar(20) default null,
	`status` varchar(10) default null,
	`shipping_company` varchar(20),
	`shipping_way` varchar(30) default null,
	`tracking_number` varchar(30) default null,
	`create_time` datetime,
	`seller_id` varchar(20) default null,
	`buyer_id` varchar(20) default null,
	`buyer_name` varchar(30) default null,
	`buyer_tel` varchar(20) default null,
	`buyer_email` varchar(30) default null,
	`buyer_address1` varchar(50) default null,
	`buyer_address2` varchar(50) default null,
	`buyer_city` varchar(30) default null,
	`buyer_state` varchar(30) default null,
	`buyer_country` varchar(30) default null,
	`buyer_zip` varchar(20) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_OUTBOUND' => 'ussw_outbound',
	'DB_USSW_OUTBOUND_ID' => 'id',
	'DB_USSW_OUTBOUND_MARKET' => 'market',
	'DB_USSW_OUTBOUND_MARKET_NO' => 'market_no',
	'DB_USSW_OUTBOUND_STATUS' => 'status', //待出库，已出库
	'DB_USSW_OUTBOUND_SHIPPING_COMPANY' => 'shipping_company',
	'DB_USSW_OUTBOUND_SHIPPING_WAY' => 'shipping_way',
	'DB_USSW_OUTBOUND_TRACKING_NUMBER' => 'tracking_number',
	'DB_USSW_OUTBOUND_CREATE_TIME' => 'create_time',
	'DB_USSW_OUTBOUND_SELLER_ID' => 'seller_id',
	'DB_USSW_OUTBOUND_BUYER_ID' => 'buyer_id',
	'DB_USSW_OUTBOUND_BUYER_NAME' => 'buyer_name',
	'DB_USSW_OUTBOUND_BUYER_TEL' => 'buyer_tel',
	'DB_USSW_OUTBOUND_BUYER_EMAIL' => 'buyer_email',
	'DB_USSW_OUTBOUND_BUYER_ADDRESS1' => 'buyer_address1',
	'DB_USSW_OUTBOUND_BUYER_ADDRESS2' => 'buyer_address2',
	'DB_USSW_OUTBOUND_BUYER_CITY' => 'buyer_city',
	'DB_USSW_OUTBOUND_BUYER_STATE' => 'buyer_state',
	'DB_USSW_OUTBOUND_BUYER_COUNTRY' => 'buyer_country',
	'DB_USSW_OUTBOUND_BUYER_ZIP' => 'buyer_ZIP',

	//ussw_outbound_item
	/*
	创建美国出库单产品明细表
	create table if not exists `3s_ussw_outbound_item`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`outbound_id` smallint(6),
	`sku` varchar(10) default null,
	`position` varchar(10) default null,
	`quantity` smallint(3) default 0,
	`market_no` varchar(20) default null,
	`transaction_no` varchar(20) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_OUTBOUND_ITEM' => 'ussw_outbound_item',
	'DB_USSW_OUTBOUND_ITEM_ID' => 'id',
	'DB_USSW_OUTBOUND_ITEM_OOID' => 'outbound_id',
	'DB_USSW_OUTBOUND_ITEM_SKU' => 'sku',
	'DB_USSW_OUTBOUND_ITEM_POSITION' => 'position',
	'DB_USSW_OUTBOUND_ITEM_QUANTITY' => 'quantity',
	'DB_USSW_OUTBOUND_ITEM_MARKET_NO' => 'market_no',
	'DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO' => 'transaction_no',

	//usstorage
	/*
	CREATE TABLE IF NOT EXISTS `3s_usstorage` (
	  `id` smallint(6) unsigned primary key NOT NULL,
	  `position` varchar(10) NOT NULL,
	  `sku` varchar(10) NOT NULL,
	  `cname` varchar(255) DEFAULT NULL,
	  `ename` varchar(255) DEFAULT NULL,
	  `attribute` varchar(50) DEFAULT NULL,
	  `cinventory` smallint(6) DEFAULT 0,
	  `ainventory` smallint(6) DEFAULT 0,
	  `oinventory` smallint(6) DEFAULT 0,
	  `iinventory` smallint(6) DEFAULT 0,
	  `csales` smallint(6) DEFAULT 0,
	  `remark` varchar(255) DEFAULT NULL
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_USSTORAGE' => 'usstorage',
	'DB_USSTORAGE_ID' => 'id',
	'DB_USSTORAGE_POSITION' => 'position',
	'DB_USSTORAGE_SKU' => 'sku',
	'DB_USSTORAGE_CNAME' => 'cname',
	'DB_USSTORAGE_ENAME' => 'ename',
	'DB_USSTORAGE_ATTRIBUTE' => 'attribute',
	'DB_USSTORAGE_CINVENTORY' => 'cinventory',
	'DB_USSTORAGE_AINVENTORY' => 'ainventory',
	'DB_USSTORAGE_OINVENTORY' => 'oinventory',
	'DB_USSTORAGE_IINVENTORY' => 'iinventory',
	'DB_USSTORAGE_CSALES' => 'csales',
	'DB_USSTORAGE_REMARK' => 'remark',


	//restock
	/*创建补货表
	CREATE TABLE IF NOT EXISTS `3s_restock` (
	  `id` smallint(6) unsigned primary key auto_increment,
	  `create_date` datetime default NULL,
	  `shipping_date` datetime default NULL,
	  `manager` varchar(20) default null,
	  `sku` varchar(10) NOT NULL,
	  `quantity` smallint(6) DEFAULT 0,
	  `warehouse` varchar(10) DEFAULT null,
	  `transport` varchar(10) DEFAULT null,
	  `status` varchar(10) DEFAULT null,
	  `remark` varchar(255) DEFAULT NULL
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_RESTOCK' => 'restock',
	'DB_RESTOCK_ID' => 'id',
	'DB_RESTOCK_CREATE_DATE' => 'create_date',
	'DB_RESTOCK_SHIPPING_DATE' => 'shipping_date',
	'DB_RESTOCK_MANAGER' => 'manager',
	'DB_RESTOCK_SKU' => 'sku',
	'DB_RESTOCK_QUANTITY' => 'quantity',
	'DB_RESTOCK_WAREHOUSE' => 'warehouse', //美自建仓,万邑通美西，万邑通德国
	'DB_RESTOCK_TRANSPORT' => 'transport', //空运，海运
	'DB_RESTOCK_STATUS' => 'status', //待发货，已发货
	'DB_RESTOCK_REMARK' => 'remark',



	//purchase
	/*创建采购表
	create table if not exists `3s_purchase`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`manager` varchar(20) default null,
	`create_date` datetime,
	`purchase_date` datetime,
	`shipping_fee` decimal(6,2),
	`status` varchar(20) default null,
	`cancel` tinyint(1) default 0,
	`order_number` varchar(20) default null,
	`tracking_number` varchar(20) default null,
	`supplier_id` smallint(6) default null,
	`remark` varchar(255) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_PURCHASE' => 'purchase',
	'DB_PURCHASE_ID' => 'id',
	'DB_PURCHASE_MANAGER' => 'manager',
	'DB_PURCHASE_CREATE_DATE' => 'create_date',
	'DB_PURCHASE_PURCHASED_DATE' => 'purchase_date',
	'DB_PURCHASE_SHIPPING_FEE' => 'shipping_fee',
	'DB_PURCHASE_STATUS' => 'status',//待确认, 待付款, 待发货, 部分到货, 全部到货
	'DB_PURCHASE_CANCEL' => 'cancel',
	'DB_PURCHASE_ORDER_NUMBER' => 'order_number',//purchase order number
	'DB_PURCHASE_TRACKING_NUMBER' => 'tracking_number',	
	'DB_PURCHASE_SUPPLIER_ID' => 'supplier_id',	
	'DB_PURCHASE_REMARK' => 'remark',

	
	//purchase_item
	/*创建补货产品表
	create table if not exists `3s_purchase_item`(
	`purchase_item_id` smallint(6) unsigned primary key not null auto_increment,
	`purchase_id` smallint(6) default null,
	`sku` varchar(10) default null,
	`price` decimal(6,2) default 0,
	`purchase_quantity` smallint(6) default 0,
	`received_quantity` smallint(6) default 0,
	`warehouse` varchar(20) default null,
	`transport_method` varchar(10) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_PURCHASE_ITEM' => 'purchase_item',
	'DB_PURCHASE_ITEM_ID' => 'purchase_item_id',
	'DB_PURCHASE_ITEM_PURCHASE_ID' => 'purchase_id',
	'DB_PURCHASE_ITEM_SKU' => 'sku',
	'DB_PURCHASE_ITEM_PRICE' => 'price',
	'DB_PURCHASE_ITEM_PURCHASE_QUANTITY' => 'purchase_quantity',
	'DB_PURCHASE_ITEM_RECEIVED_QUANTITY' => 'received_quantity',
	'DB_PURCHASE_ITEM_WAREHOUSE' => 'warehouse',  //美自建仓,万邑通美西，万邑通德国
	'DB_PURCHASE_ITEM_TRANSPORT_METHOD' => 'transport_method', //空运，海运


	//supplier
	/*创建补货产品表
	create table if not exists `3s_supplier`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`company` varchar(50) default null,
	`person` varchar(50) default null,
	`wangwang` varchar(20) default null,
	`qq` varchar(20) default null,
	`website` varchar(255) default null,
	`tel` varchar(20) default null,
	`address` varchar(50) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_SUPPLIER' => 'supplier',
	'DB_SUPPLIER_ID' => 'id',
	'DB_SUPPLIER_COMPANY' => 'company',
	'DB_SUPPLIER_PERSON' =>'person',
	'DB_SUPPLIER_WANGWANG' => 'wangwang',
	'DB_SUPPLIER_QQ' => 'qq',
	'DB_SUPPLIER_WEBSITE' => 'website',
	'DB_SUPPLIER_TEL' => 'tel',
	'DB_SUPPLIER_ADDRESS' => 'address',

	);
?>