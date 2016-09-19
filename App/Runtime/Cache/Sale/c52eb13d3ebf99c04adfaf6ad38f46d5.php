<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>深圳仓销售建议参数</title>
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
			</a>
<span>
	<i class="icon user"></i>
	<span class="blue"><?= I('session.username',0); ?></span>
	<a class="blue" style="margin:0px 10px;" href="<?php echo U('Index/Index/logout');?>">退出</a>
</span>
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
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>产品信息管理</strong>								
	</dt>
	<dd><a href="<?php echo U('Product/Product/productInfo');?>" >产品信息</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="<?php echo U('Storage/Storage/usstorage');?>" mark="storage"><span>库存</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<strong>美国仓库存</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Storage/usstorage');?>"  mark="Outbound">自建仓库存</a></dd>
	<dd><a href="<?php echo U('Storage/Storage/checkAinventory');?>"  mark="Outbound">检测库存</a></dd>
</dl>
<dl>
	<dt>
		<strong>深圳仓库存</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Storage/szstorage');?>"  mark="Outbound">深圳仓库存</a></dd>
</dl>
<!-- <dl>
	<dt>
		<strong>缺货补货</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Restock/importStorage');?>" >导出缺货表</a></dd>
	<dd><a href="<?php echo U('Storage/Restock/importStorage',array('country'=>'US'));?>" >导出美国缺货表</a></dd>
	<dd><a href="<?php echo U('Storage/Restock/importStorage',array('country'=>'DE'));?>" >导出德国缺货表</a></dd>
	<dd><a href="<?php echo U('Storage/Restock/index');?>" >补货表</a></dd>
</dl> -->

		</div>
	</li>
	<li>
		<a href="<?php echo U('Sale/GgsUsswSale/index');?>" mark="sale"><span>销售</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>基本信息</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/Metadata/index');?>" >基本信息</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>美国自建仓 Ebay Amazon</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSalePlanMetadata');?>" >美国自建仓销售基础表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest');?>" >美国自建仓销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index');?>" >美国自建仓销售表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>万邑通 Ebay</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/WinitUsSale/index');?>" >美国万邑通销售表</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/index');?>" >德国万邑通销售表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>深圳直发 Ebay</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/SzSale/szSalePlanMetadata');?>" >深圳仓销售基础表</a></dd>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usCal');?>" >美国销售表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usSuggest');?>" >美国销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deCal');?>" >德国销售表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deSuggest');?>" >德国销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usTestCal');?>" >新产品美国试算</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deTestCal');?>" >新产品德国试算</a></dd>
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
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待确认'));?>" >待确认</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待发货'));?>" >待发货</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'部分到货'));?>" >部分到货</a></dd>
</dl>
<dl>
	<dt>
		<strong>缺货补货</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Restock/importStorage');?>" >导出缺货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/importStorage',array('country'=>'US'));?>" >导出美国缺货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/importStorage',array('country'=>'DE'));?>" >导出德国缺货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/findSzswOutOfStockItem');?>" >导出深圳缺货表</a></dd>
	<dd><a href="<?php echo U('Purchase/Restock/index');?>" >补货表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>供货商</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Supplier/index');?>" >供货商信息</a></dd>
	<dd><a href="<?php echo U('Purchase/Supplier/addNewSupplier');?>" >添加供货商</a></dd>
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
	<dd ><a href="<?php echo U('Ussw/Outbound/importEbayWso');?>">导入ebay订单</a></dd>
	<dd ><a href="<?php echo U('Ussw/Outbound/index');?>">全部出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Storage/index');?>">库存信息</a></dd>
	<dd ><a href="<?php echo U('Ussw/Storage/awaitingToStop');?>">待下架商品</a></dd>
	<dd ><a href="<?php echo U('Ussw/Storage/stopped');?>">已下架商品</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>邮费管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Postage/firstclass');?>">USPS First Class</a></dd>
	<dd > </dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priorityflatrate');?>">USPS Priority Falt Rate</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priority');?>">USPS Priority</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexSmartPost');?>">Fedex Smart Post</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexHomeDelivery');?>">Fedex Home Delivery</a></dd>
</dl>

		<div>
	</li>
	<li>
		<a href="#" mark="ussw"><span>深圳仓</span></a>
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
		<a href="#" mark="ussw"><span>权限管理</span></a>
		<div class="subnav">
			<dl>
	<dt><strong>用户管理</strong>	</dt>
	<dd><a href="<?php echo U('Admin/Rbac/addUser');?>" >添加用户</a></dd>
	<dd><a href="<?php echo U('Admin/Rbac/index');?>" >用户列表</a></dd>
	<dd><a href="#" >删除用户</a></dd>
	<dd><a href="#" >锁定用户</a></dd>
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
	<dt><strong>权限分配</strong></dt>
	<dd><a href="#" >权限列表</a></dd>
</dl>
		<div>
	</li>
</ul>
			</div>
		</div>
	</div>	
	
    <!-- InstanceBeginEditable name="左边栏" -->
	<div class="area clearfix">
		<div class="sidenav">
			<div class="sidenav-hd"><strong>销售</strong></div>
			<div class="sidenav-bd">
				<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>基本信息</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/Metadata/index');?>" >基本信息</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>美国自建仓 Ebay Amazon</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSalePlanMetadata');?>" >美国自建仓销售基础表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest');?>" >美国自建仓销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index');?>" >美国自建仓销售表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>万邑通 Ebay</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/WinitUsSale/index');?>" >美国万邑通销售表</a></dd>
	<dd><a href="<?php echo U('Sale/WinitDeSale/index');?>" >德国万邑通销售表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>深圳直发 Ebay</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/SzSale/szSalePlanMetadata');?>" >深圳仓销售基础表</a></dd>
	<dd></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usCal');?>" >美国销售表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usSuggest');?>" >美国销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deCal');?>" >德国销售表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deSuggest');?>" >德国销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/usTestCal');?>" >新产品美国试算</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deTestCal');?>" >新产品德国试算</a></dd>
</dl>	
			</div>
		</div>
		<div class="content">
			<div class="tab-content">
				<div class="block-hd">
					<i class="icon import"></i>
					<strong>深圳仓销售建议参数设置</strong>
				</div>
				<table id="tablelist" class="tablelist">
					<tr>
						<th><div class="tl">清货条件（天数）</div></th>
						<th><div class="tl">重新刊登条件（天数）</div></th>
						<th><div class="tl">调整周期（天数）</div></th>
						<th><div class="tl">算法标准周期（天数）</div></th>
						<th><div class="tl">10美元起始利润率</div></th>
						<th><div class="tl">10-20美元起始利润率</div></th>
						<th><div class="tl">20-30美元起始利润率</div></th>
						<th><div class="tl">30-50美元起始利润率</div></th>
						<th><div class="tl">50美元以上起始利润率</div></th>
						<th><div class="tl">售价每次调整百分比</div></th>
						<th><div class="tl">周期销量下限</div></th>
						<th><div class="tl">销量下限替换分母</div></th>
						<th><div class="tl">增长率波动范围±</div></th>
						<th><div class="tl">操作</div></th>
					</tr>
					<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<form method="POST" id="metadataUpdate" action="<?php echo U('Sale/SzSale/updataMetaDate');?>">
							<td><div class="tl">
							<input type="hidden" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_ID');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_ID')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_ID');?>"/>
							<input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD');?>"/></div></td>						
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR1');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_SPR1')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR1');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR2');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_SPR2')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR2');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR3');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_SPR3')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR3');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR4');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_SPR4')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR4');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR5');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_SPR5')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SPR5');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_PCR');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_PCR')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_PCR');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SQNR');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_SQNR')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_SQNR');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR');?>"/></div></td>
							<td><div class="tl"><input type="text" style="width:60px;" name="<?php echo C('DB_SZ_SALE_PLAN_METADATA_GRFR');?>" value="<?php echo ($vo[C('DB_SZ_SALE_PLAN_METADATA_GRFR')]); ?>" id="<?php echo C('DB_SZ_SALE_PLAN_METADATA_GRFR');?>"/></div></td>
							<td><div class="tl"><button class="btn btn-blue btn-s" id="saveMetadataInfo">保存</button></div></td>
						</form>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?> 								
				</table>
				<div class="result page" align="center"><?php echo ($page); ?></div>
			</div>
		</div>
	</div>


	</div>
	</div>
	</div>
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>