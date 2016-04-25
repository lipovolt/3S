<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>美国仓库存信息管理</title>
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
		<form name="search_product" id="search_product" action="<?php echo U('Ussw/Storage/index');?>" method="POST">
			<div class="search-area">
				<div class="item">
					<div class="form-group">
						<label for="keyword" class="control-label">关键字</label>
						<div class="control-wrap">
							<select name="keyword" id="keyword" data-value="">
								<option value="<?php echo C('DB_USSTORAGE_SKU');?>">产品编码</option>
								<option value="<?php echo C('DB_USSTORAGE_CNAME');?>">产品名称</option>
							</select>
						</div>
						<div class="control-wrap">
							<input type="text" class="form-control"  name="keywordValue" id="keywordValue" value="">
						</div>
					</div>
					<button class="btn btn-s btn-blue" onClick="search_product.submit();">
						<span>查询</span>
					</button>
				</div>			
			</div>
		</form>
		<form name="sort_usstorage" id="sort_usstorage" action="<?php echo U('Ussw/Storage/sort');?>" method="POST">
			<div class="search-area">
				<div class="item">
					<div class="form-group">
						<label for="keyword" class="control-label">按照</label>
						<div class="control-wrap">
						<select name="sortword" id="sortword" first="firstSelect" data-value="">
							<option value="<?php echo C('DB_USSTORAGE_SKU');?>" <?php echo C('DB_USSTORAGE_SKU')==$selected?selected:'' ?>>产品编码</option>
							<option value="<?php echo C('DB_USSTORAGE_AINVENTORY');?>" <?php echo C('DB_USSTORAGE_AINVENTORY')==$selected?selected:'' ?>>可用库存</option>
							<option value="<?php echo C('DB_USSTORAGE_CINVENTORY');?>" <?php echo C('DB_USSTORAGE_CINVENTORY')==$selected?selected:'' ?>>历史入库</option>
							<option value="<?php echo C('DB_USSTORAGE_CSALES');?>" <?php echo C('DB_USSTORAGE_CSALES')==$selected?selected:'' ?>>历史销量</option>
							<option value="<?php echo C('DB_USSTORAGE_OINVENTORY');?>" <?php echo C('DB_USSTORAGE_OINVENTORY')==$selected?selected:'' ?>>待出库</option>
							<option value="<?php echo C('DB_USSTORAGE_IINVENTORY');?>" <?php echo C('DB_USSTORAGE_IINVENTORY')==$selected?selected:'' ?>>在途库存</option>
						</select>
						</div>
					</div>
					<div class="form-group">
						<div class="control-wrap">
							<select name="sort" id="sort" data-value="">
								<option value="asc" <?php echo 'asc'==$sort?selected:'' ?>>正序</option>
								<option value="desc" <?php echo 'desc'==$sort?selected:'' ?>>倒序</option>
							</select>
						</div>
					</div>
					<button class="btn btn-s btn-blue" onClick="search_product.submit();">
						<span>排序</span>
					</button>
				</div>			
			</div>
		</form>
		<div>
			<div class="tab" align="right">
				<a class="btn btn-blue btn-s" href="<?php echo U('Ussw/Storage/export');?>" >
					<span>导出</span>
				</a>
			</div>
			<div class="tab-content">	
				<table id="tablelist" class="tablelist">
					<tr>
						<th><div class="t1">货位</div></th>
					    <th><div class="t1">产品编码</div></th>					    
					    <th><div class="tl">中文名称</div></th>
						<th><div class="tl">属性</div></th>
						<th><div class="tr">历史入库</div></th>
						<th><div class="tr">可用库存</div></th>
						<th><div class="tr">待出库</div></th>
						<th><div class="tr">在途库存</div></th>
						<th><div class="tr">历史销量</div></th>
						<th><div class="tr">30天销量</div></th>
						<th><div class="t1">备注</div></th>
						<th width="230">操作</th>
					</tr>    
					<tr>
						<?php if(is_array($usstorage)): $i = 0; $__LIST__ = $usstorage;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><div class="tl"><?php echo ($vo[C('DB_USSTORAGE_POSITION')]); ?></div></td>
						<td><div class="tl"><?php echo ($vo[C('DB_USSTORAGE_SKU')]); ?></div></td>						
						<td><div class="tl"><?php echo ($vo[C('DB_USSTORAGE_CNAME')]); ?></div></td>
						<td><div class="tl"><?php echo ($vo[C('DB_USSTORAGE_ATTRIBUTE')]); ?></div></td>
						<td><div class="tr"><?php echo ($vo[C('DB_USSTORAGE_CINVENTORY')]); ?></div></td>
						<td><div class="tr"><?php echo ($vo[C('DB_USSTORAGE_AINVENTORY')]); ?></div></td>
						<td><div class="tr"><?php echo ($vo[C('DB_USSTORAGE_OINVENTORY')]); ?></div></td>
						<td><div class="tr"><?php echo ($vo[C('DB_USSTORAGE_IINVENTORY')]); ?></div></td>
						<td><div class="tr"><?php echo ($vo[C('DB_USSTORAGE_CSALES')]); ?></div></td>
						<td><div class="tr"><?php echo ($vo['30dayssales']); ?></div></td>
						<td><div class="tl"><?php echo ($vo[C('DB_USSTORAGE_REMARK')]); ?></div></td>
						<td>
							<a href="<?php echo U('Ussw/Storage/edit',array(C('DB_USSTORAGE_ID')=>$vo[C('DB_USSTORAGE_ID')]));?>">编辑</a>
						</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?> 		
					</tr>								
				</table>
				<div class="result page" align="center"><?php echo ($page); ?></div>
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