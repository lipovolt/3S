<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title></title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" href="__PUBLIC__/Css/base.css">
<link rel="stylesheet" href="__PUBLIC__/Css/zh-cn.css">
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
</head>
<body>

	<div class="header">
		<div class="top">
			<div class="area">
				<span>
					<a class="logo" href="__PUBLIC__/index.php">
	<img src="__PUBLIC__/Images/logo.png" alt="">
	<img src="__PUBLIC__/Images/slogan.png">
</a>
<span>
	<i class="icon user"></i>
	<span class="blue"><?= I('session.username',0); ?></span>
	<a class="blue" style="margin:0px 10px;" href="<?php echo U('Index/Index/logout');?>">退出</a>
</span>
<div class="droplist" style="top:-1px;">
	<div class="label" id='messagerieNum' style="padding-right:20px;border:none;background:none;box-shadow:none;">
		<?php if(I('session.username',0) == '张旻'): ?><a class="blue"  href="<?php echo U('Todo/Todo/index',['keyword'=>'person','keywordValue'=>'Jade']);?>">待办<font style="color:#F00;"><b> <?php echo ($todoQuantity); ?></b></font></a>
		  <?php elseif(I('session.username',0) == 'admin'): ?>
		  	<a class="blue"  href="<?php echo U('Todo/Todo/index',['keyword'=>'person','keywordValue'=>'张昱']);?>">待办<font style="color:#F00; font-size: 16px"><b> <?php echo ($todoQuantity); ?></b></font></a>
		  <?php else: ?>
		  	<a class="blue"  href="<?php echo U('Todo/Todo/index',['keyword'=>'person','keywordValue'=>I('session.username',0)]);?>">待办<font style="color:#F00;"><b> <?php echo ($todoQuantity); ?></b></font></a><?php endif; ?>		
	</div>
	<div id='messagerieList'></div>
</div>
			</div>
		</div>		
		<div class="nav">
			<div class="area">
				<ul class="mainnav">
	<li>
		<a href="<?php echo U('Product/Product/productInfo');?>" mark="products"><span>产品管理</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>导入产品</strong>								
	</dt>
	<dd><a href="<?php echo U('Product/Product/productBatchAdd');?>" >导入产品</a></dd>
	<dd><a href="<?php echo U('Product/Product/updateWinitProductList');?>" >导入万邑通产品信息</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>产品信息管理</strong>								
	</dt>
	<dd><a href="<?php echo U('Product/Product/productInfo');?>" >产品信息</a></dd>
	<dd><a href="<?php echo U('Product/Product/productPackRequirement');?>" >产品包装要求</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="#" mark="sale"><span>销售</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Ebay ggs 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/fileExchange',array('market'=>'ebay','account'=>'greatgoodshop'));?>" >File exchange</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/exportEbayBulkDiscount',array('account'=>'greatgoodshop'));?>" >Bulk Discount</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest',array('account'=>'greatgoodshop'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index',array('account'=>'greatgoodshop'));?>" >销售表</a></dd>
	
</dl>
<!-- <dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Ebay blackfive 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/fileExchange',array('market'=>'ebay','account'=>'blackfive'));?>" >File exchange</a></dd>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest',array('account'=>'blackfive'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index',array('account'=>'blackfive'));?>" >销售表</a></dd>
	
</dl> -->
<!-- <dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Ebay rc 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/WinitUsSale/index');?>" >美国销售表</a></dd>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/fileExchange',array('market'=>'ebay','account'=>'rc-helicar'));?>" >德国站 File exchange</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/exportDeEbayBulkDiscount',array('account'=>'rc-helicar'));?>" >Bulk Discount</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/suggest',array('account'=>'rc-helicar'));?>" >德国销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/index',array('account'=>'rc-helicar'));?>" >德国销售表</a></dd>
</dl> -->
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Ebay rc 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/SzSale/fileExchange',array('market'=>'ebay','account'=>'rc-helicar'));?>" >File exchange</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/exportUsEbayBulkDiscount',array('account'=>'rc-helicar'));?>" >Bulk Discount</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/suggest',array('account'=>'rc-helicar','country'=>'us'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/index',array('account'=>'rc-helicar','country'=>'us'));?>" >销售表</a></dd>
	<!-- <dd><a href="<?php echo U('Sale/SzSale/suggest',array('account'=>'rc-helicar','country'=>'de'));?>" >德国销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/index',array('account'=>'rc-helicar','country'=>'de'));?>" >德国销售表</a></dd> -->
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Ebay 816 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/WinitDeSale/fileExchange',array('market'=>'ebay','account'=>'yzhan-816'));?>" >File exchange</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/exportDeEbayBulkDiscount',array('account'=>'yzhan-816'));?>" >Bulk Discount</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/suggest',array('account'=>'yzhan-816'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/index',array('account'=>'yzhan-816'));?>" >销售表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Amazon lipovolt 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/fileExchange',array('market'=>'amazon','account'=>'lipovolt'));?>" >File exchange</a></dd>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest',array('account'=>'lipovolt'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index',array('account'=>'lipovolt'));?>" >销售表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Amazon shangsitech@qq.com 销售</strong>					
	</dt>
	<dd><a href="<?php echo U('Sale/WinitDeSale/fileExchange',array('market'=>'amazon','account'=>'shangsitech@qq.com'));?>" >File exchange</a></dd>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/suggest',array('account'=>'shangsitech@qq.com'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/index',array('account'=>'shangsitech@qq.com'));?>" >销售表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>Groupon Lipovolt 销售</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/fileExchange',array('market'=>'groupon','account'=>'g-lipovolt'));?>" >File exchange</a>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest',array('account'=>'g-lipovolt'));?>" >销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index',array('account'=>'g-lipovolt'));?>" >销售表</a></dd></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>屏蔽买家</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/BlockBuyer/index');?>" >被屏蔽买家列表</a>
	<dd><a href="<?php echo U('Sale/BlockBuyer/addBlockBuyer');?>" >添加屏蔽买家</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>客服模板</strong>		
	</dt>
	<dd><a href="<?php echo U('Sale/EmailTemplate/index',array('language'=>'de'));?>" >德文客服模板</a>
	<dd><a href="<?php echo U('Sale/EmailTemplate/index',array('language'=>'en'));?>" >英文客服模板</a></dd>
</dl>

		</div>
	</li>
	<li>
		<a href="<?php echo U('Purchase/Purchase/index');?>" mark="purchase"><span>采购</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>采购单</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchase');?>" >导入采购单</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/newPurchaseOrder');?>" >新增采购单</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待确认'));?>" >待确认</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待发货'));?>" >待发货</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'部分到货'));?>" >部分到货</a></dd>
</dl>
<dl>
	<dt>
		<strong>缺货补货</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Restock/findUsOutOfStockItem',array('start'=>0,'end'=>1500));?>" onclick="if(confirm('是否已经上传亚马逊FBA库存，FBA出库单，万邑通美西仓库存？如果没有上传这两个仓库的缺货建议不准确！')){return true;}else{return false;	}">导出美国缺货表1</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/findUsOutOfStockItem',array('start'=>1501,'end'=>-1));?>" onclick="if(confirm('是否已经上传亚马逊FBA库存，FBA出库单，万邑通美西仓库存？如果没有上传这两个仓库的缺货建议不准确！')){return true;}else{return false;	}">导出美国缺货表2</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/findDeOutOfStockItem');?>"  onclick="if(confirm('是否已经上传万邑通德国仓库存？')){return true;}else{return false;	}">导出德国缺货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/findSzswOutOfStockItem');?>" >导出深圳缺货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/index');?>" >补货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/editRestockPara');?>" >编辑补货参数</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>供货商</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Supplier/index');?>" >供货商信息</a></dd>
	<dd><a href="<?php echo U('Purchase/Supplier/addNewSupplier');?>" >添加供货商</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>试算</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/winitDeItemTest');?>" >万邑通德国仓试算</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usTestCal');?>" >深圳直发美国试算</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deTestCal');?>" >深圳直发德国试算</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="#" mark="ussw"><span>美国自建仓</span></a>
		<div class="subnav">
			<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Inbound/singleItemInbound');?>">单品入库</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/index');?>"  mark="Outbound">全部入库单</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/createInboundOrder');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>出库管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Outbound/outbound');?>">单品出库</a></dd>
	<dd ><a href="<?php echo U('Ussw/Outbound/importUsswOutboudOrder');?>">导入出库单</a></dd>
	<dd ><a href="<?php echo U('Ussw/Outbound/index');?>">全部出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Storage/index');?>">库存信息</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>amazon FBA 库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/AmazonUsStorage/index');?>">amazon FBA 库存信息</a></dd>
	<dd ><a href="<?php echo U('Ussw/AmazonUsStorage/outboundOrders');?>">amazon FBA 出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>邮费管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Postage/firstclass');?>">USPS First Class</a></dd>
	<dd > </dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priorityflatrate');?>">USPS Priority Flat Rate</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priority');?>">USPS Priority</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexSmartPost');?>">Fedex Smart Post</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexHomeDelivery');?>">Fedex Home Delivery</a></dd>
</dl>

		<div>
	</li>
	<li>
		<a href="#" mark="szw"><span>深圳仓</span></a>
		<div class="subnav">
			<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
	<dd ><a href="<?php echo U('Szsw/Inbound/simpleInbound');?>">单品入库</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>出库管理</strong></dt>
	<dd ><a href="<?php echo U('Szsw/Outbound/simpleOutbound');?>">单品出库</a></dd>
	<dd ><a href="<?php echo U('Szsw/Outbound/importEbayOrders');?>">导入ebay订单</a></dd>
	<dd ><a href="<?php echo U('Szsw/Outbound/index');?>">全部出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Szsw/Storage/index');?>">库存信息</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>邮费管理</strong></dt>
	<dd ><a href="<?php echo U('Szsw/Postage/eub');?>">EUB</a></dd>
	<dd ></dd>
	<dd ><a href="<?php echo U('Szsw/Postage/cpc');?>">中邮分区</a></dd>
	<dd ><a href="<?php echo U('Szsw/Postage/cpf');?>">中邮运费</a></dd>
</dl>

		<div>
	</li>
	<li>
		<a href="#" mark="winit"><span>万邑通仓</span></a>
		<div class="subnav">
			<dl>
	<dt><i class="icon dropdown-s"></i><strong>万德库存库存管理</strong></dt>
	<dd ><a href="<?php echo U('Winit/Storage/index');?>">万德库存信息</a></dd>
	<dd ><a href="<?php echo U('Winit/Storage/importWinitDeStorage');?>">导入Winit DE 库存</a></dd>
	<dd ><a href="<?php echo U('Winit/Storage/winitDtSimpleIn');?>">万德退货入库</a></dd>
	<dd ><a href="<?php echo U('Winit/Storage/winitDtSimpleOut');?>">万德退货出库</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>万美库存库存管理</strong></dt>
	<dd ><a href="<?php echo U('Winit/Storage/wuIndex');?>">万美库存信息</a></dd>
	<dd ><a href="<?php echo U('Winit/Storage/importWinitUsStorage');?>">导入Winit US 库存</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>出库管理</strong></dt>
	<dd ><a href="<?php echo U('Winit/Outbound/convertToWinitOutboundFile');?>">ebay订单转换成winit出库文件</a></dd>
	<dd ><a href="<?php echo U('Winit/Outbound/importWinitOutboundOrder');?>">导入Winit出库单</a></dd>
	<dd ><a href="<?php echo U('Winit/Outbound/index');?>">全部出库单</a></dd>
</dl>

		<div>
	</li>
	<li>
		<a href="#" mark="todo"><span>待办事项</span></a>
		<div class="subnav">
			<script type="text/javascript">
function att(){
	var date = new Date();
	var strDate=date.getFullYear()+'-'+add_zero(date.getMonth()+1)+'-'+add_zero(date.getDate());
	location.href="<?php echo U('Todo/Todo/attendance');?>?actualDate="+strDate;
}
function attManage(){
	var date = new Date();
	var strDate=date.getFullYear()+'-'+add_zero(date.getMonth()+1)+'-'+add_zero(date.getDate());
	location.href="<?php echo U('Todo/Todo/attendanceAll');?>?actualDate="+strDate;
}
function add_zero(temp){ 
 if(temp<10) 
 	return "0"+temp; 
 else 
 	return temp; 
}
</script>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>待办事项</strong>								
	</dt>
	<dd><a href="<?php echo U('Todo/Todo/index');?>" >事项列表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>考勤</strong>								
	</dt>
	<dd><a href="javascript:void(0)" onclick="att();" >考勤</a></dd>
	<dd><a href="javascript:void(0)" onclick="attManage();" >考勤管理</a></dd>
	<dd><a href="<?php echo U('Todo/Todo/attStatistic');?>">考勤统计</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>绩效考核</strong>								
	</dt>
	<dd><a href="<?php echo U('Kpi/Kpi/index');?>" >绩效统计表</a></dd>
	<dd><a href="<?php echo U('Kpi/Kpi/kpiSale');?>" >销售绩效表</a></dd>
	<dd><a href="<?php echo U('Kpi/Kpi/kpiStorage');?>" >包验货绩效表</a></dd>
	<dd><a href="<?php echo U('Kpi/Kpi/kpiCustomer');?>" >客服绩效表</a></dd>
</dl>
		<div>
	</li>
	<li>
		<a href="#" mark="accounting"><span>财务</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>收支</strong>								
	</dt>
	<dd><a href="<?php echo U('Accounting/Accounting/incomeCost');?>" >收入成本表</a></dd>
	<dd><a href="<?php echo U('Accounting/Accounting/saleFee');?>" >销售费用表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>管理费用</strong>								
	</dt>
	<dd><a href="<?php echo U('Accounting/Accounting/managementFee');?>" >管理费用表</a></dd>
	<dd><a href="<?php echo U('Accounting/Accounting/wages');?>" >工资表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>财务统计</strong>								
	</dt>
	<dd><a href="<?php echo U('Accounting/Accounting/profitStatistic');?>" >利润分析</a></dd>
	<dd><a href="<?php echo U('Accounting/Accounting/importSaleRecord');?>" >导入平台销售记录</a></dd>
</dl>
		<div>
	</li>
	<li>
		<a href="#" mark="rabc"><span>权限管理</span></a>
		<div class="subnav">
			<dl>
	<dt><strong>用户管理</strong>	</dt>
	<dd><a href="<?php echo U('Admin/Rbac/addUser');?>" >添加用户</a></dd>
	<dd><a href="<?php echo U('Admin/Rbac/index');?>" >用户列表</a></dd>
</dl>
<dl>
	<dt><strong>角色管理</strong>	</dt>
	<dd><a href="<?php echo U('Admin/Rbac/addRole');?>" >添加角色</a></dd>
	<dd><a href="<?php echo U('Admin/Rbac/role');?>" >角色列表</a></dd>
	<dd><a href="#" >删除角色</a></dd>
	<dd><a href="#" >锁定角色</a></dd>
</dl>
<dl>
	<dt><strong>节点管理</strong></dt>
	<dd><a href="<?php echo U('Admin/Rbac/addNode');?>" >添加节点</a></dd>
	<dd><a href="<?php echo U('Admin/Rbac/node');?>" >节点列表</a></dd>
</dl>
<dl>
	<dt><strong>销售管理</strong></dt>
	<dd><a href="<?php echo U('Sale/Metadata/index');?>">基础销售参数</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/usSalePlanMetadata');?>" >美国销售参数表</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/szSalePlanMetadata');?>" >深圳销售参数表</a></dd>
</dl>
<dl>
	<dt><strong>账号管理</strong></dt>
	<dd><a href="<?php echo U('Sale/Metadata/email');?>">邮箱</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/newEmail');?>">添加邮箱</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/bank');?>" >银行</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/newBank');?>">添加银行</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/paypal');?>" >Paypal</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/newPaypal');?>">添加Paypal</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/sellerAccount');?>">销售账号</a></dd>
	<dd><a href="<?php echo U('Sale/Metadata/newSellerAccount');?>">添加销售账号</a></dd>
</dl>
<!-- <dl>
	<dt><strong>权限分配</strong></dt>
	<dd><a href="#" >权限列表</a></dd>
</dl> -->
		<div>
	</li>
</ul>
			</div>
		</div>
	</div>	
	<div class="area clearfix">
		<!-- 左边栏 -->
		<div class="sidenav">
			<div class="sidenav-hd"><strong>美国自建仓</strong></div>
			<div class="sidenav-bd">
				<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Inbound/singleItemInbound');?>">单品入库</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/index');?>"  mark="Outbound">全部入库单</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/createInboundOrder');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>出库管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Outbound/outbound');?>">单品出库</a></dd>
	<dd ><a href="<?php echo U('Ussw/Outbound/importUsswOutboudOrder');?>">导入出库单</a></dd>
	<dd ><a href="<?php echo U('Ussw/Outbound/index');?>">全部出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Storage/index');?>">库存信息</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>amazon FBA 库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/AmazonUsStorage/index');?>">amazon FBA 库存信息</a></dd>
	<dd ><a href="<?php echo U('Ussw/AmazonUsStorage/outboundOrders');?>">amazon FBA 出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>邮费管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Postage/firstclass');?>">USPS First Class</a></dd>
	<dd > </dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priorityflatrate');?>">USPS Priority Flat Rate</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priority');?>">USPS Priority</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexSmartPost');?>">Fedex Smart Post</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexHomeDelivery');?>">Fedex Home Delivery</a></dd>
</dl>
	
			</div>
		</div>
	<div class="content">
			<script>
			var GlobalData = {
				_COMMON_DATA_PROCESSING_ : "数据处理中...",
				_PRODUCTINFO_BATCHADD_PRODUCTIMPORT_ERROR_FILE_TYPE_NOT_MATCH_:"产品信息导入只支持XLS格式文件!",
				_COMMON_PLEASE_SELECT_FILE_:"请选择文件！"
			}
			</script>
			
			<!-- 主页面开始  -->
				<div id="ProductInfo" class="main">
					<ul class="tab">
						<li class="on">
						<i class="before"></i>
							<span>入库单号 <?php echo ($orderID); ?></span>
						<i class="after"></i>
						</li>					
					</ul>
					<div class="tab-content">
						<div class="tab-inner-content" style="">
							<div class="block">
								<div class="block-hd">
									<i class="icon import"></i>
									<strong>批量导入装箱信息</strong>
								</div>
								<div class="block-bd">
									<div class="block-indent" style="overflow:hidden;">
										<div style="float:left;width:456px;">
											<p>请<a href="http://www.lipovolt.com/3s/Download/Import_Inbound_Package_Template.xls">点击此处</a>下载产品导入模板，填写产品信息完成后，上传数据。</p>
											<div>
												 <form action="<?php echo U('Ussw/Inbound/addPackage',array('orderID'=>$orderID));?>" method="post" enctype="multipart/form-data">
										            <input type="file" name="import"/>
								          			<input type="hidden" name="table" value="tablename"/>
									             	<input type="submit" value="导入"/>
									         	</form>

											</div>
											<p>
												<span class="notice-s">产品信息导入只支持XLS格式文件</span>
											</p>
										</div>										
									</div>															
								</div>
							</div>
						</div>
					</div>
			<!-- 主页面结束 -->
		</div>
	</div>
	</div>
		
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>