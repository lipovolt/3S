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
		<a href="<?php echo U('Product/Product/productInfo');?>" mark=""><span>产品管理</span></a>
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
	<dd><a href="<?php echo U('Product/Product/productInfo',array('country'=>'us'));;?>" >产品信息</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="<?php echo U('Storage/Storage/usstorage');?>" mark="Outbound"><span>库存</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>美国仓库存</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Storage/usstorage');?>"  mark="Outbound">自建仓库存</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>深圳仓库存</strong>								
	</dt>
	<dd><a href="#"  mark="Outbound">深圳仓库存</a></dd>
</dl>

		</div>
	</li>
	<li>
		<a href="<?php echo U('Shenzhen/Shenzhen/shenzhen');?>" mark="shenzhen"><span>深圳</span></a>
		<div class="subnav">
			<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>美国自建仓</strong>								
	</dt>
	<dd><a href="<?php echo U('Shenzhen/Shenzhen/updateUsswInbound');?>"  mark="Outbound">更新美国自建仓入库单</a></dd>
	<dd><a href="#"  mark="Outbound">导出美国自建仓补货表</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>万邑通</strong>								
	</dt>
	<dd><a href="#"  mark="Outbound">导出万邑通美国仓补货表</a></dd>
	<dd><a href="#"  mark="Outbound">导出万邑通德国仓补货表</a></dd>
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
	<li>
		<a href="#" mark="Admin"><span>系统管理</span></a>
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
		<form name="search_product" id="search_product" action="<?php echo U('Ussw/Ussw/usswManage');?>" method="POST">
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
						<th><div class="t1">货位</div></th>
					    <th><div class="t1">产品编码</div></th>					    
					    <th><div class="tl">中文名称</div></th>	                          
					    <th><div class="tl">英文名称</div></th>
						<th><div class="tl">属性</div></th>
						<th><div class="tr">历史入库</div></th>
						<th><div class="tr">可用库存</div></th>
						<th><div class="tr">待出库</div></th>
						<th><div class="tr">在途库存</div></th>
						<th><div class="tr">历史销量</div></th>
						<th><div class="t1">备注</div></th>
						<th width="230">操作</th>
					</tr>    
					<tr>
						<?php if(is_array($usstorage)): $i = 0; $__LIST__ = $usstorage;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><div class="tl"><?php echo ($vo["position"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["sku"]); ?></div></td>						
						<td><div class="tl"><?php echo ($vo["cname"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["ename"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["attribute"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["cinventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["ainventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["oinventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["iinventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["csales"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["remark"]); ?></div></td>
						<td>
							<a href="<?php echo U('Ussw/Ussw/usswEdit',array('sku'=>$vo['sku']));?>">编辑</a>
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