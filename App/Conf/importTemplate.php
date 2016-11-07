<?php

return array(

	//Product import template
	'IMPORT_PRODUCT' => array(
		'A' => '产品编码', 
		'B' => '中文名称',
		'C' => '英文名称',
		'D' => '价格￥',
		'E' => '发货重量g',
		'F' => '包装长cm',
		'G' => '包装宽cm',
		'H' => '包装高cm',
		'I' => '是否带电',
		'J' => '德国头程方式',
		'K' => '美国头程方式',
		'L' => '美国关税率',
		'M' => '德国关税率',
		'N' => '预计采购用时天',
		'O' => '产品经理',
		'P' => '供货商编号',
		'Q' => '采购链接',
		'R' => 'greatgoodshop自建仓售价$',
		'S' => 'rc-helicar万邑联美国售价$',
		'T' => 'rc-helicar万邑联德国售价€',
		'U' => 'ebay.com销量链接',
		'V' => 'ebay.com最低价链接',
		'W' => 'ebay.de销量链接',
		'X' => 'ebay.de最低价链接',
		),

	//Inbound_item import template
	'IMPORT_INBOUND_ITEM' => array(
		'A' => '补货表编号',
		'B' => '产品编码',
		'C' => '数量',
		),

	//Inbound_item import template
	'IMPORT_INBOUND_PACKAGE' => array(
		'A' => '编号',
		'B' => '重量kg',
		'C' => '长度cm',
		'D' => '宽度cm',
		'E' => '高度cm',
		),

	//Ebay en order tamplate
	'IMPORT_EBAY_EN_ORDER' => array(
		'A' => 'Sales Record Number',
		'B' => 'User Id',
		'C' => 'Buyer Fullname',
		'D' => 'Buyer Phone Number',
		'E' => 'Buyer Email',
		'F' => 'Buyer Address 1',
		'G' => 'Buyer Address 2',
		'H' => 'Buyer City',
		'I' => 'Buyer State',
		'J' => 'Buyer Zip',
		'K' => 'Buyer Country',
		'L' => 'Item Number',
		'M' => 'Item Title',
		'N' => 'Custom Label',
		'O' => 'Quantity',
		'P' => 'Sale Price',
		'Q' => 'Shipping and Handling',
		'R' => 'US Tax',
		'S' => 'Insurance',
		'T' => 'Cash on delivery fee',
		'U' => 'Total Price',
		'V' => 'Payment Method',
		'W' => 'Sale Date',
		'X' => 'Checkout Date',
		'Y' => 'Paid on Date',
		'Z' => 'Shipped on Date',
		'AA' => 'Feedback left',
		'AB' => 'Feedback received',
		'AC' => 'Notes to yourself',
		'AD' => 'PayPal Transaction ID',
		'AE' => 'Shipping Service',
		'AF' => 'Cash on delivery option',
		'AG' => 'Transaction ID',
		'AH' => 'Order ID',
		),

	//Purchase order template
	'IMPORT_PURCHASE' => array(
		'A' => '临时编号',
		'B' => '产品编码',
		'C' => '单价',
		'D' => '数量',
		'E' => '运费',
		'F' => '仓库',
		'G' => '产品经理',
		'H' => '供货商编号',
		'I' => '订单号',
		'J' => '追踪号',
		'K' => '备注',
		),

	//Winit storage template
	'IMPORT_WINIT_STORAGE' => array(
		'A' => '商品编号',
		'B' => '中文名称',
		'C' => '英文名称',
		'D' => '备注',
		'E' => '历史入库',
		'F' => '规格',
		'G' => '可用库存',
		'H' => '待出库',
		'I' => '在途库存',
		'J' => '超时库存',
		'K' => '历史销量',
		'L' => '30天平均日销量',
		'M' => '30天平均库存',
		'N' => 'DOI',
		),

	//Winit storage template
	'IMPORT_WINIT_STORAGE_SHEET' => array(
		0 => '万邑通美西库存表',
		1 => '万邑通德国库存表',
		),

	//update restock shipping status for other warehosue
	'IMPORT_UPDATE_RESTOCK_SHIPPING_STATUS' => array(
		'A' => '补货表编号',
		'B' => '状态', //部分发货，已发货
		),

	//update product packing weight length widht height from winit product template
	'IMPORT_WINIT_PRODUCT' => array(
		'A'=>'编码',
		'B'=>'规格',
		'C'=>'第三方商品编码',
		'D'=>'中文名称',
		'E'=>'英文名称',
		'F'=>'注册重量(kg)',
		'G'=>'重量体积固定',
		'H'=>'注册长(cm)',
		'I'=>'注册宽(cm)',
		'J'=>'注册高(cm)',
		'K'=>'注册体积(CBM)',
		'L'=>'有品牌',
		'M'=>'有电池',
		'N'=>'商品展示页网址',
		'O'=>'备注',
		'P'=>'出口国家',
		'Q'=>'出口申报价值(USD)',
		),
	);
?>