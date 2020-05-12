<?php

return array(

	//general
	'USSW' => '美自建仓',
	'winit_de_warehouse' => '万邑通德国',
	'winit_uswc_warehouse' => '万邑通美西',
	'SZSW' => '深圳仓',

	'美自建仓' => 'USSW',
	'万邑通德国' => 'winit_de_warehouse',
	'万邑通美西' => 'winit_uswc_warehouse' ,
	'深圳仓' => 'SZSW',

	'WAREHOUSE' =>array(
		'USSW',
		'winit_de_warehouse',
		'winit_uswc_warehouse',
		'SZSW',
		),


	//kpi parameters
	'kpi_pqm' => 8, //产品开发每月最低数量
	'kpi_pqs' => 12, //产品开发每月标准数量
	'kpi_scqm' => 4, //销售每月最低有效清货数量
	'kpi_scqs' => 4, //销售每月标准有效清货数量
	'kpi_sc1_squantity' => 3,//有效清货起始库存数量段1
	'kpi_sc1_day' => 30,//有效清货起始库存数量段1清货时间要求
	'kpi_sc2_squantity' => 10,//有效清货起始库存数量段2
	'kpi_sc2_day' => 60,//有效清货起始库存数量段2清货时间要求
	'kpi_sc3_day' => 90,//有效清货起始库存数量段3清货时间要求
	'kpi_srqm' => 4,//销售每月最低有效重新刊登数量
	'kpi_srqs' => 8,//销售每月标准有效重新刊登数量
	'kpi_smq' => 3,//仓储每月最多错误数
	'kpi_syq' => 30,//仓储年最多错误数
	'kpi_srmsq' => 3,//销售有效重新刊登周期最低销量
	'kpi_srsap' => 0.1, //销售有效重新刊登周期最低利润率


	//login
	'LOGIN_TITLE' => '用户登录',
	'LOGIN_FORM_TITLE' => '用户登录',
	'LOGIN_LABEL_USERNAME' => '用户名',
	'LOGIN_LABEL_PASSWORD' => '密码',
	'LOGIN_LABEL_CODE' => '验证码',
	'LOGIN_CHANGE_CODE' => '看不清',
	'LOGIN_BTN_SUBMIT' => '提交',

	//our of stock
	'EXPORT_OOS_WAREHOUSE' => '仓库',
	'EXPORT_OOS_SKU' => '产品编码',
	'EXPORT_OOS_QUANTITY' => '数量',
	'EXPORT_OOS_MANAGER' => '产品经理',
	'EXPORT_OOS_DATE' => '日期',
	'EXPORT_OOS_USSW' => '日期',

	//product manager en name
	'KPI_STATISTIC_TYPE' =>array(
		'new_item_quantity' => '新产品数量',
		'relisting' => '重新刊登',
		'clear' => '清货',
		'mistake' => '错误',
		'customer_performance' => '客服绩效',
		),

	//product manager en name
	'PRODUCT_MANAGER_NAME' =>array(
		'Yangtze' => '孙志磊',
		'Yellow River' => '张昱',
		'Pearl River' => '文学',
		),

	//product manager cn name
	'PRODUCT_MANAGER_ENAME' =>array(
		'孙志磊' => 'Yangtze',
		'张昱' => 'Yellow River',
		'文学' => 'Pearl River',
		),

	'DEPARTMENT' => array(
		'product'=>'产品开发',
		'sale'=>'销售',
		'storage'=>'仓库',
		'customer'=>'客服',
		),

	'POSITION' => array(
		'supervisor'=>'主管',
		'staff'=>'员工',
		),

	//paypal ebay fee
	'PLATFORM_FEE' => array(
		'Paypal_US_Percent' => 0.029,
		'Paypal_US_Base' => 0.3,
		'Paypal_CN_Percent' => 0.044,
		'Paypal_CN_Base' => 0.35,
		'Paypal_CN_Small_Percent' => 0.06,
		'Paypal_CN_Small_Base' => 0.05,
		'Ebay_US_Percent' => 0.1,
		'Ebay_US_Shop' => 59.95,
		'Ebay_CN_Percent' => 0.1,
		'Ebay_CN_Shop' => 59.95,
		'Amazon_US_Percent' => 0.15,
		'Amazon_US_Shop' => 39.99,
		'Grounpon_US_Percent' => 0.15,
		'Grounpon_US_Shop' => 0,
		'Wish_CN_Percent' => 0.15,
		'Wish_CN_Shop' => 0,
		),
	
	//Noon break
	'NOON_BREAK' => array(
		'张昱' => 1,
		'孙志磊' => 1,
		'文学' => 1,
		'王朝金' => 1,
		),

	//performace percent
	'WAGES_PERFORMANCE_PERCENT' => array(
		'孙志磊' => 1,
		'张旻' => 0,
		'文学' => 1,
		),

	//wages base
	'WAGES_BASE' => array(
		'张昱' => 6500,
		'张旻' => 2000,
		'孙志磊' => 4000,
		'文学' => 4000,
		'王朝金' => 4000,
		),

	//cooperate sellerID
	'COOPERATE_SELLERID' => array(
		0 => 'greatgoodshop',
		1 => 'blackfive',
		2 => 'lipovolt',
		3 => 'g-lipovolt',
		),

	//personal sellerID
	'PERSONAL_SELLERID' => array(
		0 => 'rc-helicar',
		1 => 'vtkg5755',
		2 => 'yzhan-816',
		3 => 'shangsitech@qq.com',
		),

	//Share type
	'SHARE_TYPE' => array(
		0 => 'cooperate',
		1 => 'personal',
		2 => 'all',
		),

	//Share type cnmae
	'SHARE_TYPE_CNAME' => array(
		'cooperate'=>'合作账号',
		'personal'=>'个人账号',
		'all'=>'全部账号',
		),

	'MANAGEMENT_PURPOSE' => array(
		0 => 'rent',
		1 => 'packing',
		2 => 'booking',
		3 => 'purchasing_shipping',
		4 => 'other',
		),

	'MANAGEMENT_PURPOSE_CNAME' => array(
		'rent'=>'房租',
		'packing'=>'包材',
		'booking'=>'代理记账',
		'purchasing_shipping'=>'采购快递费',
		'other'=>'其他',
		),

	//profit statistic subject row number
	'PROFIT_STATISTIC_SUBJECT_ROW' => array(
		'title1' => 0,
		'title2' => 1,
		'income_title' => 2,
		'income_usd' => 3,
		'income_eur' => 4,
		'income_rmb' => 5,
		'item_cost' => 6,
		'sale_fee_total' => 7,
		'sale_fee_share_percent' => 8,
		'sale_fee_detail_title' => 9,
		'firstsf_storage' => 10,
		'firstsf_storage_sfee' => 11,
		'local_sf' => 12,
		'local_sf_sfee' => 13,
		'packing_fee' => 14,
		'packing_fee_sfee' => 15,
		'tariff' => 16,
		'tariff_sfee' => 17,
		'platform_fee' => 18,
		'management_title' => 19,
		'management_percent' => 20,
		'management_sfee' => 21,
		'management_detail_title' => 22,
		'management_rent' => 23,
		'managment_wages' => 24,
		'managment_booking' => 25,
		'managment_other' => 26,
		'statistic_profit' => 27,
		'statistic_analysis' => 28,
		'statistic_gross_profit_rate' => 29,
		'statistic_net_profit_rate' => 30,
		'statistic_sale_fee_income_rate' => 31,
		'statistic_management_fee_income_rate' => 32,
		),
	);
?>