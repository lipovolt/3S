<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>美国自建仓入库单</title>
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
		<i class="icon dropdown-s"></i><strong>美国仓库存</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Storage/usstorage');?>"  mark="Outbound">自建仓库存</a></dd>
</dl><!-- 
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>深圳仓库存</strong>								
	</dt>
	<dd><a href="#"  mark="Outbound">深圳仓库存</a></dd>
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
		<i class="icon dropdown-s"></i><strong>greatgoodshop</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index');?>" >美国自建仓销售表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>rc-helcar</strong>								
	</dt>
	<dd><a href="#" >美国万邑通销售表</a></dd>
	<dd><a href="#" >德国万邑通销售表</a></dd>
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
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchaseOrder');?>" >导入采购单</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchaseOrder');?>" >待确认</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchaseOrder');?>" >待付款</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchaseOrder');?>" >待收货</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchaseOrder');?>" >已取消</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>供货商</strong>								
	</dt>
	<dd><a href="<?php echo U('Product/Product/productInfo');?>" >供货商信息</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="#" mark="ussw"><span>美国自建仓</span></a>
		<div class="subnav">
			<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
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
	<dd ><a href="<?php echo U('Ussw/Storage/add');?>">新增库存</a></dd>
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
			<div class="sidenav-hd"><strong>美国自建仓</strong></div>
			<div class="sidenav-bd">
				<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
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
	<dd ><a href="<?php echo U('Ussw/Storage/add');?>">新增库存</a></dd>
</dl>
	
			</div>
		</div>
	<div class="content">
	<div id="inbounds" class="main">
		<div>
			<div class="tab-content">
				<div class="form-group">
					<label for="pQuantity" class="control-label">入库单号 <?php echo ($orderID); ?></label>
				</div>
				
				<table id="tablelist" class="tablelist">
					<tr>
						<th><div class="tl">编号</div></th>
						<th><div class="tl">重量kg</div></th>
						<th><div class="tl">长度cm</div></th>
						<th><div class="tl">宽度cm</div></th>
						<th><div class="tl">高度cm</div></th>
						<th><div class="tl">操作</div></th>
					</tr>
						
					<?php if(is_array($items)): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><form method="POST" id="edit_productInfo" action="<?php echo U('Ussw/Inbound/updateInboundPackage');?>">
						<input type="hidden"  id="<?php echo C('DB_USSW_INBOUND_PACKAGE_ID');?>" name="<?php echo C('DB_USSW_INBOUND_PACKAGE_ID');?>" value="<?php echo ($vo[C('DB_USSW_INBOUND_PACKAGE_ID')]); ?>" />
						<tr>
							<td><div class="tl"><input type="text"  id="<?php echo C('DB_USSW_INBOUND_PACKAGE_NUMBER');?>" name="<?php echo C('DB_USSW_INBOUND_PACKAGE_NUMBER');?>" value="<?php echo ($vo[C('DB_USSW_INBOUND_PACKAGE_NUMBER')]); ?>" style="width:80px;"/></div></td>
							<td><div class="tl"><input type="text"  id="<?php echo C('DB_USSW_INBOUND_PACKAGE_WEIGHT');?>" name="<?php echo C('DB_USSW_INBOUND_PACKAGE_WEIGHT');?>" value="<?php echo ($vo[C('DB_USSW_INBOUND_PACKAGE_WEIGHT')]); ?>" style="width:80px;"/></div></td>
							<td><div class="tl"><input type="text"  id="<?php echo C('DB_USSW_INBOUND_PACKAGE_LENGTH');?>" name="<?php echo C('DB_USSW_INBOUND_PACKAGE_LENGTH');?>" value="<?php echo ($vo[C('DB_USSW_INBOUND_PACKAGE_LENGTH')]); ?>" style="width:80px;"/></div></td>				
							<td><div class="tl"><input type="text"  id="<?php echo C('DB_USSW_INBOUND_PACKAGE_WIDTH');?>" name="<?php echo C('DB_USSW_INBOUND_PACKAGE_WIDTH');?>" value="<?php echo ($vo[C('DB_USSW_INBOUND_PACKAGE_WIDTH')]); ?>" style="width:80px;"/></div></td>
							<td><div class="tl"><input type="text"  id="<?php echo C('DB_USSW_INBOUND_PACKAGE_HEIGHT');?>" name="<?php echo C('DB_USSW_INBOUND_PACKAGE_HEIGHT');?>" value="<?php echo ($vo[C('DB_USSW_INBOUND_PACKAGE_HEIGHT')]); ?>" style="width:80px;"/></div></td>
							<td><div class="tl">
									<button class="btn btn-blue btn-s" id="saveCQuantity">保存</button>
									</div></td>
						</tr>
						</form><?php endforeach; endif; else: echo "" ;endif; ?>
											
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