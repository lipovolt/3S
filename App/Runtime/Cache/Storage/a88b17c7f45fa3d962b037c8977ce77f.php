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
						<a href="<?php echo U('Storage/Storage/storage');?>" mark="Outbound"><i class="icon MWO"></i><span>库存</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>美国仓库存</strong>								</dt>
								<dd><a href="<?php echo U('Storage/Storage/storage');?>"  mark="Outbound">自建仓库存</a></dd>
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
	<div class="area clearfix">
		<!-- 左边栏 -->
<div class="sidenav">
	<div class="sidenav-hd"><strong>库存</strong></div>
	<div class="sidenav-bd">
		<dl>
			<dt><i class="icon dropdown-s"></i><strong>美国自建仓库存</strong></dt>
			<dd><a href="<?php echo U('Storage/Storage/storage');?>">自建仓库存</a></dd>
		</dl>
		<dl>
			<dt><i class="icon dropdown-s"></i><strong>深圳库存</strong></dt>
			<dd ><a href="#">深圳库存</a></dd>
		</dl>
	</div>
</div>
	<div class="content">
	<div id="Australia" class="main">
		<form name="search_outbound" id="search_outbound" action="<?php echo U('Storage/Storage/storage');?>" method="POST">			
		<div class="block-indent search-area">
			<div class="form-group">
				<label for="" class="control-label">关键字</label>
				<div class="control-wrap">
					<select name="keyword" id="keyword" data-value="">
						<option value="sku">产品编码</option>
						<option value="cname">产品中文名称</option>
						<option value="ename">产品英文名称</option>
					</select>
				</div>
				<div class="control-wrap">
					<input type="text" name="keywordValue" id="keywordValue" value="">
				</div>
			</div>
			<button class="btn btn-s btn-blue" onclick="search_outbound.submit();">
				<i class="icon search"></i>
				<i class="vline-inline"></i>
				<span>查询</span>
			</button>
        </div>
		<input type="hidden" name="__hash__" value="4aa8bc909dc4e7c9f1bf19868849db32_0a5f96e955ccfaec6ab83bbc99a7516f" /></form>
		<div>
			<div class="tab-content data-list">				
							
				<table id="tablelist" class="tablelist">
					<tr>
					    <th><div class="t1">产品编码</div></th>
					    <th><div class="t1">货位</div></th>
					    <th><div class="tl">中文名称</div></th>	                          
					    <th><div class="tl">英文名称</div></th>
						<th><div class="tl">属性</div></th>
						<th><div class="tr">历史入库</div></th>
						<th><div class="tr">可用库存</div></th>
						<th><div class="tr">待出库</div></th>
						<th><div class="tr">在途库存</div></th>
						<th><div class="tr">历史销量</div></th>
						<th><div class="t1">备注</div></th>
					</tr>    
					<tr>
						<?php if(is_array($usstorage)): $i = 0; $__LIST__ = $usstorage;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td><div class="tl"><?php echo ($vo["sku"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["position"]); ?></div></td>						
						<td><div class="tl"><?php echo ($vo["cname"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["ename"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["attribute"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["cinventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["ainventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["oinventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["iinventory"]); ?></div></td>
						<td><div class="tr"><?php echo ($vo["csales"]); ?></div></td>
						<td><div class="tl"><?php echo ($vo["remark"]); ?></div></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?> 		
					</tr>						
				</table>
				<div class="result page" align="center"><?php echo ($page); ?></div>
				<div class="tr">
								
					<!-- 分页开始  --> 
										<!-- 分页 结束 -->	
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