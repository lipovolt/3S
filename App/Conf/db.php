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
	'DB_PRODUCT_BATTERY' => 'battery',
	'DB_PRODUCT_TODE' => 'tode',
	'DB_PRODUCT_TOUS' => 'tous',
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
	'DB_USSW_INBOUND' => 'ussw_inbound',
	'DB_USSW_INBOUND_ID' => 'ussw_inbound_id',
	'DB_USSW_INBOUND_DATE' => 'ussw_inbound_date',
	'DB_USSW_INBOUND_SHIPPING_WAY' => 'ussw_inbound_shipping_way',
	'DB_USSW_INBOUND_DPQ' => 'ussw_inbound_dpq', //DPQ=declared package quantity
	'DB_USSW_INBOUND_CPQ' => 'ussw_inbound_cpq',//DPQ=confirmed package quantity
	'DB_USSW_INBOUND_PACKAGE_WEIGHT' => 'ussw_inbound_package_weight',
	'DB_USSW_INBOUND_STATUS' => 'ussw_inbound_status',

	//ussw_inbound_package
	'DB_USSW_INBOUND_PACKAGE' => 'ussw_inbound_package',
	'DB_USSW_INBOUND_PACKAGE_ID' => 'ussw_inbound_package_id',
	'DB_USSW_INBOUND_PACKAGE_IOID' => 'ussw_inbound_package_ioid',
	'DB_USSW_INBOUND_PACKAGE_WEIGHT' => 'ussw_inbound_package_weight',
	'DB_USSW_INBOUND_PACKAGE_LENGTH' => 'ussw_inbound_package_length',
	'DB_USSW_INBOUND_PACKAGE_WIDTH' => 'ussw_inbound_package_width',
	'DB_USSW_INBOUND_PACKAGE_HEIGHT' => 'ussw_inbound_package_height',
	'DB_USSW_INBOUND_SHIPPING_WAY' => 'ussw_inbound_shipping_way',
	

	//ussw_inbound_item
	'DB_USSW_INBOUND_ITEM' => 'ussw_inbound_item',
	'DB_USSW_INBOUND_ITEM_ID' => 'ussw_inbound_item_id',
	'DB_USSW_INBOUND_ITEM_IOID' => 'ussw_inbound_item_ioid',
	'DB_USSW_INBOUND_ITEM_SKU' => 'ussw_inbound_item_sku',
	'DB_USSW_INBOUND_ITEM_DQUANTIY' => 'ussw_inbound_item_dquantity',
	'DB_USSW_INBOUND_ITEM_CQUANTIY' => 'ussw_inbound_item_cquantity',

	//ussw_outbound
	'DB_USSW_OUTBOUND' => 'ussw_outbound',
	'DB_USSW_OUTBOUND_ID' => 'ussw_outbound_id',
	'DB_USSW_OUTBOUND_MARKET' => 'ussw_outbound_market',
	'DB_USSW_OUTBOUND_MARKET_NO' => 'ussw_outbound_market_no',
	'DB_USSW_OUTBOUND_STATUS' => 'ussw_outbound_status',
	'DB_USSW_OUTBOUND_SHIPPING_COMPANY' => 'ussw_outbound_shipping_company',
	'DB_USSW_OUTBOUND_SHIPPING_WAY' => 'ussw_outbound_shipping_way',
	'DB_USSW_OUTBOUND_TRACKING_NUMBER' => 'ussw_outbound_tracking_number',
	'DB_USSW_OUTBOUND_CREATE_TIME' => 'ussw_outbound_create_time',
	'DB_USSW_OUTBOUND_BUYER_ID' => 'ussw_outbound_buyer_id',
	'DB_USSW_OUTBOUND_BUYER_NAME' => 'ussw_outbound_buyer_name',
	'DB_USSW_OUTBOUND_BUYER_TEL' => 'ussw_outbound_buyer_tel',
	'DB_USSW_OUTBOUND_BUYER_EMAIL' => 'ussw_outbound_buyer_email',
	'DB_USSW_OUTBOUND_BUYER_ADDRESS1' => 'ussw_outbound_buyer_address1',
	'DB_USSW_OUTBOUND_BUYER_ADDRESS2' => 'ussw_outbound_buyer_address2',
	'DB_USSW_OUTBOUND_BUYER_CITY' => 'ussw_outbound_buyer_city',
	'DB_USSW_OUTBOUND_BUYER_STATE' => 'ussw_outbound_buyer_state',
	'DB_USSW_OUTBOUND_BUYER_COUNTRY' => 'ussw_outbound_buyer_country',
	'DB_USSW_OUTBOUND_BUYER_ZIP' => 'ussw_outbound_buyer_ZIP',

	//ussw_outbound_item
	'DB_USSW_OUTBOUND_ITEM' => 'ussw_outbound_item',
	'DB_USSW_OUTBOUND_ITEM_OOID' => 'ussw_outbound_item_ooid',
	'DB_USSW_OUTBOUND_ITEM_SKU' => 'ussw_outbound_item_sku',
	'DB_USSW_OUTBOUND_ITEM_POSITION' => 'ussw_outbound_item_position',
	'DB_USSW_OUTBOUND_ITEM_QUANTITY' => 'ussw_outbound_item_quantity',
	'DB_USSW_OUTBOUND_ITEM_MARKET_NO' => 'ussw_outbound_item_market_no',
	'DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO' => 'ussw_outbound_item_transaction_no',
	);
?>