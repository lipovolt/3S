<?php
return array(
	//meta_data
	/*创建meta_data表
    CREATE TABLE IF NOT EXISTS `3s_metadata`(
    `id` smallint(6) unsigned primary key NOT NULL AUTO_INCREMENT,
    `eur_usd` decimal(5,3) default null,
    `usd_rmb` decimal(5,3) default null,
    `eur_rmb` decimal(5,3) default null,
    `de_mwst` int(5) default null,
    `used_upc` bigint default null
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;        
    */
	'DB_METADATA' => 'metadata',
	'DB_METADATA_ID' => 'id',
	'DB_METADATA_EURTOUSD' => 'eur_usd',
	'DB_METADATA_USDTORMB' => 'usd_rmb',
	'DB_METADATA_EURTORMB' => 'eur_rmb',
	'DB_METADATA_DEMWST' => 'de_mwst',
	'DB_METADATA_USED_UPC' => 'used_upc',

	//product
	/*创建product表
    CREATE TABLE IF NOT EXISTS `3s_product`(
    `id` smallint(6) unsigned primary key NOT NULL AUTO_INCREMENT,
    `sku` varchar(10) Not null,
    `upc` bigint default null,
    `cname` varchar(255) default null,
    `ename` varchar(255) default null,
    `price` decimal(5,2) default 0,
    `weight` smallint(6) default 0,
    `length` decimal(5,2) default 0,
    `width` decimal(5,2) default 0,
    `height` decimal(5,2) default 0,
    `pweight` smallint(6) default 0,
    `plength` decimal(5,2) default 0,
    `pwidth` decimal(5,2) default 0,
    `pheight` decimal(5,2) default 0,
    `premark` varchar(255) default null,
    `battery` varchar(10) default null,
    `tode` varchar(10) default null,
    `tous` varchar(10) default null,
    `detariff` decimal(4,2) default 5,
    `ustariff` decimal(4,2) default 5,
    `incoming_day` smallint(3) default 0,
    `manager` varchar(20) default null,
    `supplier` varchar(255) default null,
    `ggs_ussw_sale_price` decimal(6,2) default 0,
    `rc_winit_us_sale_price` decimal(6,2) default 0,
    `rc_winit_de_sale_price` decimal(6,2) default 0,
    `amazon_ussw_sale_price` decimal(6,2) default 0,
    `sz_us_sale_price` decimal(6,2) default 0,
    `sz_de_sale_price` decimal(6,2) default 0,
    `ebay_com_best` varchar(255) default null,
    `ebay_com_cheapest` varchar(255) default null,
    `ebay_de_best` varchar(255) default null,
    `ebay_de_cheapest` varchar(255) default null,
    `purchase_link` varchar(255) default null
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;        
    */
	'DB_PRODUCT' => 'product',
	'DB_PRODUCT_ID' => 'id',
	'DB_PRODUCT_SKU' => 'sku',
	'DB_PRODUCT_UPC' => 'upc',
	'DB_PRODUCT_CNAME' => 'cname',
	'DB_PRODUCT_ENAME' => 'ename',
	'DB_PRODUCT_PRICE' => 'price',
	'DB_PRODUCT_WEIGHT' => 'weight',
	'DB_PRODUCT_LENGTH' => 'length',
	'DB_PRODUCT_WIDTH' => 'width',
	'DB_PRODUCT_HEIGHT' => 'height',
	'DB_PRODUCT_PWEIGHT' => 'pweight',
	'DB_PRODUCT_PLENGTH' => 'plength',
	'DB_PRODUCT_PWIDTH' => 'pwidth',
	'DB_PRODUCT_PHEIGHT' => 'pheight',
	'DB_PRODUCT_PREMARK' => 'premark',
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
	'DB_PRODUCT_AMAZON_USSW_SALE_PRICE' => 'amazon_ussw_sale_price',
	'DB_PRODUCT_SZ_US_SALE_PRICE' => 'sz_us_sale_price',
	'DB_PRODUCT_SZ_DE_SALE_PRICE' => 'sz_de_sale_price',
	'DB_PRODUCT_EBAY_COM_BEST_MATCH' => 'ebay_com_best',
	'DB_PRODUCT_EBAY_COM_PRICE_LOWEST' => 'ebay_com_cheapest',
	'DB_PRODUCT_EBAY_DE_BEST_MATCH' => 'ebay_de_best',
	'DB_PRODUCT_EBAY_DE_PRICE_LOWEST' => 'ebay_de_cheapest',
	'DB_PRODUCT_PURCHASE_LINK' => 'purchase_link',

	//session
	/*创建session表
    CREATE TABLE `3s_session` (
    session_id varchar(255) NOT NULL,
    session_expire int(11) NOT NULL,
    session_data blob,
    UNIQUE KEY `session_id` (`session_id`)
    );        
    */
	'DB_SESSION'=> 'session',
	'DB_SESSION_ID' => 'session_id',
	'DB_SESSION_EXPIRE' =>'session_expire',
	'DB_SESSION_DATA' => 'session_data',

	//user
	/*创建user表
	    CREATE TABLE IF NOT EXISTS `3s_user`(
	    `id` int(10) unsigned primary key NOT NULL AUTO_INCREMENT,
	    `username` char(20) Not null default '',
	    `password` char(32) Not null default '',
	    `loginip` varchar(30) not null,
	    `logintime` int(10) unsigned not null,
	    `lock` tinyint(1) default 0,
	    `email` varchar(30) default null
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;        
    */
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
	'DB_US_INVENTORY_ID' => 'id',
	'DB_US_INVENTORY_SKU' => 'sku',
	'DB_US_INVENTORY_POSITION' => 'position',
	'DB_US_INVENTORY_CINVENTORY' => 'cinventory',
	'DB_US_INVENTORY_AINVENTORY' => 'ainventory',
	'DB_US_INVENTORY_OINVENTORY' => 'oinventory',
	'DB_US_INVENTORY_iINVENTORY' => 'iinventory',
	'DB_US_INVENTORY_CSALES' => 'csales',

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
	'DB_USSW_OUTBOUND_BUYER_ZIP' => 'buyer_zip',

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
	  `id` smallint(6) unsigned primary key NOT NULL auto_increment,
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
	  `remark` varchar(255) DEFAULT NULL,
	  `sale_status` varchar(10) DEFAULT NULL
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
	'DB_USSTORAGE_SALE_STATUS' => 'sale_status', //待下架，已下架, Null


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
	'DB_RESTOCK_STATUS' => 'status', //待发货，部分发货，已发货
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
	'DB_PURCHASE_ITEM_WAREHOUSE' => 'warehouse',  //美自建仓,万邑通美西，万邑通德国,深圳仓
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


	//szstorage
	/*
	CREATE TABLE IF NOT EXISTS `3s_szstorage` (
	  `id` smallint(6) unsigned primary key NOT NULL auto_increment,
	  `position` varchar(10) NOT NULL,
	  `sku` varchar(10) NOT NULL,
	  `cinventory` smallint(6) DEFAULT 0,
	  `ainventory` smallint(6) DEFAULT 0,
	  `csales` smallint(6) DEFAULT 0,
	  `remark` varchar(255) DEFAULT NULL
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_SZSTORAGE' => 'szstorage',
	'DB_SZSTORAGE_ID' => 'id',
	'DB_SZSTORAGE_POSITION' => 'position',
	'DB_SZSTORAGE_SKU' => 'sku',
	'DB_SZSTORAGE_CINVENTORY' => 'cinventory',
	'DB_SZSTORAGE_AINVENTORY' => 'ainventory',
	'DB_SZSTORAGE_CSALES' => 'csales',
	'DB_SZSTORAGE_REMARK' => 'remark',


	//sz_outbound
	/*创建深圳仓出库表
	create table if not exists `3s_sz_outbound`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`market` varchar(20) default null,
	`market_no` varchar(20) default null,
	`status` varchar(10) default null,
	`shipping_company` varchar(20) default null,
	`shipping_way` varchar(30) default null,
	`tracking_number` varchar(30) default null,
	`create_time` timestamp default CURRENT_TIMESTAMP,
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
	'DB_SZ_OUTBOUND' => 'sz_outbound',
	'DB_SZ_OUTBOUND_ID' => 'id',
	'DB_SZ_OUTBOUND_MARKET' => 'market',
	'DB_SZ_OUTBOUND_MARKET_NO' => 'market_no',
	'DB_SZ_OUTBOUND_STATUS' => 'status', //待出库，已出库
	'DB_SZ_OUTBOUND_SHIPPING_COMPANY' => 'shipping_company',
	'DB_SZ_OUTBOUND_SHIPPING_WAY' => 'shipping_way',
	'DB_SZ_OUTBOUND_TRACKING_NUMBER' => 'tracking_number',
	'DB_SZ_OUTBOUND_CREATE_TIME' => 'create_time',
	'DB_SZ_OUTBOUND_SELLER_ID' => 'seller_id',
	'DB_SZ_OUTBOUND_BUYER_ID' => 'buyer_id',
	'DB_SZ_OUTBOUND_BUYER_NAME' => 'buyer_name',
	'DB_SZ_OUTBOUND_BUYER_TEL' => 'buyer_tel',
	'DB_SZ_OUTBOUND_BUYER_EMAIL' => 'buyer_email',
	'DB_SZ_OUTBOUND_BUYER_ADDRESS1' => 'buyer_address1',
	'DB_SZ_OUTBOUND_BUYER_ADDRESS2' => 'buyer_address2',
	'DB_SZ_OUTBOUND_BUYER_CITY' => 'buyer_city',
	'DB_SZ_OUTBOUND_BUYER_STATE' => 'buyer_state',
	'DB_SZ_OUTBOUND_BUYER_COUNTRY' => 'buyer_country',
	'DB_SZ_OUTBOUND_BUYER_ZIP' => 'buyer_zip',


	//sz_outbound_item
	/*
	创建美国出库单产品明细表
	create table if not exists `3s_sz_outbound_item`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`outbound_id` smallint(6) not null,
	`sku` varchar(10) default null,
	`position` varchar(10) default null,
	`quantity` smallint(3) default 0,
	`market_no` varchar(20) default null,
	`transaction_no` varchar(20) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_SZ_OUTBOUND_ITEM' => 'sz_outbound_item',
	'DB_SZ_OUTBOUND_ITEM_ID' => 'id',
	'DB_SZ_OUTBOUND_ITEM_OOID' => 'outbound_id',
	'DB_SZ_OUTBOUND_ITEM_SKU' => 'sku',
	'DB_SZ_OUTBOUND_ITEM_POSITION' => 'position',
	'DB_SZ_OUTBOUND_ITEM_QUANTITY' => 'quantity',
	'DB_SZ_OUTBOUND_ITEM_MARKET_NO' => 'market_no',
	'DB_SZ_OUTBOUND_ITEM_TRANSACTION_NO' => 'transaction_no',


	/*
	sz_postage_eub
	CREATE TABLE IF NOT EXISTS `3s_sz_postage_eub` (
	  `id` smallint(3) unsigned primary key not null auto_increment,
	  `country` varchar(20) default null,
	  `register` decimal(5,2) default 0,
	  `fee` decimal(5,2) DEFAULT 0
	) engine=myisam default charset=utf8;
	*/
	'DB_SZ_POSTAGE_EUB' => 'sz_postage_eub',
	'DB_SZ_POSTAGE_EUB_ID' => 'id',
	'DB_SZ_POSTAGE_EUB_COUNTRY' => 'country',
	'DB_SZ_POSTAGE_EUB_REGISTER' => 'register',
	'DB_SZ_POSTAGE_EUB_FEE' =>'fee',


	/*
	sz_postage_china_post_class
	CREATE TABLE IF NOT EXISTS `3s_sz_postage_cpc` (
	  `id` smallint(6) unsigned primary key not null auto_increment,
	  `country` varchar(20) default null,
	  `class` smallint(3) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_SZ_POSTAGE_CPC' => 'sz_postage_cpc',
	'DB_SZ_POSTAGE_CPC_ID' => 'id',
	'DB_SZ_POSTAGE_CPC_COUNTRY' => 'country',
	'DB_SZ_POSTAGE_CPC_CLASS' => 'class',


	/*
	sz_postage_china_post_fee
	CREATE TABLE IF NOT EXISTS `3s_sz_postage_cpf` (
	  `id` smallint(3) unsigned primary key not null auto_increment,
	  `class_id` smallint(3) default null,
	  `register` decimal(5,2) default 0,
	  `fee` decimal(5,2) DEFAULT 0
	) engine=myisam default charset=utf8;
	*/
	'DB_SZ_POSTAGE_CPF' => 'sz_postage_cpf',
	'DB_SZ_POSTAGE_CPF_ID' => 'id',
	'DB_SZ_POSTAGE_CPF_CLASS_ID' => 'class_id',
	'DB_SZ_POSTAGE_CPF_REGISTER' => 'register',
	'DB_SZ_POSTAGE_CPF_FEE' =>'fee',



	//ussw_sale_plan
	/*
	create table if not exists `3s_ussw_sale_plan`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1
	) engine=myisam default charset=utf8;	
	*/
	'DB_USSW_SALE_PLAN' => 'ussw_sale_plan',
	'DB_USSW_SALE_PLAN_ID' => 'id',
	'DB_USSW_SALE_PLAN_SKU' => 'sku',
	'DB_USSW_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_USSW_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_USSW_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_USSW_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_USSW_SALE_PLAN_COST' => 'cost',
	'DB_USSW_SALE_PLAN_PRICE' => 'sale_price',
	'DB_USSW_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_USSW_SALE_PLAN_SUGGEST' => 'suggest', //clear,relisting,price_up, ,price_down,complete_product_info,complete_sale_info,null
	'DB_USSW_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close

	'USSW_SALE_PLAN_COMPLETE_PRODUCT_INFO' => '完善产品信息',
	'USSW_SALE_PLAN_COMPLETE_SALE_INFO' => '完善自建仓销售信息',
	'USSW_SALE_PLAN_CLEAR' => '清货',
	'USSW_SALE_PLAN_RELISTING' => '重新刊登',
	'USSW_SALE_PLAN_PRICE_UP' => '涨价',
	'USSW_SALE_PLAN_PRICE_DOWN' => '降价',
	'USSW_SALE_PLAN_NONE' => '无',


	/*
	create table if not exists `3s_ussw_sale_plan_metadata`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`clear_nod` smallint(3) not null,
	`relisting_nod` smallint(3) not null,
	`adjust_period` smallint(3) not null,
	`spr1` smallint(3) not null,
	`spr2` smallint(3) not null,
	`spr3` smallint(3) not null,
	`spr4` smallint(3) not null,
	`spr5` smallint(3) not null,
	`pcr` smallint(3) not null,
	`sqnr` smallint(3) not null,
	`denominator` smallint(3) not null,
	`grfr` smallint(3) not null,
	`standard_period` smallint(3) not null
	) engine=myisam default charset=utf8;

	


	Paramters
		1. asq: actual adjust period sale quantity
		2. lsq: last adjust period sale quantity
		3. clear_nod: number of days, if the sale quantity of the clear_nod==0 then clear
		4. relisting_nod: number of days, if the sale quantity of the relisting_nod==0 then relisting
		5. adjust_period: number of days
		6. spr1, spr2, spr3, spr4, spr5: item cost classify. To define different start and floor sale price for different item. The cheap item can be sold with spr1 profit rate. The expensivest item must be sold with spr5 profit rate.
		7. pcr: price change rate, define the proce change rate of each adjust.
		8. sqnr: smallest sale quantity need to be replaced with a determine denominator. For example the lsq=1 and the asp=2. The growth rate is (asp-lsp)/lsp equal 100%. In this situation, the growth rate is high. But the price needn't to be adjusted. So we need to define a sqnr. For example, if the lsq<5, the growth rate should be (asp-lsp)/denominator.
		9. denominator: see the 8.
		10. grfr: growth rate fluctuation range. growth rate >grfr then increse price, growth rate <-grfr then reduct price.
		11. standard_period: define the standard period, the sale quantity of adjust period must be changed to the sale quantity of standard period. Then classify the sale quantity of standard period to decide the grfr.

	Algorithm
	foreach item in usstorage
		if sale qauantty of clear_nod == 0 then clear
		if sale qauantty of relisting_nod == 0 then relisting
		if  last_modify_date < actual_date - adjust_period 
			if (actual_standard_period_sale_quantity - last_standard_period_sale_quantity) / last_standard_period_sale_quantity > grfr then sale_price=sale-price + sale_price*pcr.
			if (actual_standard_period_sale_quantity - last_standard_period_sale_quantity) / last_standard_period_sale_quantity < -grfr then sale_price=sale-price - sale_price*pcr.

	*/
	'DB_USSW_SALE_PLAN_METADATA' => 'ussw_sale_plan_metadata',
	'DB_USSW_SALE_PLAN_METADATA_ID' => 'id',
	'DB_USSW_SALE_PLAN_METADATA_CLEAR_NOD' =>'clear_nod', // number of day
	'DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD' =>'relisting_nod', //number of day
	'DB_USSW_SALE_PLAN_METADATA_ADJUST_PERIOD' =>'adjust_period', //number of day
	'DB_USSW_SALE_PLAN_METADATA_SPR1' =>'spr1',//start_profit_rate for cost under 10 USD
	'DB_USSW_SALE_PLAN_METADATA_SPR2' =>'spr2',//start_profit_rate for cost over 10 under 20 USD
	'DB_USSW_SALE_PLAN_METADATA_SPR3' =>'spr3',//start_profit_rate for cost over 20 under 30 USD
	'DB_USSW_SALE_PLAN_METADATA_SPR4' =>'spr4',//start_profit_rate for cost over 30 under 50 USD
	'DB_USSW_SALE_PLAN_METADATA_SPR5' =>'spr5',//start_profit_rate for cost over 50 USD
	'DB_USSW_SALE_PLAN_METADATA_PCR' =>'pcr',//price_change_rate
	'DB_USSW_SALE_PLAN_METADATA_SQNR' =>'sqnr',//smallest sale quantity need to be replaced with denominator
	'DB_USSW_SALE_PLAN_METADATA_DENOMINATOR' =>'denominator',//denominator to avoid small sale quantity with higher growth rate.
	'DB_USSW_SALE_PLAN_METADATA_GRFR' =>'grfr',//growth rate fluctuation range
	'DB_USSW_SALE_PLAN_METADATA_STANDARD_PERIOD' =>'standard_period',//the sale quantity of adjust period should be change to sale quantity of standard period


	//sz_sale_plan
	/*
	create table if not exists `3s_sz_us_sale_plan`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1
	) engine=myisam default charset=utf8;	
	*/
	'DB_SZ_US_SALE_PLAN' => 'sz_us_sale_plan',
	'DB_SZ_US_SALE_PLAN_ID' => 'id',
	'DB_SZ_US_SALE_PLAN_SKU' => 'sku',
	'DB_SZ_US_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_SZ_US_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_SZ_US_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_SZ_US_SALE_PLAN_COST' => 'cost',
	'DB_SZ_US_SALE_PLAN_PRICE' => 'sale_price',
	'DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_SZ_US_SALE_PLAN_SUGGEST' => 'suggest', //clear,us_relisting,us_price_up, ,us_price_down,de_relisting,de_price_up, ,de_price_down,complete_product_info,complete_sale_info,null
	'DB_SZ_US_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close


	//sz_sale_plan
	/*
	create table if not exists `3s_sz_de_sale_plan`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1
	) engine=myisam default charset=utf8;	
	*/
	'DB_SZ_DE_SALE_PLAN' => 'sz_de_sale_plan',
	'DB_SZ_DE_SALE_PLAN_ID' => 'id',
	'DB_SZ_DE_SALE_PLAN_SKU' => 'sku',
	'DB_SZ_DE_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_SZ_DE_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_SZ_DE_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_SZ_DE_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_SZ_DE_SALE_PLAN_COST' => 'cost',
	'DB_SZ_DE_SALE_PLAN_PRICE' => 'sale_price',
	'DB_SZ_DE_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_SZ_DE_SALE_PLAN_SUGGEST' => 'suggest', //clear,us_relisting,us_price_up, ,us_price_down,de_relisting,de_price_up, ,de_price_down,complete_product_info,complete_sale_info,null
	'DB_SZ_DE_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close



	'SZ_SALE_PLAN_COMPLETE_PRODUCT_INFO' => '完善产品信息',
	'SZ_SALE_PLAN_COMPLETE_SALE_INFO' => '完善自建仓销售信息',
	'SZ_SALE_PLAN_CLEAR' => '清货',
	'SZ_SALE_PLAN_RELISTING' => '重新刊登',
	'SZ_SALE_PLAN_PRICE_UP' => '涨价',
	'SZ_SALE_PLAN_PRICE_DOWN' => '降价',
	'SZ_SALE_PLAN_NONE' => '无',


	/*
	create table if not exists `3s_sz_sale_plan_metadata`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`clear_nod` smallint(3) not null,
	`relisting_nod` smallint(3) not null,
	`adjust_period` smallint(3) not null,
	`spr1` smallint(3) not null,
	`spr2` smallint(3) not null,
	`spr3` smallint(3) not null,
	`spr4` smallint(3) not null,
	`spr5` smallint(3) not null,
	`pcr` smallint(3) not null,
	`sqnr` smallint(3) not null,
	`denominator` smallint(3) not null,
	`grfr` smallint(3) not null,
	`standard_period` smallint(3) not null
	) engine=myisam default charset=utf8;

	


	Paramters
		1. asq: actual adjust period sale quantity
		2. lsq: last adjust period sale quantity
		3. clear_nod: number of days, if the sale quantity of the clear_nod==0 then clear
		4. relisting_nod: number of days, if the sale quantity of the relisting_nod==0 then relisting
		5. adjust_period: number of days
		6. spr1, spr2, spr3, spr4, spr5: item cost classify. To define different start and floor sale price for different item. The cheap item can be sold with spr1 profit rate. The expensivest item must be sold with spr5 profit rate.
		7. pcr: price change rate, define the proce change rate of each adjust.
		8. sqnr: smallest sale quantity need to be replaced with a determine denominator. For example the lsq=1 and the asp=2. The growth rate is (asp-lsp)/lsp equal 100%. In this situation, the growth rate is high. But the price needn't to be adjusted. So we need to define a sqnr. For example, if the lsq<5, the growth rate should be (asp-lsp)/denominator.
		9. denominator: see the 8.
		10. grfr: growth rate fluctuation range. growth rate >grfr then increse price, growth rate <-grfr then reduct price.
		11. standard_period: define the standard period, the sale quantity of adjust period must be changed to the sale quantity of standard period. Then classify the sale quantity of standard period to decide the grfr.

	Algorithm
	foreach item in usstorage
		if sale qauantty of clear_nod == 0 then clear
		if sale qauantty of relisting_nod == 0 then relisting
		if  last_modify_date < actual_date - adjust_period 
			if (actual_standard_period_sale_quantity - last_standard_period_sale_quantity) / last_standard_period_sale_quantity > grfr then sale_price=sale-price + sale_price*pcr.
			if (actual_standard_period_sale_quantity - last_standard_period_sale_quantity) / last_standard_period_sale_quantity < -grfr then sale_price=sale-price - sale_price*pcr.

	*/
	'DB_SZ_SALE_PLAN_METADATA' => 'sz_sale_plan_metadata',
	'DB_SZ_SALE_PLAN_METADATA_ID' => 'id',
	'DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD' =>'clear_nod', // number of day
	'DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD' =>'relisting_nod', //number of day
	'DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD' =>'adjust_period', //number of day
	'DB_SZ_SALE_PLAN_METADATA_SPR1' =>'spr1',//start_profit_rate for cost under 10 USD
	'DB_SZ_SALE_PLAN_METADATA_SPR2' =>'spr2',//start_profit_rate for cost over 10 under 20 USD
	'DB_SZ_SALE_PLAN_METADATA_SPR3' =>'spr3',//start_profit_rate for cost over 20 under 30 USD
	'DB_SZ_SALE_PLAN_METADATA_SPR4' =>'spr4',//start_profit_rate for cost over 30 under 50 USD
	'DB_SZ_SALE_PLAN_METADATA_SPR5' =>'spr5',//start_profit_rate for cost over 50 USD
	'DB_SZ_SALE_PLAN_METADATA_PCR' =>'pcr',//price_change_rate
	'DB_SZ_SALE_PLAN_METADATA_SQNR' =>'sqnr',//smallest sale quantity need to be replaced with denominator
	'DB_SZ_SALE_PLAN_METADATA_DENOMINATOR' =>'denominator',//denominator to avoid small sale quantity with higher growth rate.
	'DB_SZ_SALE_PLAN_METADATA_GRFR' =>'grfr',//growth rate fluctuation range
	'DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD' =>'standard_period',//the sale quantity of adjust period should be change to sale quantity of standard period



	/*
	ussw_postage_firstclass
	CREATE TABLE IF NOT EXISTS `3s_ussw_postage_firstclass` (
	  `oz` smallint(3) unsigned primary key not null auto_increment,
	  `gram` smallint(3) default null,
	  `fee` decimal(5,2) DEFAULT NULL
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_POSTAGE_FIRSTCLASS' => 'ussw_postage_firstclass',
	'DB_USSW_POSTAGE_FIRSTCLASS_OZ' => 'oz',
	'DB_USSW_POSTAGE_FIRSTCLASS_GR' => 'gram',
	'DB_USSW_POSTAGE_FIRSTCLASS_FEE' =>'fee',


	/*
	ussw_postage_priorityflatrate
	CREATE TABLE IF NOT EXISTS `3s_ussw_postage_priorityflatrate` (
	  `id` smallint(3) unsigned primary key not null auto_increment,
	  `name` varchar(30) default null,
	  `fee` decimal(5,2) DEFAULT NULL
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_POSTAGE_PRIORITYFLATRATE' => 'ussw_postage_priorityflatrate',
	'DB_USSW_POSTAGE_PRIORITYFLATRATE_ID' => 'id',
	'DB_USSW_POSTAGE_PRIORITYFLATRATE_NAME' => 'name',
	'DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE' =>'fee',


	/*
	ussw_postage_priority
	CREATE TABLE IF NOT EXISTS `3s_ussw_postage_priority` (
	  `lbs` smallint(3) unsigned primary key not null auto_increment,
	  `gram` smallint(3) default null,
	  `fee` decimal(5,2) DEFAULT NULL
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_POSTAGE_PRIORITY' => 'ussw_postage_priority',
	'DB_USSW_POSTAGE_PRIORITY_LBS' => 'lbs',
	'DB_USSW_POSTAGE_PRIORITY_GR' => 'gram',
	'DB_USSW_POSTAGE_PRIORITY_FEE' =>'fee',


	/*
	ussw_postage_fedex_smart
	CREATE TABLE IF NOT EXISTS `3s_ussw_postage_fedex_smart` (
	  `lbs` smallint(3) unsigned primary key not null auto_increment,
	  `gram` smallint(3) default null,
	  `fee` decimal(5,2) DEFAULT NULL
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_POSTAGE_FEDEX_SMART' => 'ussw_postage_fedex_smart',
	'DB_USSW_POSTAGE_FEDEX_SMART_LBS' => 'lbs',
	'DB_USSW_POSTAGE_FEDEX_SMART_GR' => 'gram',
	'DB_USSW_POSTAGE_FEDEX_SMART_FEE' =>'fee',


	/*
	ussw_postage_fedex_home
	CREATE TABLE IF NOT EXISTS `3s_ussw_postage_fedex_home` (
	  `lbs` smallint(3) unsigned primary key not null auto_increment,
	  `gram` smallint(3) default null,
	  `fee` decimal(5,2) DEFAULT NULL
	) engine=myisam default charset=utf8;
	*/
	'DB_USSW_POSTAGE_FEDEX_HOME' => 'ussw_postage_fedex_home',
	'DB_USSW_POSTAGE_FEDEX_HOME_LBS' => 'lbs',
	'DB_USSW_POSTAGE_FEDEX_HOME_GR' => 'gram',
	'DB_USSW_POSTAGE_FEDEX_HOME_FEE' =>'fee',


	/*
	Role 
	角色表
	CREATE TABLE IF NOT EXISTS `3s_role` (
	  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(20) NOT NULL,
	  `pid` smallint(6) DEFAULT NULL,
	  `status` tinyint(1) unsigned DEFAULT NULL,
	  `remark` varchar(255) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  KEY `pid` (`pid`),
	  KEY `status` (`status`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
	*/
	'DB_ROLE' => 'role',
	'DB_ROLE_ID' => 'id',
	'DB_ROLE_NAME' => 'name',
	'DB_ROLE_PID' =>'pid',
	'DB_ROLE_STATUS' => 'status',
	'DB_ROLE_REMARK' => 'remark',


	/*
	Node 
	节点表
	CREATE TABLE IF NOT EXISTS `3s_node` (
	  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(20) NOT NULL,
	  `title` varchar(50) DEFAULT NULL,
	  `status` tinyint(1) DEFAULT '0',
	  `remark` varchar(255) DEFAULT NULL,
	  `sort` smallint(6) unsigned DEFAULT NULL,
	  `pid` smallint(6) unsigned NOT NULL,
	  `level` tinyint(1) unsigned NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `level` (`level`),
	  KEY `pid` (`pid`),
	  KEY `status` (`status`),
	  KEY `name` (`name`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_NODE' => 'node',
	'DB_NODE_ID' => 'id',
	'DB_NODE_NAME' => 'name',
	'DB_NODE_TITLE' =>'title',
	'DB_NODE_STATUS' => 'status',
	'DB_NODE_REMARK' => 'remark',
	'DB_NODE_SORT' => 'sort',
	'DB_NODE_PID' => 'pid',	
	'DB_NODE_LEVEL' => 'level',	


	/*
	Access 
	权限表
	CREATE TABLE IF NOT EXISTS `3s_access` (
	  `role_id` smallint(6) unsigned NOT NULL,
	  `node_id` smallint(6) unsigned NOT NULL,
	  `level` tinyint(1) NOT NULL,
	  `module` varchar(50) DEFAULT NULL,
	  KEY `groupId` (`role_id`),
	  KEY `nodeId` (`node_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	*/
	'DB_ACCESS' => 'access',
	'DB_ACCESS_ROLE_ID' => 'role_id',
	'DB_ACCESS_NODE_ID' => 'node_id',
	'DB_ACCESS_LEVEL' =>'level',
	'DB_ACCESS_MODULE' => 'module',


	/*
	Role_user 
	分组表
	CREATE TABLE IF NOT EXISTS `3s_role_user` (
	  `role_id` mediumint(9) unsigned DEFAULT NULL,
	  `user_id` char(32) DEFAULT NULL,
	  KEY `group_id` (`role_id`),
	  KEY `user_id` (`user_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	*/
	'DB_ROLE_USER' => 'role_user',
	'DB_ROLE_USER_ROLE_ID' => 'role_id',
	'DB_ROLE_USER_USER_ID' => 'user_id',

	);
?>