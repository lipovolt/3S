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

	//product_prohibit
	/*
	create table if not exists `3s_product_prohibit`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`product_id` smallint(6) default null,
	`seller_id` smallint(6) default null,
	`prohibit` tinyint(1) default 0
	) engine=myisam default charset=utf8;
	*/
	'DB_PRODUCT_PROHIBIT' => 'product_prohibit', 
	'DB_PRODUCT_PROHIBIT_ID' => 'id',
	'DB_PRODUCT_PROHIBIT_PRODUCT_ID' => 'product_id', 
	'DB_PRODUCT_PROHIBIT_SELLER_ID' => 'seller_id',
	'DB_PRODUCT_PROHIBIT_PROHIBIT' => 'prohibit',


	//product_pack_requirement
	/*
	create table if not exists `3s_product_pack_requirement`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`product_id` smallint(6) default null,
	`warehouse` varchar(20) default null,
	`requirement` varchar(100) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_PRODUCT_PACK_REQUIREMENT' => 'product_pack_requirement', 
	'DB_PRODUCT_PACK_REQUIREMENT_ID' => 'id',
	'DB_PRODUCT_PACK_REQUIREMENT_PRODUCT_ID' => 'product_id', 
	'DB_PRODUCT_PACK_REQUIREMENT_WAREHOUSE' => 'warehouse', 
	'DB_PRODUCT_PACK_REQUIREMENT_REQUIREMENT' => 'requirement',


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
	    `email` varchar(30) default null,
	    `position` varchar(30) default null,
	    `lunch_break` tinyint unsigned default 60,
	    `basic_wage` int(10) unsigned default null,
	    `performace_percent` decimal(5,2) default null
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;        
    */
	'DB_3S_USER' => 'user',
	'DB_3S_USER_ID' => 'id',
	'DB_3S_USER_USERNAME' => 'username',
	'DB_3S_USER_PASSWORD' => 'password',
	'DB_3S_USER_LOGINIP' => 'loginip',
	'DB_3S_USER_LOGINTIME' => 'logintime',
	'DB_3S_USER_LOCK' => 'lock',
	'DB_3S_USER_EMAIL' => 'email',
	'DB_3S_USER_POSITION' => 'position', //主管，员工
	'DB_3S_USER_LUNCH_BREAK' => 'lunch_break', //分钟
	'DB_3S_USER_BASIC_WAGE' => 'basic_wage',
	'DB_3S_USER_PERFOMANCE_PERCENT' => 'performace_percent',

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
    `id` smallint(10) unsigned primary key not null auto_increment,
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
	`id` int(10) unsigned primary key not null auto_increment,
	`market` varchar(10) default null,
	`market_no` varchar(30) default null,
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
	'DB_USSW_OUTBOUND_MARKET' => 'market', //ebay,amazon,groupon
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
	`id` int(10) unsigned primary key not null auto_increment,
	`outbound_id` smallint(10),
	`sku` varchar(10) default null,
	`position` varchar(10) default null,
	`quantity` smallint(3) default 0,
	`market_no` varchar(30) default null,
	`transaction_no` varchar(30) default null
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
	  `id` smallint(10) unsigned primary key NOT NULL auto_increment,
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

	//amazon_us_storage
	/*
	CREATE TABLE IF NOT EXISTS `3s_amazon_us_storage` (
	  `id` smallint(10) unsigned primary key NOT NULL auto_increment,
	  `sku` varchar(15) NOT NULL,
	  `cinventory` smallint(6) DEFAULT 0,
	  `ainventory` smallint(6) DEFAULT 0,
	  `oinventory` smallint(6) DEFAULT 0,
	  `iinventory` smallint(6) DEFAULT 0,
	  `csales` smallint(6) DEFAULT 0,
	  `last_restock_time` datetime
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_AMAZON_US_STORAGE' => 'amazon_us_storage',
	'DB_AMAZON_US_STORAGE_ID' => 'id',
	'DB_AMAZON_US_STORAGE_SKU' => 'sku',
	'DB_AMAZON_US_STORAGE_CINVENTORY' => 'cinventory',
	'DB_AMAZON_US_STORAGE_AINVENTORY' => 'ainventory',
	'DB_AMAZON_US_STORAGE_OINVENTORY' => 'oinventory',
	'DB_AMAZON_US_STORAGE_IINVENTORY' => 'iinventory',
	'DB_AMAZON_US_STORAGE_CSALES' => 'csales',
	'DB_AMAZON_US_STORAGE_LASTTIME' => 'last_restock_time',

	//amazon_us_fba_outbound
	/*创建amazon us fba仓出库表
	create table if not exists `3s_amazon_us_fba_outbound`(
	`id` int(10) unsigned primary key not null auto_increment,
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
	'DB_AMAZON_US_FBA_OUTBOUND' => 'amazon_us_fba_outbound',
	'DB_AMAZON_US_FBA_OUTBOUND_ID' => 'id',
	'DB_AMAZON_US_FBA_OUTBOUND_MARKET' => 'market', //ebay,amazon,groupon
	'DB_AMAZON_US_FBA_OUTBOUND_MARKET_NO' => 'market_no',
	'DB_AMAZON_US_FBA_OUTBOUND_STATUS' => 'status', //待出库，已出库
	'DB_AMAZON_US_FBA_OUTBOUND_SHIPPING_COMPANY' => 'shipping_company',
	'DB_AMAZON_US_FBA_OUTBOUND_SHIPPING_WAY' => 'shipping_way',
	'DB_AMAZON_US_FBA_OUTBOUND_TRACKING_NUMBER' => 'tracking_number',
	'DB_AMAZON_US_FBA_OUTBOUND_CREATE_TIME' => 'create_time',
	'DB_AMAZON_US_FBA_OUTBOUND_SELLER_ID' => 'seller_id',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_ID' => 'buyer_id',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_NAME' => 'buyer_name',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_TEL' => 'buyer_tel',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_EMAIL' => 'buyer_email',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_ADDRESS1' => 'buyer_address1',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_ADDRESS2' => 'buyer_address2',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_CITY' => 'buyer_city',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_STATE' => 'buyer_state',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_COUNTRY' => 'buyer_country',
	'DB_AMAZON_US_FBA_OUTBOUND_BUYER_ZIP' => 'buyer_zip',

	//amazon_us_fba_outbound_item
	/*
	创建美国出库单产品明细表
	create table if not exists `3s_amazon_us_fba_outbound_item`(
	`id` int(10) unsigned primary key not null auto_increment,
	`outbound_id` smallint(10),
	`sku` varchar(15) default null,
	`position` varchar(10) default null,
	`quantity` smallint(3) default 0,
	`market_no` varchar(20) default null,
	`transaction_no` varchar(20) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM' => 'amazon_us_fba_outbound_item',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_ID' => 'id',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_OOID' => 'outbound_id',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_SKU' => 'sku',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_POSITION' => 'position',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_QUANTITY' => 'quantity',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_MARKET_NO' => 'market_no',
	'DB_AMAZON_US_FBA_OUTBOUND_ITEM_TRANSACTION_NO' => 'transaction_no',



	//restock
	/*创建补货表
	CREATE TABLE IF NOT EXISTS `3s_restock` (
	  `id` int(10) unsigned primary key auto_increment,
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
	'DB_RESTOCK_STATUS' => 'status', //待发货，已发货,延迟发货
	'DB_RESTOCK_REMARK' => 'remark',

	//restock parameters
	/*创建补货参数表
	CREATE TABLE IF NOT EXISTS `3s_restock_parameters` (
	  `id` smallint(6) unsigned primary key auto_increment,
	  `ussw_air_ad` smallint(6) DEFAULT 15,
	  `ussw_air_td` smallint(6) DEFAULT 30,
	  `ussw_air_id` smallint(6) DEFAULT 6,
	  `ussw_sea_ad` smallint(6) DEFAULT 15,
	  `ussw_sea_td` smallint(6) DEFAULT 30,
	  `ussw_sea_id` smallint(6) DEFAULT 6,
	  `winitde_air_ad` smallint(6) DEFAULT 30,
	  `winitde_air_td` smallint(6) DEFAULT 90,
	  `winitde_air_id` smallint(6) DEFAULT 25,
	  `winitde_sea_ad` smallint(6) DEFAULT 30,
	  `winitde_sea_td` smallint(6) DEFAULT 90,
	  `winitde_sea_id` smallint(6) DEFAULT 25,
	  `szsw_ad` smallint(6) DEFAULT 0,
	  `szsw_min_ai` smallint(6) default 0,
	  `ussw_lock` tinyint(1) default 0,
	  `winitde_lock` tinyint(1) default 0,
	  `ussw_auto_move` tinyint(1) default 0,
	  `winitde_auto_move` tinyint(1) default 0,
	  `exclude_large_quantity` smallint(6) default 0,
	  `no_order_days` smallint(6) default 0,
	  `ussw_estimated_days_sea_shipping` smallint(6) default 0,
	  `ussw_air_first_count_limit` smallint(6) default 0,
	  `ussw_air_first_days_limit` smallint(6) default 0,
	  `winitde_estimated_days_sea_shipping` smallint(6) default 0,
	  `winitde_air_first_count_limit` smallint(6) default 0,
	  `winitde_air_first_days_limit` smallint(6) default 0,
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	*/
	'DB_RESTOCK_PARA' => 'restock_parameters',
	'DB_RESTOCK_PARA_ID' => 'id',
	'DB_RESTOCK_PARA_USSW_AIR_AD' => 'ussw_air_ad',
	'DB_RESTOCK_PARA_USSW_AIR_tD' => 'ussw_air_td',
	'DB_RESTOCK_PARA_USSW_AIR_iD' => 'ussw_air_id',
	'DB_RESTOCK_PARA_USSW_SEA_AD' => 'ussw_sea_ad',
	'DB_RESTOCK_PARA_USSW_SEA_tD' => 'ussw_sea_td',
	'DB_RESTOCK_PARA_USSW_SEA_iD' => 'ussw_sea_id',
	'DB_RESTOCK_PARA_WINITDE_AIR_AD' => 'winitde_air_ad',
	'DB_RESTOCK_PARA_WINITDE_AIR_tD' => 'winitde_air_td',
	'DB_RESTOCK_PARA_WINITDE_AIR_iD' => 'winitde_air_id',
	'DB_RESTOCK_PARA_WINITDE_SEA_AD' => 'winitde_sea_ad',
	'DB_RESTOCK_PARA_WINITDE_SEA_tD' => 'winitde_sea_td',
	'DB_RESTOCK_PARA_WINITDE_SEA_ID' => 'winitde_sea_id',
	'DB_RESTOCK_PARA_SZSW_AD' => 'szsw_ad',
	'DB_RESTOCK_PARA_SZSW_MIN_AI' => 'szsw_min_ai',
	'DB_RESTOCK_PARA_USSW_LOCK' => 'ussw_lock',
	'DB_RESTOCK_PARA_USSW_AUTO_MOVE' => 'ussw_auto_move',
	'DB_RESTOCK_PARA_WINITDE_LOCK' => 'winitde_lock',
	'DB_RESTOCK_PARA_WINITDE_AUTO_MOVE' => 'winitde_auto_move',
	'DB_RESTOCK_PARA_ELQ' => 'exclude_large_quantity',
	'DB_RESTOCK_PARA_NOD' => 'no_order_days',
	'DB_RESTOCK_PARA_USSW_EDSS' => 'ussw_estimated_days_sea_shipping',
	'DB_RESTOCK_PARA_USSW_AFCL' => 'ussw_air_first_count_limit',
	'DB_RESTOCK_PARA_USSW_AFDL' => 'ussw_air_first_days_limit',
	'DB_RESTOCK_PARA_WINITDE_EDSS' => 'winitde_estimated_days_sea_shipping',
	'DB_RESTOCK_PARA_WINITDE_AFCL' => 'winitde_air_first_count_limit',
	'DB_RESTOCK_PARA_WINITDE_AFDL' => 'winitde_air_first_days_limit',

	//purchase
	/*创建采购表
	create table if not exists `3s_purchase`(
	`id` int(10) unsigned primary key not null auto_increment,
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
	`purchase_item_id` int(10) unsigned primary key not null auto_increment,
	`purchase_id` int(10) unsigned default null,
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
	`id` smallint(10) unsigned primary key not null auto_increment,
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
	  `id` smallint(10) unsigned primary key NOT NULL auto_increment,
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
	`id` int(10) unsigned primary key not null auto_increment,
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
	`id` int(10) unsigned primary key not null auto_increment,
	`outbound_id` int(10) unsigned not null,
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



	//ussw_sale_plan for ebay greatgoodshop
	/*
	create table if not exists `3s_ussw_sale_plan`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`sale_status` tinyint(1) default 0
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
	'DB_USSW_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1=Banned sale


	//ussw_sale_plan2 for amazon lipovolt
	/*
	create table if not exists `3s_ussw_sale_plan2`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(15) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`sale_status` tinyint(1) default 0,
	`upc` bigint default null
	) engine=myisam default charset=utf8;	
	*/
	'DB_USSW_SALE_PLAN2' => 'ussw_sale_plan2',
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
	'DB_USSW_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1=Banned sale
	'DB_USSW_SALE_PLAN_UPC' => 'upc',



	//ussw_sale_plan3 for groupon lipovolt
	/*
	create table if not exists `3s_ussw_sale_plan3`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`sale_status` tinyint(1) default 0
	) engine=myisam default charset=utf8;	
	*/
	'DB_USSW_SALE_PLAN3' => 'ussw_sale_plan3',
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
	'DB_USSW_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1=Banned sale



	//ussw_sale_plan4 for ebay blackfive
	/*
	create table if not exists `3s_ussw_sale_plan4`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`sale_status` tinyint(1) default 0
	) engine=myisam default charset=utf8;	
	*/
	'DB_USSW_SALE_PLAN4' => 'ussw_sale_plan4',
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
	'DB_USSW_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1=Banned sale



	'USSW_SALE_PLAN_COMPLETE_PRODUCT_INFO' => '完善产品信息',
	'USSW_SALE_PLAN_COMPLETE_SALE_INFO' => '完善销售信息',
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
		8. sqnr: smallest sale quantity need to be replaced with a determine denominator. For example the lsq=1 and the asq=2. The growth rate is (asq-lsp)/lsp equal 100%. In this situation, the growth rate is high. But the price needn't to be adjusted. So we need to define a sqnr. For example, if the lsq<5, the growth rate should be (asq-lsp)/denominator.
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
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`register` tinyint(1) default 1,
	`sale_status` tinyint(1) default 0
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
	'DB_SZ_US_SALE_PLAN_REGISTER' => 'register', //register shipping. 1=yes,0=no
	'DB_SZ_US_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1= banned sale



	//sz_sale_plan
	/*
	create table if not exists `3s_sz_de_sale_plan`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`register` tinyint(1) default 1,
	`sale_status` tinyint(1) default 0
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
	'DB_SZ_DE_SALE_PLAN_REGISTER' => 'register', //register shipping. 1=yes,0=no
	'DB_SZ_DE_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1= banned sale



	'SZ_SALE_PLAN_COMPLETE_PRODUCT_INFO' => '完善产品信息',
	'SZ_SALE_PLAN_COMPLETE_SALE_INFO' => '完善销售信息',
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
	'DB_SZ_SALE_PLAN_METADATA_PROFIT_LIMIT' =>'0.39', // Profit limit 39%

	//sz_wish_sale_plan
	/*
	create table if not exists `3s_sz_wish_sale_plan`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
	`register` tinyint(1) default 1
	) engine=myisam default charset=utf8;	
	*/
	'DB_SZ_WISH_SALE_PLAN' => 'sz_wish_sale_plan',
	'DB_SZ_WISH_SALE_PLAN_ID' => 'id',
	'DB_SZ_WISH_SALE_PLAN_SKU' => 'sku',
	'DB_SZ_WISH_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_SZ_WISH_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_SZ_WISH_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_SZ_WISH_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_SZ_WISH_SALE_PLAN_COST' => 'cost',
	'DB_SZ_WISH_SALE_PLAN_PRICE' => 'sale_price',
	'DB_SZ_WISH_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_SZ_WISH_SALE_PLAN_SUGGEST' => 'suggest', //clear,us_relisting,us_price_up, ,us_price_down,de_relisting,de_price_up, ,de_price_down,complete_product_info,complete_sale_info,null
	'DB_SZ_WISH_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close
	'DB_SZ_WISH_SALE_PLAN_REGISTER' => 'register', //register shipping. 1=yes,0=no


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
	'DB_ROLE_NAME' => 'name',//产品经理，销售，仓库管理，客服
	'DB_ROLE_PID' =>'pid',
	'DB_ROLE_STATUS' => 'status',
	'DB_ROLE_REMARK' => 'remark',


	/*
	Node 
	节点表
	CREATE TABLE IF NOT EXISTS `3s_node` (
	  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(50) NOT NULL,
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



	/*
	Todo
	任务表
	CREATE TABLE IF NOT EXISTS `3s_todo` (
	  `id` int(10) unsigned primary key not null auto_increment,
	  `ctime` timestamp default NOW(),
	  `dtime` timestamp default null,
	  `creater` varchar(20) NOT NULL,
	  `person` varchar(20) NOT NULL,
	  `status` tinyint(1) unsigned DEFAULT NULL,
	  `task` varchar(500) DEFAULT NULL,
	  `remark` varchar(500) DEFAULT NULL
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
	*/
	'DB_TODO' => 'todo',
	'DB_TODO_ID' => 'id',
	'DB_TODO_CTIME' => 'ctime',
	'DB_TODO_DTIME' => 'dtime',
	'DB_TODO_CREATER' => 'creater',
	'DB_TODO_PERSON' => 'person',
	'DB_TODO_STATUS' => 'status',
	'DB_TODO_TASK' => 'task', //0, 待处理，1 已处理
	'DB_TODO_REMARK' => 'remark',


	//winit_outbound
	/*创建万邑通出库表
	create table if not exists `3s_winit_outbound`(
	`id` int(10) unsigned primary key not null auto_increment,
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
	'DB_WINIT_OUTBOUND' => 'winit_outbound',
	'DB_WINIT_OUTBOUND_ID' => 'id',
	'DB_WINIT_OUTBOUND_MARKET' => 'market', //ebay,amazon,groupon
	'DB_WINIT_OUTBOUND_MARKET_NO' => 'market_no',
	'DB_WINIT_OUTBOUND_STATUS' => 'status', //待出库，已出库
	'DB_WINIT_OUTBOUND_SHIPPING_COMPANY' => 'shipping_company',
	'DB_WINIT_OUTBOUND_SHIPPING_WAY' => 'shipping_way',
	'DB_WINIT_OUTBOUND_TRACKING_NUMBER' => 'tracking_number',
	'DB_WINIT_OUTBOUND_CREATE_TIME' => 'create_time',
	'DB_WINIT_OUTBOUND_SELLER_ID' => 'seller_id',
	'DB_WINIT_OUTBOUND_BUYER_ID' => 'buyer_id',
	'DB_WINIT_OUTBOUND_BUYER_NAME' => 'buyer_name',
	'DB_WINIT_OUTBOUND_BUYER_TEL' => 'buyer_tel',
	'DB_WINIT_OUTBOUND_BUYER_EMAIL' => 'buyer_email',
	'DB_WINIT_OUTBOUND_BUYER_ADDRESS1' => 'buyer_address1',
	'DB_WINIT_OUTBOUND_BUYER_ADDRESS2' => 'buyer_address2',
	'DB_WINIT_OUTBOUND_BUYER_CITY' => 'buyer_city',
	'DB_WINIT_OUTBOUND_BUYER_STATE' => 'buyer_state',
	'DB_WINIT_OUTBOUND_BUYER_COUNTRY' => 'buyer_country',
	'DB_WINIT_OUTBOUND_BUYER_ZIP' => 'buyer_zip',

	//wini_outbound_item
	/*
	创建万邑通出库单产品明细表
	create table if not exists `3s_winit_outbound_item`(
	`id` int(10) unsigned primary key not null auto_increment,
	`outbound_id` int(10) unsigned,
	`sku` varchar(10) default null,
	`position` varchar(10) default null,
	`quantity` smallint(3) default 0,
	`market_no` varchar(20) default null,
	`transaction_no` varchar(20) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_WINIT_OUTBOUND_ITEM' => 'winit_outbound_item',
	'DB_WINIT_OUTBOUND_ITEM_ID' => 'id',
	'DB_WINIT_OUTBOUND_ITEM_OOID' => 'outbound_id',
	'DB_WINIT_OUTBOUND_ITEM_SKU' => 'sku',
	'DB_WINIT_OUTBOUND_ITEM_POSITION' => 'position',
	'DB_WINIT_OUTBOUND_ITEM_QUANTITY' => 'quantity',
	'DB_WINIT_OUTBOUND_ITEM_MARKET_NO' => 'market_no',
	'DB_WINIT_OUTBOUND_ITEM_TRANSACTION_NO' => 'transaction_no',

	//winit_de_storage
	/*
	CREATE TABLE IF NOT EXISTS `3s_winit_de_storage` (
	  `id` smallint(10) unsigned primary key NOT NULL auto_increment,
	  `position` varchar(10) NOT NULL,
	  `sku` varchar(15) NOT NULL,
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
	'DB_WINIT_DE_STORAGE' => 'winit_de_storage',
	'DB_WINIT_DE_STORAGE_ID' => 'id',
	'DB_WINIT_DE_STORAGE_POSITION' => 'position',
	'DB_WINIT_DE_STORAGE_SKU' => 'sku',
	'DB_WINIT_DE_STORAGE_CNAME' => 'cname',
	'DB_WINIT_DE_STORAGE_ENAME' => 'ename',
	'DB_WINIT_DE_STORAGE_ATTRIBUTE' => 'attribute',
	'DB_WINIT_DE_STORAGE_CINVENTORY' => 'cinventory',
	'DB_WINIT_DE_STORAGE_AINVENTORY' => 'ainventory',
	'DB_WINIT_DE_STORAGE_OINVENTORY' => 'oinventory',
	'DB_WINIT_DE_STORAGE_IINVENTORY' => 'iinventory',
	'DB_WINIT_DE_STORAGE_CSALES' => 'csales',
	'DB_WINIT_DE_STORAGE_REMARK' => 'remark',
	'DB_WINIT_DE_STORAGE_SALE_STATUS' => 'sale_status', //待下架，已下架, Null

	//winit_us_storage
	/*
	CREATE TABLE IF NOT EXISTS `3s_winit_us_storage` (
	  `id` smallint(10) unsigned primary key NOT NULL auto_increment,
	  `position` varchar(10) NOT NULL,
	  `sku` varchar(15) NOT NULL,
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
	'DB_WINIT_US_STORAGE' => 'winit_us_storage',
	'DB_WINIT_US_STORAGE_ID' => 'id',
	'DB_WINIT_US_STORAGE_POSITION' => 'position',
	'DB_WINIT_US_STORAGE_SKU' => 'sku',
	'DB_WINIT_US_STORAGE_CNAME' => 'cname',
	'DB_WINIT_US_STORAGE_ENAME' => 'ename',
	'DB_WINIT_US_STORAGE_ATTRIBUTE' => 'attribute',
	'DB_WINIT_US_STORAGE_CINVENTORY' => 'cinventory',
	'DB_WINIT_US_STORAGE_AINVENTORY' => 'ainventory',
	'DB_WINIT_US_STORAGE_OINVENTORY' => 'oinventory',
	'DB_WINIT_US_STORAGE_IINVENTORY' => 'iinventory',
	'DB_WINIT_US_STORAGE_CSALES' => 'csales',
	'DB_WINIT_US_STORAGE_REMARK' => 'remark',
	'DB_WINIT_US_STORAGE_SALE_STATUS' => 'sale_status', //待下架，已下架, Null

	//rc_de_sale_plan for rc-helicar ebay.de items
	/*
	create table if not exists `3s_rc_de_sale_plan`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
    `sale_status` tinyint(1) default 0
	) engine=myisam default charset=utf8;	
	*/
	'DB_RC_DE_SALE_PLAN' => 'rc_de_sale_plan',
	'DB_RC_DE_SALE_PLAN_ID' => 'id',
	'DB_RC_DE_SALE_PLAN_SKU' => 'sku',
	'DB_RC_DE_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_RC_DE_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_RC_DE_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_RC_DE_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_RC_DE_SALE_PLAN_COST' => 'cost',
	'DB_RC_DE_SALE_PLAN_PRICE' => 'sale_price',
	'DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_RC_DE_SALE_PLAN_SUGGEST' => 'suggest', //clear,relisting,price_up, ,price_down,complete_product_info,complete_sale_info,null
	'DB_RC_DE_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close
	'DB_RC_DE_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1= banned sale


	//yzhan_816_pl_sale_plan for rc-helicar ebay.de items
	/*
	create table if not exists `3s_yzhan_816_pl_sale_plan`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
    `sale_status` tinyint(1) default 0
	) engine=myisam default charset=utf8;	
	*/
	'DB_YZHAN_816_PL_SALE_PLAN' => 'yzhan_816_pl_sale_plan',
	'DB_YZHAN_816_PL_SALE_PLAN_ID' => 'id',
	'DB_YZHAN_816_PL_SALE_PLAN_SKU' => 'sku',
	'DB_YZHAN_816_PL_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_YZHAN_816_PL_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_YZHAN_816_PL_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_YZHAN_816_PL_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_YZHAN_816_PL_SALE_PLAN_COST' => 'cost',
	'DB_YZHAN_816_PL_SALE_PLAN_PRICE' => 'sale_price',
	'DB_YZHAN_816_PL_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_YZHAN_816_PL_SALE_PLAN_SUGGEST' => 'suggest', //clear,relisting,price_up, ,price_down,complete_product_info,complete_sale_info,null
	'DB_YZHAN_816_PL_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close
	'DB_YZHAN_816_PL_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1= banned sale

	//winit de amazon sale plan
	/*
	create table if not exists `3s_winit_de_amazon_sale_plan`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`sku` varchar(10) not null,
	`first_sale_date` timestamp default NOW(),
	`last_modify_date` datetime default null,
	`relisting_times` smallint(6) default 0,
	`price_note` varchar(255) default null,
	`cost` decimal(5,2) default null,
	`sale_price` decimal(5,2) default null,
	`suggested_price` decimal(5,2) default null,
	`suggest` varchar(20) default null,
	`status` tinyint(1) default 1,
    `sale_status` tinyint(1) default 0,
    `upc` bigint default null
	) engine=myisam default charset=utf8;	
	*/
	'DB_WINIT_DE_AMAZON_SALE_PLAN' => 'winit_de_amazon_sale_plan',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_ID' => 'id',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_SKU' => 'sku',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_FIRST_DATE' => 'first_sale_date',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_LAST_MODIFY_DATE' => 'last_modify_date',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_RELISTING_TIMES' => 'relisting_times',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_PRICE_NOTE' => 'price_note',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_COST' => 'cost',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_PRICE' => 'sale_price',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_SUGGESTED_PRICE' => 'suggested_price',
	'DB_WINIT_DE_AMAZON_SALE_PLAN_SUGGEST' => 'suggest', //clear,relisting,price_up, ,price_down,complete_product_info,complete_sale_info,null
	'DB_WINIT_DE_AMAZON_SALE_PLAN_STATUS' => 'status', //open or close the automatic suggest. 1=open,0=close
	'DB_WINIT_DE_AMAZON_SALE_PLAN_SALE_STATUS' => 'sale_status', //0=free sale 1= banned sale
	'DB_WINIT_DE_AMAZON_SALE_PLAN_UPC' => 'upc', 

	//Income cost table
	/*
	create table if not exists `3s_income_cost`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`month` varchar(20) not null,
	`seller_id` varchar(30) default null,
	`seller_id_type` varchar(10) default null, //cooperate,personal
	`usd_income` decimal(10,2) default null,
	`eur_income` decimal(10,2) default null,
	`usd_item_cost` decimal(10,2) default null,
	`eur_item_cost` decimal(10,2) default null,
	`usd_return` decimal(10,2) default null,
	`eur_return` decimal(10,2) default null,
	`market_fee` decimal(10,2) default null,
	`paypal_fee` decimal(10,2) default null,
	`tax_collection` decimal(10,2) default null
	) engine=myisam default charset=utf8;	
	*/
	'DB_INCOMECOST' => 'income_cost',
	'DB_INCOMECOST_ID' => 'id',
	'DB_INCOMECOST_MONTH' => 'month',
	'DB_INCOMECOST_SLLERID' => 'seller_id',
	'DB_INCOMECOST_SLLERIDTYPE' => 'seller_id_type',
	'DB_INCOMECOST_USDINCOME' => 'usd_income',
	'DB_INCOMECOST_USDITEMCOST' => 'usd_item_cost',
	'DB_INCOMECOST_USDRETURN' => 'usd_return',
	'DB_INCOMECOST_EURINCOME' => 'eur_income',
	'DB_INCOMECOST_EURITEMCOST' => 'eur_item_cost',
	'DB_INCOMECOST_EURRETURN' => 'eur_return',
	'DB_INCOMECOST_MARKETFEE' => 'market_fee', //USD
	'DB_INCOMECOST_PAYPALFEE' => 'paypal_fee', //USD
	'DB_INCOMECOST_TAX_COLLECTION' => 'tax_collection', //USD

	//Sale fee table
	/*
	create table if not exists `3s_sale_fee`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`month` varchar(20) not null,
	`ussw_sf_cn` decimal(10,2) default null,
	`ussw_sf_local` decimal(10,2) default null,
	`ussw_storage_fee` decimal(10,2) default null,
	`ussw_tariff` decimal(10,2) default null,
	`third_party_sf_cn` decimal(10,2) default null,
	`third_party_sf_local` decimal(10,2) default null,
	`third_party_storage_fee` decimal(10,2) default null,
	`third_party_tariff` decimal(10,2) default null,
	`szsw_sf` decimal(10,2) default null
	) engine=myisam default charset=utf8;	
	*/
	'DB_SALEFEE' => 'sale_fee',
	'DB_SALEFEE_ID' => 'id',
	'DB_SALEFEE_MONTH' => 'month',
	'DB_SALEFEE_USSWSFCN' => 'ussw_sf_cn', //RMB
	'DB_SALEFEE_USSWSFLOCAL' => 'ussw_sf_local', //USD
	'DB_SALEFEE_USSWSTORAGEFEE' => 'ussw_storage_fee', //USD
	'DB_SALEFEE_USSWTARIFF' => 'ussw_tariff', //USD
	'DB_SALEFEE_THRIDPARTYSFCN' => 'third_party_sf_cn',//USD
	'DB_SALEFEE_THIRDPARTYSFLOCAL' => 'third_party_sf_local',//USD
	'DB_SALEFEE_THIRDPARTYSTORAGEFEE' => 'third_party_storage_fee',//USD
	'DB_SALEFEE_THIRDPARTYTARIFF' => 'third_party_tariff', //USD
	'DB_SALEFEE_SZSWSF' => 'szsw_sf', //RMB

	//Management fee table
	/*
	create table if not exists `3s_management_fee`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`month` varchar(20) not null,
	`purpose` varchar(20) default null,
	`amount` decimal(10,2) default null,
	`share_type` varchar(20) default null,
	`remark` varchar(255) default null
	) engine=myisam default charset=utf8;	
	*/
	'DB_MANAGEMENTFEE' => 'management_fee',
	'DB_MANAGEMENTFEE_ID' => 'id',
	'DB_MANAGEMENTFEE_MONTH' => 'month',
	'DB_MANAGEMENTFEE_PURPOSE' => 'purpose', //rent,booking,packing,purchasing_shipping_fee,other
	'DB_MANAGEMENTFEE_AMOUNT' => 'amount', //RMB
	'DB_MANAGEMENTFEE_SHARE_TYPE' => 'share_type', //cooperate,personal,all
	'DB_MANAGEMENTFEE_REMARK' => 'remark',


	//Wages table
	/*
	create table if not exists `3s_wages`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`month` varchar(20) not null,
	`name` varchar(10) default null,
	`base` decimal(10,2) default null,
	`performance` decimal(10,2) default null,
	`percent` decimal(5,2) default null,
	`si_company` decimal(10,2) default null,
	`si_person` decimal(10,2) default null,
	`bonus` decimal(10,2) default 0,
	`leave_days` decimal(5,2) default null,
	`remark` varchar(255) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_WAGES' => 'wages',
	'DB_WAGES_ID' => 'id',
	'DB_WAGES_MONTH' => 'month',
	'DB_WAGES_NAME' => 'name',
	'DB_WAGES_BASE' => 'base',
	'DB_WAGES_PERFORMANCE' => 'performance',
	'DB_WAGES_PERCENT' => 'percent',
	'DB_WAGES_SI_COMPANY' => 'si_company',
	'DB_WAGES_SI_PERSON' => 'si_person',
	'DB_WAGES_LEAVE_DAYS' => 'leave_days',
	'DB_WAGES_REMARK' => 'remark',
	'DB_WAGES_BONUS' => 'bonus',


	//Attendance table
	/*
	create table if not exists `3s_attendance`(
	`id` smallint(10) unsigned primary key not null auto_increment,
	`name` varchar(20) not null,
	`come_ip` varchar(30) default null,
	`leave_ip` varchar(30) default null,
	`come_time` varchar(30) default null,
	`leave_time` varchar(30) default null,
	`rest1_begin` varchar(30) default null,
	`rest1_end` varchar(30) default null,
	`rest2_begin` varchar(30) default null,
	`rest2_end` varchar(30) default null,
	`overtime_begin` varchar(30) default null,
	`overtime_end` varchar(30) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_ATTENDANCE' => 'attendance',
	'DB_ATTENDANCE_ID' => 'id',
	'DB_ATTENDANCE_NAME' => 'name',
	'DB_ATTENDANCE_COMEIP' => 'come_ip',
	'DB_ATTENDANCE_LEAVEIP' => 'leave_ip',
	'DB_ATTENDANCE_COMETIME' => 'come_time',
	'DB_ATTENDANCE_LEAVETIME' => 'leave_time',
	'DB_ATTENDANCE_REST1_BEGIN' => 'rest1_begin',
	'DB_ATTENDANCE_REST1_END' => 'rest1_end',
	'DB_ATTENDANCE_REST2_BEGIN' => 'rest2_begin',
	'DB_ATTENDANCE_REST2_END' => 'rest2_end',
	'DB_ATTENDANCE_OVERTIME_BEGIN' => 'overtime_begin',
	'DB_ATTENDANCE_OVERTIME_END' => 'overtime_end',

	//kpi_sale
	/*
	create table if not exists `3s_kpi_sale`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`name` varchar(20) not null,
	`sku` varchar(10) default null,
	`warehouse` varchar(30) default null,
	`type` varchar(30) default null, 
	`begin_date` int(30) not null,
	`begin_squantity` int(10) unsigned default null
	) engine=myisam default charset=utf8;
	*/
	'DB_KPI_SALE' => 'kpi_sale',
	'DB_KPI_SALE_ID' => 'id',
	'DB_KPI_SALE_NAME' => 'name',
	'DB_KPI_SALE_SKU' => 'sku',
	'DB_KPI_SALE_WAREHOUSE' => 'warehouse',
	'DB_KPI_SALE_TYPE' => 'type', //重新刊登，清货
	'DB_KPI_SALE_BEGIN_DATE' => 'begin_date',
	'DB_KPI_SALE_BEGIN_SQUANTITY' => 'begin_squantity',
	//以下三个不需要数据库列。实时计算
	'DB_KPI_SALE_SALE_QUANTITY' => 'sale_quantity',
	'DB_KPI_SALE_AVERAGE_PROFIT' => 'average_profit',
	'DB_KPI_SALE_DETAILS' => 'details',

	//kpi_sale_record
	/*
	create table if not exists `3s_kpi_sale_record`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`kpi_sale_id` smallint(6) unsigned not null,
	`sku` varchar(10) default null,
	`warehouse` varchar(30) default null,
	`sold_date` int(30) not null,
	`quantity` tinyint unsigned not null,
	`price` decimal(10,2) default null,
	`shipping_fee` decimal(10,2) default null,
	`market` varchar(30) default null,
	`seller_id` varchar(30) default null,
	`market_no` varchar(30) default null,
	`transaction_no` varchar(30) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_KPI_SALE_RECORD' => 'kpi_sale_record',
	'DB_KPI_SALE_RECORD_ID' => 'id',
	'DB_KPI_SALE_RECORD_SALE_ID' => 'kpi_sale_id',
	'DB_KPI_SALE_RECORD_SKU' => 'sku',
	'DB_KPI_SALE_RECORD_WAREHOUSE' => 'warehouse',
	'DB_KPI_SALE_RECORD_SOLD_DATE' => 'sold_date',
	'DB_KPI_SALE_RECORD_QUANTITY' => 'quantity',
	'DB_KPI_SALE_RECORD_PRICE' => 'price',
	'DB_KPI_SALE_RECORD_SHIPPING_FEE' => 'shipping_fee',
	'DB_KPI_SALE_RECORD_MARKET' => 'market',
	'DB_KPI_SALE_RECORD_SELLER_ID' => 'seller_id',
	'DB_KPI_SALE_RECORD_MARKET_NO' => 'market_no',
	'DB_KPI_SALE_RECORD_TRANSACTION_NO' => 'transaction_no',

	//kpi_storage
	/*
	create table if not exists `3s_kpi_storage_mistake`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`name` varchar(20) not null,
	`month` varchar(10) default null,
	`mistake` varchar(100) default null,
	`score` tinyint signed default null
	) engine=myisam default charset=utf8;
	*/
	'DB_KPI_STORAGE_MISTAKE' => 'kpi_storage_mistake',
	'DB_KPI_STORAGE_MISTAKE_ID' => 'id',
	'DB_KPI_STORAGE_MISTAKE_NAME' => 'name',
	'DB_KPI_STORAGE_MISTAKE_MONTH' => 'month',
	'DB_KPI_STORAGE_MISTAKE_MISTAKE' => 'mistake',
	'DB_KPI_STORAGE_MISTAKE_SCORE' => 'score',

	//kpi_customer
	/*
	create table if not exists `3s_kpi_customer`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`name` varchar(20) not null,
	`month` varchar(10) default null,
	`performance` varchar(100) default null,
	`score` tinyint signed default null
	) engine=myisam default charset=utf8;
	*/
	'DB_KPI_CUSTOMER' => 'kpi_customer',
	'DB_KPI_CUSTOMER_ID' => 'id',
	'DB_KPI_CUSTOMER_NAME' => 'name',
	'DB_KPI_CUSTOMER_MONTH' => 'month',
	'DB_KPI_CUSTOMER_PERFORMANCE' => 'performance',
	'DB_KPI_CUSTOMER_SCORE' => 'score',

	//kpi_statistic
	/*
	create table if not exists `3s_kpi_statistic`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`name` varchar(20) not null,
	`month` varchar(10) default null,
	`type` varchar(30) default null,
	`score` int(30) default null,
	`pass` tinyint(1) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_KPI_STATISTIC' => 'kpi_statistic',
	'DB_KPI_STATISTIC_ID' => 'id',
	'DB_KPI_STATISTIC_NAME' => 'name',
	'DB_KPI_STATISTIC_MONTH' => 'month',
	'DB_KPI_STATISTIC_TYPE' => 'type',//new_item_quantity,relisting,clear,mistake,customer_performance
	'DB_KPI_STATISTIC_SCORE' => 'score',
	'DB_KPI_STATISTIC_PASS' => 'pass',

	//kpi_parameters
	/*
	create table if not exists `3s_kpi_parameters`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`product_month_minium` smallint(6) default null,
	`product_year_standard` smallint(6) default null,
	`sale_month_minium` smallint(6) default null,
	`sale_year_standard` smallint(6) default null,
	`storage_month_minium` smallint(6) default null,
	`storage_year_standard` smallint(6) default null,
	`customer_year_standard` smallint(6) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_KPI_PARAMETERS' => 'kpi_parameters', 
	'DB_KPI_PARAMETERS_ID' => 'id',
	'DB_KPI_PARAMETERS_PMM' => 'product_month_minium', 
	'DB_KPI_PARAMETERS_PYS' => 'product_year_standard',
	'DB_KPI_PARAMETERS_SMM' => 'sale_month_minium',
	'DB_KPI_PARAMETERS_SYS' => 'sale_year_standard',
	'DB_KPI_PARAMETERS_STMM' => 'storage_month_minium',
	'DB_KPI_PARAMETERS_STYS' => 'storage_year_standard',
	'DB_KPI_PARAMETERS_CYS' => 'customer_year_standard',


	//block buyer
	/*
	create table if not exists `3s_block_buyer`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`buyer_id` varchar(50) default null,
	`platform` varchar(50) default null,
	`block_date`  timestamp default NOW()
	) engine=myisam default charset=utf8;
	*/
	'DB_BLOCK_BUYER' => 'block_buyer', 
	'DB_BLOCK_BUYER_ID' => 'id',
	'DB_BLOCK_BUYER_BID' => 'buyer_id', 
	'DB_BLOCK_BUYER_PLATFORM' => 'platform',
	'DB_BLOCK_BUYER_BDATE' => 'block_date',

	//block_buyer_constant
	'DB_BLOCK_BUYER_PLATFORM_CONSTANT' =>array('amazon.com','amazon.de','ebay.com','ebay.de','groupon.com'),


	//bank
	/*
	create table if not exists `3s_bank`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`paypal_id` smallint(6) default 0,
	`holder_name` varchar(50) not null,	
	`holder_tel`  varchar(50) not null,
	`account` varchar(50) not null,
	`bank_name`  varchar(50) not null,
	`bank_address`  varchar(250) default null,
	`bank_country`  varchar(50) not null,
	`bank_swift`  varchar(50) default null,
	`online_banking_account`  varchar(50) default null,
	`online_banking_pw`  varchar(50) default null,
	`withdrawal_pw`  varchar(50) default null,
	`status`  tinyint default 0,
	`remark` varchar(250) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_BANK' => 'bank', 
	'DB_BANK_ID' => 'id',
	'DB_BANK_PID' => 'paypal_id',
	'DB_BANK_HOLDER_NAME' => 'holder_name', 
	'DB_BANK_HOLDER_TEL' => 'holder_tel',
	'DB_BANK_ACCOUNT' => 'account',
	'DB_BANK_BNAME' => 'bank_name',
	'DB_BANK_BADDRESS' => 'bank_address',
	'DB_BANK_BCOUNTRY' => 'bank_country',
	'DB_BANK_SWIFT' => 'swift',
	'DB_BANK_OBA' => 'online_banking_account',
	'DB_BANK_OBPW' => 'online_banking_pw',
	'DB_BANK_WITHDRAWAL' => 'withdrawal_pw',
	'DB_BANK_STATUS' => 'status',
	'DB_BANK_REMARK' => 'remark',

	//Paypal
	/*
	create table if not exists `3s_paypal`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`pid` varchar(50) default null,
	`password` varchar(50) not null,
	`status` tinyint default 0,
	`remark` varchar(250) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_PAYPAL' => 'paypal', 
	'DB_PAYPAL_ID' => 'id',
	'DB_PAYPAL_PID' => 'pid',
	'DB_PAYPAL_PASSWORD' => 'password',
	'DB_PAYPAL_STATUS' => 'status',
	'DB_PAYPAL_REMARK' => 'remark',

	//seller_account
	/*
	create table if not exists `3s_seller_account`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`platform` varchar(50) not null,
	`main_account` smallint(6) default 0,
	`account` varchar(50) not null,
	`password`  varchar(50) not null,
	`holder_name`  varchar(50) not null,
	`email_id`  smallint(6) default null,
	`bank_id`  smallint(6) default null,
	`same_pholder`  tinyint default 0,
	`address`  varchar(50) not null,
	`tel`  varchar(50) not null,
	`status`  tinyint default 0,
	`question1` varchar(50) default null,
	`answer1` varchar(50) default null,
	`question2` varchar(50) default null,
	`answer2` varchar(50) default null,
	`question3` varchar(50) default null,
	`answer3` varchar(50) default null,
	`remark` varchar(250) default null,
	`ip` varchar(20) default null,
	`used_account` varchar(250) default null,
	`used_holder_name`  varchar(250) default null,
	`used_email_id`  varchar(250) default null,
	`used_address`  varchar(500) default null,
	`used_tel`  varchar(250) default null,
	`used_bank_id`  varchar(250) default null,
	`used_paypal_id`  varchar(250) default null,
	`used_paypal_mail_id`  varchar(250) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_SELLER_ACCOUNT' => 'seller_account', 
	'DB_SELLER_ACCOUNT_ID' => 'id',
	'DB_SELLER_ACCOUNT_PLATFORM' => 'platform',
	'DB_SELLER_ACCOUNT_MACCOUNT' => 'main_account',
	'DB_SELLER_ACCOUNT_ACCOUNT' => 'account',
	'DB_SELLER_ACCOUNT_PASSWORD' => 'password',
	'DB_SELLER_ACCOUNT_HOLDER_NAME' => 'holder_name',
	'DB_SELLER_ACCOUNT_EMAIL_ID' => 'email_id',
	'DB_SELLER_ACCOUNT_BANK_ID' => 'bank_id',
	'DB_SELLER_ACCOUNT_SAME_PHOLDER' => 'same_pholder',
	'DB_SELLER_ACCOUNT_ADDRESS' => 'address', 
	'DB_SELLER_ACCOUNT_TEL' => 'tel',
	'DB_SELLER_ACCOUNT_STATUS' => 'status',
	'DB_SELLER_ACCOUNT_QUESTION1' => 'question1',
	'DB_SELLER_ACCOUNT_ANSWER1' => 'answer1',
	'DB_SELLER_ACCOUNT_QUESTION2' => 'question2',
	'DB_SELLER_ACCOUNT_ANSWER2' => 'answer2',
	'DB_SELLER_ACCOUNT_QUESTION3' => 'question3',
	'DB_SELLER_ACCOUNT_ANSWER3' => 'answer3',
	'DB_SELLER_ACCOUNT_REMARK' => 'remark',
	'DB_SELLER_ACCOUNT_IP' => 'ip',
	'DB_SELLER_ACCOUNT_USED_ACCOUNT' => 'used_account',
	'DB_SELLER_ACCOUNT_USED_HNAME' => 'used_holder_name',
	'DB_SELLER_ACCOUNT_USED_EMAIL' => 'used_email',
	'DB_SELLER_ACCOUNT_USED_TEL' => 'used_tel',
	'DB_SELLER_ACCOUNT_USED_ADDRESS' => 'used_address',
	'DB_SELLER_ACCOUNT_USED_BID' => 'used_bank_id',
	'DB_SELLER_ACCOUNT_USED_PID' => 'used_paypal_id',
	'DB_SELLER_ACCOUNT_USED_PMAIL_ID' => 'used_paypal_mail_id',
	'DB_SELLER_ACCOUNT_USED_CCARD' => 'used_credit_card',

	//related_mark_constant
	'DB_SELLER_ACCOUNT_RELATED_MARK_CONSTANT' =>array('liu_wei','sst_enterprise','wu_guanchen','yu_haolan'),

	//paypal_seller_account
	/*
	create table if not exists `3s_paypal_seller_account`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`paypal_id` smallint(6) not null,
	`seller_account_id` smallint(6) not null
	) engine=myisam default charset=utf8;
	*/
	'DB_PAYPAL_SELLER_ACCOUNT' => 'paypal_seller_account', 
	'DB_PAYPAL_SELLER_ACCOUNT_ID' => 'id',
	'DB_PAYPAL_SELLER_ACCOUNT_PID' => 'paypal_id',
	'DB_PAYPAL_SELLER_ACCOUNT_SAID' => 'seller_account_id',


	//seller_email
	/*
	create table if not exists `3s_seller_email`(
	`id` smallint(6) unsigned primary key not null auto_increment,
	`email` varchar(50) not null,
	`password` varchar(50) not null,
	`tel` varchar(50) default null,
	`paypal_id` smallint(6) default 0,
	`paypal_offset` int(1) default 0,
	`question1` varchar(50) default null,
	`answer1` varchar(50) default null,
	`question2` varchar(50) default null,
	`answer2` varchar(50) default null,
	`question3` varchar(50) default null,
	`answer3` varchar(50) default null,
	`status` tinyint default 0,
	`remark` varchar(250) default null
	) engine=myisam default charset=utf8;
	*/
	'DB_SELLER_EMAIL' => 'seller_email', 
	'DB_SELLER_EMAIL_ID' => 'id',
	'DB_SELLER_EMAIL_EMAIL' => 'email',
	'DB_SELLER_EMAIL_PASSWORD' => 'password', 
	'DB_SELLER_EMAIL_TEL' => 'tel',
	'DB_SELLER_EMAIL_PID' => 'paypal_id',
	'DB_SELLER_EMAIL_POFFSET' => 'paypal_offset',//2 main paypal account, 1 no main paypal account, 0 no paypal account
	'DB_SELLER_EMAIL_QUESTION1' => 'question1',
	'DB_SELLER_EMAIL_ANSWER1' => 'answer1',
	'DB_SELLER_EMAIL_QUESTION2' => 'question2',
	'DB_SELLER_EMAIL_ANSWER2' => 'answer2',
	'DB_SELLER_EMAIL_QUESTION3' => 'question3',
	'DB_SELLER_EMAIL_ANSWER3' => 'answer3',
	'DB_SELLER_EMAIL_STATUS' => 'status',
	'DB_SELLER_EMAIL_REMARK' => 'remark',
	);
?>