<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>更新美国自建仓入库单</title>
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
	<dd><a href="<?php echo U('Shenzhen/Shenzhen/importUsswInbound');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
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
	<dd><a href="<?php echo U('Ussw/Ussw/ussw');?>">入库验货</a></dd>
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
			<div class="sidenav-hd"><strong>深圳</strong></div>
			<div class="sidenav-bd">
				<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>美国自建仓</strong>								
	</dt>
	<dd><a href="<?php echo U('Shenzhen/Shenzhen/importUsswInbound');?>"  mark="Outbound">新建美国自建仓入库单</a></dd>
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
		</div>
		<div class="content">
			<script>
			var GlobalData = {
				_COMMON_DATA_PROCESSING_ : "数据处理中...",
				_PRODUCTINFO_BATCHADD_PRODUCTIMPORT_ERROR_FILE_TYPE_NOT_MATCH_:"产品信息导入只支持XLS格式文件!",
				_COMMON_PLEASE_SELECT_FILE_:"请选择文件！"
			}
			</script>
			
			<!-- 主页面开始  -->
				<div id="ProductInfo" class="main">
					<ul class="tab">
						<li class="on">
						<i class="before"></i>
							<span>美国自建仓</span>
						<i class="after"></i>
						</li>					
					</ul>
					<div class="tab-content">
						<div class="tab-inner-content" style="">
							<div class="block">
								<div class="block-hd">
									<i class="icon import"></i>
									<strong>更新美国自建仓入库单</strong>
								</div>
								<div class="block-bd">
									<div class="block-indent" style="overflow:hidden;">
										<div style="float:left;width:456px;">
											<p>请<a href="#" style="color: #6495ED">点击此处</a>下载产品导入模板，填写产品信息完成后，上传数据。</p>
											<div>
												 <form action="<?php echo U('Shenzhen/Shenzhen/update');?>" method="post" enctype="multipart/form-data">
												 	<div class="form-group">
														<label for="tableName" class="control-label">表格名称</label>
														<div class="control-wrap">
															<input type="text"  id="tableNameValue" name="tableNameValue" value="usswYYYYMMDD" />
														</div>
													</div>
										            <input type="file" name="import"/>
								          			<input type="hidden" name="table" value="tablename"/>
									             	<input type="submit" value="导入"/>
									         	</form>

											</div>
											<p>
												<span class="notice-s">产品信息导入只支持XLS格式文件，文件名格式 usswYYYYMMDD, 例如ussw20160330。</span>
											</p>
										</div>										
									</div>															
								</div>
							</div>
						</div>
					</div>
			<!-- 主页面结束 -->
		</div>
	</div>
	</div>
		
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>