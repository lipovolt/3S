<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>节点信息</title>
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
<dl>
	<dt>
		<strong>缺货补货</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Restock/importStorage');?>" >导出缺货表</a></dd>
	<dd><a href="<?php echo U('Storage/Restock/importStorage',array('country'=>'US'));?>" >导出美国缺货表</a></dd>
	<dd><a href="<?php echo U('Storage/Restock/importStorage',array('country'=>'DE'));?>" >导出德国缺货表</a></dd>
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
		<i class="icon dropdown-s"></i><strong>美国自建仓 Ebay Amazon</strong>								
	</dt>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSalePlanMetadata');?>" >美国自建仓销售基础表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest');?>" >美国自建仓销售表</a></dd>
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
	<dd><a href="<?php echo U('Sale/SzSale/usCal');?>" >飞特小包美国试算</a></dd>
	<dd><a href="<?php echo U('Sale/SzSale/deCal');?>" >飞特小包德国试算</a></dd>
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
			<div class="sidenav-hd"><strong>权限管理</strong></div>
			<div class="sidenav-bd">
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
			</div>
		</div>
	<div class="content">
	<div id="ProductInfo" class="main">
		<form name="search_product" id="search_product" action="<?php echo U('Admin/Rbac/searchNode');?>" method="POST">
			<div class="search-area">
				<div class="item">
					<div class="form-group">
						<label for="keyword" class="control-label">关键字</label>
						<div class="control-wrap">
							<select name="keyword" id="keyword" data-value="">
								<option value="<?php echo C('DB_NODE_ID');?>">节点编号</option>
								<option value="<?php echo C('DB_NODE_NAME');?>">节点名称</option>
							</select>
						</div>
						<div class="control-wrap">
							<input type="text" class="form-control"  name="keywordValue" id="keywordValue" value="">
						</div>
					</div>
					<button class="btn btn-s btn-blue" onClick="search_product.submit();"><span>查询</span></button>
				</div>			
			</div>
		</form>
		<div>
			<div class="tab-content">
				<div class="tab-inner"><a href="<?php echo U('Admin/Rbac/addNode');?>">添加应用</a></div>
				<table id="tablelist" class="tablelist">
					<tr>
						<th width="110">节点编号</th>
						<th><div class="tl">节点名称</div></th>
						<th><div class="tl">节点描述</div></th>
						<th><div class="tl">节点状态</div></th>
						<th><div class="tl">排序</div></th>
						<th><div class="tl">父节点编号</div></th>
						<th><div class="tl">节点级别</div></th>
						<th><div class="tl">备注</div></th>
						<th width="230">操作</th>
					</tr>
					<?php if(is_array($node)): $i = 0; $__LIST__ = $node;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$app): $mod = ($i % 2 );++$i;?><tr>
						<td><div class="tl"><?php echo ($app[C('DB_NODE_ID')]); ?></div></td>
						<td><div class="tl"><?php echo ($app[C('DB_NODE_NAME')]); ?></div></td>						
						<td><div class="tl"><?php echo ($app[C('DB_NODE_TITLE')]); ?></div></td>
						<td><div class="tl"><?php echo ($app[C('DB_NODE_STATUS')]); ?></div></td>						
						<td><div class="tl"><?php echo ($app[C('DB_NODE_SORT')]); ?></div></td>
						<td><div class="tl"><?php echo ($app[C('DB_NODE_PID')]); ?></div></td>
						<td><div class="tl"><?php echo ($app[C('DB_NODE_LEVEL')]); ?></div></td>
						<td><div class="tl"><?php echo ($app[C('DB_NODE_REMARK')]); ?></div></td>
						<td>
							<a href="<?php echo U('Admin/Rbac/addNode',array(C('DB_NODE_PID')=>$app[C('DB_NODE_ID')],C('DB_NODE_LEVEL')=>2));?>">添加控制器</a>
							<a href="<?php echo U('Admin/Rbac/updateNode',array(C('DB_NODE_PID')=>$app[C('DB_NODE_ID')],C('DB_NODE_LEVEL')=>2));?>">修改</a>
							<a href="<?php echo U('Admin/Rbac/deleteNode',array(C('DB_NODE_PID')=>$app[C('DB_NODE_ID')],C('DB_NODE_LEVEL')=>2));?>">删除</a>
						</td>
						</tr>	
						<?php if(is_array($app["child"])): $i = 0; $__LIST__ = $app["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$action): $mod = ($i % 2 );++$i;?><tr>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_ID')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_NAME')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_TITLE')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_STATUS')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_SORT')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_PID')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_LEVEL')]); ?></div></td>
								<td><div class="tl"><?php echo ($action[C('DB_NODE_REMARK')]); ?></div></td>
								<td>
									<a href="<?php echo U('Admin/Rbac/addNode',array(C('DB_NODE_PID')=>$action[C('DB_NODE_ID')],C('DB_NODE_LEVEL')=>3));?>">添加方法</a>
								</td>
							</tr>
							<?php if(is_array($action["child"])): $i = 0; $__LIST__ = $action["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$method): $mod = ($i % 2 );++$i;?><tr>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_ID')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_NAME')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_TITLE')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_STATUS')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_SORT')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_PID')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_LEVEL')]); ?></div></td>
									<td><div class="tl"><?php echo ($method[C('DB_NODE_REMARK')]); ?></div></td>
									<td>
										<a href="<?php echo U('Admin/Rbac/updateNode',array(C('DB_NODE_PID')=>$method[C('DB_NODE_ID')],C('DB_NODE_LEVEL')=>3));?>">修改</a>
										<a href="<?php echo U('Admin/Rbac/deleteNode',array(C('DB_NODE_PID')=>$method[C('DB_NODE_ID')],C('DB_NODE_LEVEL')=>3));?>">删除</a>
									</td>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
							<tr><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td></tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>								
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