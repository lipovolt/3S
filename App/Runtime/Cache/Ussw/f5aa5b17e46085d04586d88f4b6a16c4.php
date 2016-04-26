<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Untitled Document</title>
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
	<dd><a href="<?php echo U('Storage/Storage/checkAinventory');?>"  mark="Outbound">检测库存</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>深圳仓库存</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Storage/szstorage');?>"  mark="Outbound">深圳仓库存</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>缺货补货</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Restock/importStorage');?>" >导出缺货表</a></dd>
	<dd><a href="<?php echo U('Storage/Restock/index');?>" >补货表</a></dd>
</dl>

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
	<dd><a href="<?php echo U('Purchase/Purchase/importPurchase');?>" >导入采购单</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待确认'));?>" >待确认</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待付款'));?>" >待付款</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'待发货'));?>" >待发货</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_CANCEL')=>1));?>" >已取消</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>供货商</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Supplier/index');?>" >供货商信息</a></dd>
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
</dl>

		<div>
	</li>
</ul>
			</div>
		</div>
	</div>	
	<div class="area clearfix">
		<!-- 左边栏 -->
		<div class="sidenav">
			<div class="sidenav-hd"><strong>美国库存管理</strong></div>
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
	<dd ><a href="<?php echo U('Ussw/Outbound/importEbayWso');?>">导入ebay订单</a></dd>
	<dd ><a href="<?php echo U('Ussw/Outbound/index');?>">全部出库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Storage/index');?>">库存信息</a></dd>
</dl>
	
			</div>
		</div>
	<div class="content">
	<div id="ProductInfo" class="main">
		<form method="POST" id="edit_productInfo" action="<?php echo U('Ussw/Storage/update');?>">
		<div class="block-outer BaseInfo">
			<div class="block-outer-hd"><strong>基本信息</strong></div>
			<div class="block-outer-bd">
				<div class="inline-block block-indent">
					<div class="item">
						<div class="form-group">
							<label for="position" class="control-label">货位</label>
							<input type="hidden"  id="<?php echo C('DB_USSTORAGE_ID');?>" name="<?php echo C('DB_USSTORAGE_ID');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_ID')]); ?>"/>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_USSTORAGE_POSITION');?>" name="<?php echo C('DB_USSTORAGE_POSITION');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_POSITION')]); ?>"/>
							</div>
						</div>
						<div class="form-group">
							<label for="sku" class="control-label">产品编码</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_USSTORAGE_SKU');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_SKU')]); ?>" id="<?php echo C('DB_USSTORAGE_SKU');?>" />
							</div>
						</div>
					</div>
					<div class="item">
						<div class="form-group">
							<label for="cname" class="control-label">中文名称</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_USSTORAGE_CNAME');?>" id="<?php echo C('DB_USSTORAGE_CNAME');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_CNAME')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="ename" class="control-label">英文名称</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_USSTORAGE_ENAME');?>" id="<?php echo C('DB_USSTORAGE_ENAME');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_ENAME')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="attribute" class="control-label">属性</label>
							<div class="control-wrap">
								<input type="text"  id="attributeValue" name="attributeValue" value="<?php echo ($usstorage[0]['attribute']); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="csales" class="control-label">累计销量</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_USSTORAGE_CSALES');?>" name="<?php echo C('DB_USSTORAGE_CSALES');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_CSALE')]); ?>" />
							</div>
						</div>
					</div>
                    <div class="item">
                        <div class="form-group">
							<label for="cinventory" class="control-label">累计入库</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_USSTORAGE_CINVENTORY');?>" name="<?php echo C('DB_USSTORAGE_CINVENTORY');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_CINVENTORY')]); ?>" />
							</div>
						</div>
                        <div class="form-group">
							<label for="ainventory" class="control-label">可用数量</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_USSTORAGE_AINVENTORY');?>" name="<?php echo C('DB_USSTORAGE_AINVENTORY');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_AINVENTORY')]); ?>" />
							</div>
						</div>
                    </div>
                    <div class="item">
                        <div class="form-group">
							<label for="oinventory" class="control-label">待出库</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_USSTORAGE_OINVENTORY');?>" name="<?php echo C('DB_USSTORAGE_OINVENTORY');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_OINVENTORY')]); ?>" />
							</div>
						</div>
                        <div class="form-group">
							<label for="iinventory" class="control-label">在途数量</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_USSTORAGE_IINVENTORY');?>" name="<?php echo C('DB_USSTORAGE_IINVENTORY');?>" value="<?php echo ($usstorage[0][C('DB_USSTORAGE_IINVENTORY')]); ?>" />
							</div>
						</div>
                    </div>
				</div>
			</div>
		</div>
		<div class="item tc">
			<a class="btn btn-s btn-grey" href="javascript:history.back();">返回</a>
			<button class="btn btn-blue btn-s" id="saveProductInfo">
				保存			</button>
		</div>
		</form> 
	</div>


		</div>
	</div>
		
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>