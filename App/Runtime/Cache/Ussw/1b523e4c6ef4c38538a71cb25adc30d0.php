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
		<a href="<?php echo U('Sale/Sale/index',array('table'=>'usswSale'));?>" mark="sale"><span>销售</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>基本信息</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/Sale/index',array('table'=>'metadata'));?>" >基本信息</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>greatgoodshop</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/Sale/index',array('table'=>'usswSale'));?>" >美国自建仓销售表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>rc-helcar</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/Sale/index',array('table'=>'usWinitSale'));?>" >美国万邑通销售表</a></dd>
	<dd><a href="<?php echo U('Sale/Sale/index',array('table'=>'deWinitSale'));?>" >德国万邑通销售表</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="#" mark="USSW"><span>美国自建仓</span></a>
		<div class="subnav">
			<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
	<dd><a href="<?php echo U('Ussw/Inbound/index');?>"  mark="Outbound">全部入库单</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/creatInboundOrder');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>出库管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Ussw/usswOutbound');?>">单品出库</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Ussw/usswManage');?>">库存信息</a></dd>
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
	<dd><a href="<?php echo U('Ussw/Inbound/index');?>"  mark="Outbound">全部入库单</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/creatInboundOrder');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>出库管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Ussw/usswOutbound');?>">单品出库</a></dd>
</dl>
<dl>
	<dt><i class="icon dropdown-s"></i><strong>库存管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Ussw/usswManage');?>">库存信息</a></dd>
</dl>
	
			</div>
		</div>
	<div class="content">
	<div id="ProductInfo" class="main">
		<form method="POST" id="edit_productImg" action="<?php echo U(Ussw/Ussw/update);?>" enctype="multipart/form-data">
		
		<div class="product-upload-wrap">
			<input type="hidden" name="ProductID" value="1030634"> 
			<input type="hidden" name="CountryID" value="122">
		</div>
		<input type="hidden" name="__hash__" value="48007adec5871053582d206bccb8d2ac_c6d32f4df480a79e2054461b3dc60b86" /></form>
		<form method="POST" id="edit_productInfo" action="<?php echo U('Ussw/Ussw/update');?>">
		<div class="block-outer BaseInfo">
			<div class="block-outer-hd"><strong>基本信息</strong></div>
			<div class="block-outer-bd">
				<div class="inline-block block-indent">
					<div class="item">
						<div class="form-group">
							<label for="position" class="control-label">货位</label>
							<input type="hidden"  id="idValue" name="idValue" value="<?php echo ($usstorage[0]['id']); ?>"/>
							<div class="control-wrap">
								<input type="text"  id="positionValue" name="positionValue" value="<?php echo ($usstorage[0]['position']); ?>"/>
							</div>
						</div>
						<div class="form-group">
							<label for="sku" class="control-label">产品编码</label>
							<div class="control-wrap">
								<input type="text" name="skuValue" value="<?php echo ($usstorage[0]['sku']); ?>" id="skuValue" />
							</div>
						</div>
					</div>
					<div class="item">
						<div class="form-group">
							<label for="cname" class="control-label">中文名称</label>
							<div class="control-wrap">
								<input type="text" name="cnameValue" id="cnameValue" value="<?php echo ($usstorage[0]['cname']); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="ename" class="control-label">英文名称</label>
							<div class="control-wrap">
								<input type="text" name="enameValue" id="enameValue" value="<?php echo ($usstorage[0]['ename']); ?>" />
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
								<input type="text"  id="csalesValue" name="csalesValue" value="<?php echo ($usstorage[0]['csales']); ?>" />
							</div>
						</div>
					</div>
                    <div class="item">
                        <div class="form-group">
							<label for="cinventory" class="control-label">累计入库</label>
							<div class="control-wrap">
								<input type="text"  id="cinventoryValue" name="cinventoryValue" value="<?php echo ($usstorage[0]['cinventory']); ?>" />
							</div>
						</div>
                        <div class="form-group">
							<label for="ainventory" class="control-label">可用数量</label>
							<div class="control-wrap">
								<input type="text"  id="ainventoryValue" name="ainventoryValue" value="<?php echo ($usstorage[0]['ainventory']); ?>" />
							</div>
						</div>
                    </div>
                    <div class="item">
                        <div class="form-group">
							<label for="oinventory" class="control-label">待出库</label>
							<div class="control-wrap">
								<input type="text"  id="oinventoryValue" name="oinventoryValue" value="<?php echo ($usstorage[0]['oinventory']); ?>" />
							</div>
						</div>
                        <div class="form-group">
							<label for="iinventory" class="control-label">在途数量</label>
							<div class="control-wrap">
								<input type="text"  id="iinventoryValue" name="iinventoryValue" value="<?php echo ($usstorage[0]['iinventory']); ?>" />
							</div>
						</div>
                    </div>
				</div>
			</div>
		</div>
		<div class="item tc"><input type='hidden' name='ProductID' value='1030634'>
			<a class="btn btn-s btn-grey" href="javascript:history.back();">返回</a>
			<button class="btn btn-blue btn-s" id="saveProductInfo">
				保存			</button>
		</div>
		<input type="hidden" name="__hash__" value="48007adec5871053582d206bccb8d2ac_c6d32f4df480a79e2054461b3dc60b86" /></form> 
	</div>


		</div>
	</div>
		
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>