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
	<dd><a href="<?php echo U('Ussw/Ussw/ussw');?>">入库验货</a></dd>
	<dd ><a href="<?php echo U('Ussw/Inbound/fileImport');?>">批量导入入库单</a></dd>
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
	
    <!-- InstanceBeginEditable name="左边栏" -->
	<div class="area clearfix">
		<div class="sidenav">
			<div class="sidenav-hd"><strong>美国自建仓</strong></div>
			<div class="sidenav-bd">
				<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
	<dd><a href="<?php echo U('Ussw/Inbound/index');?>"  mark="Outbound">全部入库单</a></dd>
	<dd><a href="<?php echo U('Ussw/Inbound/creatInboundOrder');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
	<dd><a href="<?php echo U('Ussw/Ussw/ussw');?>">入库验货</a></dd>
	<dd ><a href="<?php echo U('Ussw/Inbound/fileImport');?>">批量导入入库单</a></dd>
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
		<form method="POST" id="edit_productInfo" action="<?php echo U('Ussw/Inbound/addInbound');?>">
		<div class="block-outer BaseInfo">
			<div class="block-outer-bd">
				<div class="inline-block block-indent">
					<div class="item">
						<div class="form-group">
							<label for="way" class="control-label">运输方式</label>
							<div class="control-wrap">
								<input type="text" name="wayValue" value="" id="wayValue"/>
							</div>
						</div>
						<div class="form-group">
							<label for="pQuantity" class="control-label">包裹数</label>
							<div class="control-wrap">
								<input type="text"  id="pQuantityValue" name="pQuantityValue" value="" />
							</div>
						</div>
					</div>
					<div class="item">
						<div class="form-group">
							<label for="weight" class="control-label">重量</label>
							<div class="control-wrap">
								<input type="text" name="weightValue" id="weightValue" value="" />
							</div>
						</div>
						<div class="form-group">
							<label for="volume" class="control-label">体积</label>
							<div class="control-wrap">
								<input type="text" name="volumeValue" id="volumeValue" value="" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="status" class="control-label">状态</label>
							<div class="control-wrap">
								<input type="text"  id="statusValue" name="statusValue" value="" />
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
		</div></form> 
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