<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ggs 美自建仓试算</title>
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
	<dd><a href="<?php echo U('Sale/Sale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
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
	
    <!-- InstanceBeginEditable name="左边栏" -->
	<div class="area clearfix">
		<div class="sidenav">
			<div class="sidenav-hd"><strong>销售</strong></div>
			<div class="sidenav-bd">
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
	<dd><a href="<?php echo U('Sale/Sale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>rc-helcar</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/Sale/index',array('table'=>'usWinitSale'));?>" >美国万邑通销售表</a></dd>
	<dd><a href="<?php echo U('Sale/Sale/index',array('table'=>'deWinitSale'));?>" >德国万邑通销售表</a></dd>
</dl>	
			</div>
		</div>
		<div class="content">
	<div id="ProductInfo" class="main">
		<div>
			<div class="tab-content">	
				<table id="tablelist" class="tablelist">
					<tr>
						<th><div class="tl">采购价￥</div></th>
						<th><div class="tl">关税$</div></th>
						<th><div class="tl">仓出入费$</div></th>
						<th><div class="tl">头程方式</div></th>
						<th><div class="tl">头程$</div></th>
						<th><div class="tl">本地方式</div></th>
						<th><div class="tl">本地运费$</div></th>
						<th><div class="tl">成本$</div></th>
						<th><div class="tl">售价$</div></th>
						<th><div class="tl">毛利润$</div></th>
						<th><div class="tl">毛利率</div></th>
						<th><div class="tl">重量g</div></th>
						<th>长cm</th>
						<th>宽cm</th>
						<th><div class="tl">高cm</div></th>
						<th><div class="tl">操作</div></th>
					</tr>
					<tr>
						<form method="POST" id="ggs-ussw-item-test" action="<?php echo U('Sale/Sale/ggsUsswItemTest');?>">		
						<td><div class="tl"><input type="text" style="width:50px;" name="price" value="<?php echo ($testData['price']); ?>" id="price"q/></div></td>
						<td><div class="tl"><?php echo ($testData['us-rate']); ?></div></td>
						<td><div class="tl"><?php echo ($testData['ussw-fee']); ?></div></td>
						<td><div class="tl">
							<select name="keyword" id="keyword" data-value="">
								<option value="air">空运</option>
								<option value="sea">海运</option>
							</select></div></td>
						<td><div class="tl"><?php echo ($testData['way-to-us-fee']); ?></div></td>
						<td><div class="tl"><?php echo ($testData['local-shipping-way']); ?></div></td>
						<td><div class="tl"><?php echo ($testData['local-shipping-fee']); ?></div></td>
						<td><div class="tl"><?php echo ($testData['cost']); ?></div></td>
						<td><div class="tl"><input type="text" style="width:60px;" name="saleprice" value="<?php echo ($testData['saleprice']); ?>" id="saleprice"/></div></td>
						<td><div class="tl"><?php echo ($testData['gprofit']); ?></div></td>
						<td><div class="tl"><?php echo ($testData['grate']); ?></div></td>
						<td><div class="tl"><input type="text" style="width:60px;" name="weight" value="<?php echo ($testData['weight']); ?>" id="weight"/></div></td>
						<td><div class="tl"><input type="text" style="width:50px;" name="length" value="<?php echo ($testData['length']); ?>" id="length"/></div></td>
						<td><div class="tl"><input type="text" style="width:50px;" name="width" value="<?php echo ($testData['width']); ?>" id="width"/></div></td>
						<td><div class="tl"><input type="text" style="width:50px;" name="height" value="<?php echo ($testData['height']); ?>" id="height"/></div></td>
						<td>
							<button class="btn btn-blue btn-s" id="saveProductInfo">试算</button>
							<a href="<?php echo U('Sale/Sale/ggsUsswItemTest');?>">重置</a>
						</td>
						</form>
					</tr>								
				</table>
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