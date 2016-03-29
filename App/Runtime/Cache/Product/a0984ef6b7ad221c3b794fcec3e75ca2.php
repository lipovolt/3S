<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>产品信息</title>
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
					<span>
	<i class="icon user"></i>
	<span class="blue"><?= I('session.username',0); ?></span>
	<a class="blue" style="margin:0px 10px;" href="<?php echo U('Index/Index/logout');?>">退出</a>
</span>
			</div>
		</div>		
		<div class="nav">
			<div class="area">
				<html>
<head>
	<link rel="stylesheet" href="__PUBLIC__/Css/base.css">
	<link rel="stylesheet" href="__PUBLIC__/Css/zh-cn.css">
</head>
<body>
<ul class="mainnav">
					<li>
						<a href="<?php echo U('Product/Product/productInfo');?>" mark=""><i class="icon MPD"></i><span>产品管理</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>导入产品</strong>								</dt>
								<dd><a href="<?php echo U('Product/Product/productBatchAdd');?>" >导入产品</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>产品信息管理</strong>								</dt>
								<dd><a href="<?php echo U('Product/Product/productInfo',array('country'=>'us'));;?>" >产品信息</a></dd>
							</dl>
						</div>
					</li>
					<li>
						<a href="<?php echo U('Storage/Storage/usstorage');?>" mark="Outbound"><i class="icon MWO"></i><span>库存</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>美国仓库存</strong>								</dt>
								<dd><a href="<?php echo U('Storage/Storage/usstorage');?>"  mark="Outbound">自建仓库存</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>深圳仓库存</strong>								</dt>
								<dd><a href="#"  mark="Outbound">深圳仓库存</a></dd>
							</dl>
						</div>
					</li>
					<li>
						<a href="#" mark="Outbound"><i class="icon MWI"></i><span>万邑通</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>入库</strong>								</dt>
								<dd><a href="/Manage/index.php?s=/WarehouseOrder/stepOne"  mark="Outbound">新增入库单</a></dd>
								<dd><a href="#"  mark="Outbound">全部订单</a></dd><dd><a href="#"  mark="Outbound">已合并订单</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>出库</strong>								</dt>
								<dd><a href="#"  mark="Outbound">单个录入</a></dd>
								<dd><a href="#"  mark="Outbound">批量上传</a></dd>
								<dd><a href="#"  mark="Outbound">全部订单</a></dd>
							</dl>
						</div>
					</li>
					<li>
						<a href="<?php echo U('Ussw/Ussw/ussw');?>" mark="Outbound"><i class="icon GlobalTransfer"></i><span>美国自建仓</span></a>
						<div class="subnav">
							<dl>
								<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
								<dd><a href="<?php echo U('Ussw/Ussw/ussw');?>">单品入库操作</a></dd>
								<dd ><a href="<?php echo U('Ussw/Ussw/storageFileBatchAdd');?>">批量导入入库单</a></dd>
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
					</li>
					<li  >
						<a href="#" mark="Outbound"><i class="icon GlobalTransfer"></i><span>系统管理</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>用户管理</strong>								</dt>
								<dd><a href="<?php echo U('Index/Rbac/addRole');?>" >添加用户</a></dd>
								<dd><a href="#" >删除用户</a></dd>
								<dd><a href="#" >锁定用户</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>角色管理</strong>								</dt>
								<dd><a href="#" >添加角色</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>节点管理</strong>								</dt>
								<dd><a href="#" >添加节点</a></dd>
								<dt>
									<i class="icon dropdown-s"></i><strong>权限分配</strong>								</dt>
								<dd><a href="#" >权限分配</a></dd>
							</dl>
						</div>
					</li>
				</ul>
</body>
</html>
			</div>
		</div>
	</div>	
	
    <!-- InstanceBeginEditable name="左边栏" -->
	<div class="area clearfix">
		<!-- 左边栏 -->
<div class="sidenav">
	<div class="sidenav-hd"><strong>产品管理</strong></div>
	<div class="sidenav-bd">
		<dl>
			<dt>
			<i class="icon dropdown-s"></i>
			<strong>导入产品</strong>					</dt>
			<dd ><a href="<?php echo U('Product/Product/productBatchAdd');?>">导入产品</a></dd>
		</dl>
		<dl>
			<dt>
			<i class="icon dropdown-s"></i>
			<strong>产品信息管理</strong>	</dt>
			<dd  class="on" ><a href="<?php echo U('Product/Product/productInfo');?>">产品信息</a></dd>
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
						<th width="110">产品编码</th>
						<th><div class="tl">中文名称</div></th>
						<th><div class="tl">重量g</div></th>
						<th>长cm</th>
						<th>宽cm</th>
						<th><div class="tl">高cm</div></th>
						<th><div>带电</div></th>
						<th><div>德国</div></th>
						<th><div>德国头程方式</div></th>
						<th><div>美国</div></th>
						<th><div>美国头程方式</div></th>
						<th width="60">产品经理</th>
						<th width="230">操作</th>
					</tr>
					<?php if(is_array($products)): $i = 0; $__LIST__ = $products;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><div class="tl"><?php echo ($vo["sku"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["title-cn"]); ?></div></td>						
						<td><div class="tl"><?php echo ($vo["weight"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["length"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["width"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["height"]); ?></div></td>
						<?php echo $vo['battery']==0?'<td><div class="tl">否</div></td>':'<td><div class="tl">是</div></td>';?>
						<?php echo $vo['de']==0?'<td><div class="tl">否</div></td>':'<td><div class="tl">是</div></td>';?>
						<td><div class="tl"><?php echo ($vo["way-to-de"]); ?></div></td>
						<?php echo $vo['us']==0?'<td><div class="tl">否</div></td>':'<td><div class="tl">是</div></td>';?>
						<td><div class="tl"><?php echo ($vo["way-to-us"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["manager"]); ?></div></td>
						<td>
							<a href="<?php echo U('Product/Product/productEdit',array('sku'=>$vo['sku']));?>">编辑</a>
						</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?> 								
				</table>
				<div class="result page" align="center"><?php echo ($page); ?></div>
			</div>
		</div>
	</div>>


	</div>
	</div>
	</div>
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>