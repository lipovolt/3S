<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>销售信息</title>
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
		<a href="<?php echo U('Sale/UsswSale/index');?>" mark="sale"><span>销售</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>基本信息</strong>								
	</dt>
	<dd><a href="#" >基本信息</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>greatgoodshop</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/UsswSale/index');?>" >美国自建仓销售表</a></dd>
	<dd><a href="<?php echo U('Sale/UsswSale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
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
	<dd><a href="#" >基本信息</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>greatgoodshop</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/UsswSale/index');?>" >美国自建仓销售表</a></dd>
	<dd><a href="<?php echo U('Sale/UsswSale/ggsUsswItemTest');?>" >美国自建仓试算</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>rc-helcar</strong>								
	</dt>
	<dd><a href="#" >美国万邑通销售表</a></dd>
	<dd><a href="#" >德国万邑通销售表</a></dd>
</dl>	
			</div>
		</div>
		<div class="content">
	<div id="ProductInfo" class="main">
		<form name="search_product" id="search_product" action="<?php echo U('Product/Product/productInfo');?>" method="POST">
			<div class="search-area">
				<div class="item">
					<div class="form-group">
						<label for="keyword" class="control-label">关键字</label>
						<div class="control-wrap">
							<select name="keyword" id="keyword" data-value="">
								<option value="sku">产品编码</option>
								<option value="title-cn">产品名称</option>
							</select>
						</div>
						<div class="control-wrap">
							<input type="text" class="form-control"  name="keywordValue" id="keywordValue" value="">
						</div>
					</div>
					<input type="hidden" name="country" value="122" />
					<button class="btn btn-s btn-blue" onClick="search_product.submit();">
						<i class="icon search"></i>
						<i class="vline-inline"></i>
						<span>查询</span>
					</button>
				</div>			
			</div>
			<input type="hidden" name="__hash__" value="ff49ed719b3da9a91e3fa1b682fe6f2c_58292026a894f750e9cf920bd524eb81" />
		</form>
		<div>
			<div class="tab-content">	
				<table id="tablelist" class="tablelist">
					<tr>
						<th width="66">产品编码</th>
						<th><div class="tl">中文名称</div></th>
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
						<th><div class="tl">重量oz</div></th>
						<th>长in.</th>
						<th>宽in.</th>
						<th><div class="tl">高in.</div></th>
					</tr>
					<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><div class="tl"><?php echo ($vo["sku"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["title-cn"]); ?></div></td>						
						<td><div class="tl"><?php echo ($vo["price"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["us-rate"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["ussw-fee"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["way-to-us"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["way-to-us-fee"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["local-shipping-way"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["local-shipping-fee"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["cost"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["ggs-ussw-sp"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["gprofit"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["grate"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["weight"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["length"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["width"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["height"]); ?></div></td>
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