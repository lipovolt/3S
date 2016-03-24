<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="product.dwt" codeOutsideHTMLIsLocked="false" -->
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
					<i class="icon user"></i>
					<span class="blue">账户名</span>
					<a class="blue" style="margin:0px 10px;" href="#">退出</a>				</span>			</div>
		</div>
	
		
		<div class="nav">
			<div class="area">
				<!-- 头部菜单 -->
				<ul class="mainnav">
					<li>
						<a href="<?php echo U('Index/Manage/productInfo');;?>" mark=""><i class="icon MPD"></i><span>产品管理</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>导入产品</strong>								</dt>
								<dd><a href="#" >导入产品</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>产品信息管理</strong>								</dt>
								<dd><a href="/Manage/index.php?s=/ProductInfo/index" >产品信息</a></dd>
								<dd><a href="#" >批量打印单品条码</a></dd>
							</dl>
						</div>
					</li>
					<li>
						<a href="#" mark="Outbound"><i class="icon MWO"></i><span>库存</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>美国仓库存</strong>								</dt>
								<dd><a href="#"  mark="Outbound">万邑通库存</a></dd>
								<dd><a href="#"  mark="Outbound">自建仓库存</a></dd>
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
						<a href="#" mark="Outbound"><i class="icon GlobalTransfer"></i><span>美国自建仓</span></a>
						<div class="subnav">
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>入库</strong>								</dt>
								<dd><a href="#"  mark="Outbound">新增入库单</a></dd>
								<dd><a href="#"  mark="Outbound">全部订单</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>出库</strong>								</dt>
								<dd><a href="#"  mark="Outbound">单个录入</a></dd>
								<dd><a href="#"  mark="Outbound">批量上传</a></dd>
								<dd><a href="#"  mark="Outbound">全部订单</a></dd>
								<dd><a href="#"  mark="Outbound">未提交订单</a></dd>
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
			</div>
		</div>
	</div>	
	
    <!-- InstanceBeginEditable name="左边栏" -->
	<div class="area clearfix">
		<!-- 左边栏 -->
		<div class="sidenav"><div class="sidenav-hd"><strong>产品管理</strong></div>
			<div class="sidenav-bd">
				<dl>
					<dt>
					<i class="icon dropdown-s"></i>
					<strong>导入产品</strong>					</dt>
					<dd ><a href="#">导入产品</a></dd>
				</dl>
				<dl>
					<dt>
					<i class="icon dropdown-s"></i>
					<strong>产品信息管理</strong>					</dt>
					<dd  class="on" ><a href="#">产品信息</a></dd>
					<dd ><a href="#">批量打印单品条码</a></dd>
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
							<span>导入产品信息</span>
						<i class="after"></i>
						</li>					
					</ul>
					<div class="tab-content">
						<div class="tab-inner-content" style="">
							<div class="block">
								<div class="block-hd">
									<i class="icon import"></i>
									<strong>导入出口产品</strong>
								</div>
								<div class="block-bd">
									<div class="block-indent" style="overflow:hidden;">
										<div style="float:left;width:456px;">
											<p>请<a href="/Manage/index.php?s=/ProductInfo/DownloadProductTemplate/" style="color: #6495ED">点击此处</a>下载产品导入模板，填写产品信息完成后，上传数据。</p>
											<div>
												<!-- <form id="upload" method="POST" action="<?php echo U('Index/Manage/batchAdd');?>" enctype="multipart/form-data">
												<div class="file-input">
													<label for="importFile">选择文件</label>
													<span id="importFileText">未选择文件</span>
													<input id="importFile" name="image" accept=".xls" type="file" onchange="document.getElementById('importFileText').innerHTML = (this.value.replace(/^.*\\([^\.]*\..*)$/,'$1') || '未选择文件')" >
												</div>
												<button class="btn btn-s btn-blue" id="uploadSubmit">
												<i class="icon uparrow-m"></i>
												<i class="vline-inline"></i>
												<span>提交</span>
												</button>
												 <input type="hidden" name="__hash__" value="205ce8867ed6985ec41e823e939e67b5_e5fd8695ceb4b3bebb3f1b2a5bbce9a5" /></form> -->
												 <form action="<?php echo U('Index/Manage/batchAdd');?>" method="post" enctype="multipart/form-data">
										            <input type="file" name="import"/>
								          			<input type="hidden" name="table" value="tablename"/>
									             	<input type="submit" value="导入"/>
									         	</form>

											</div>
											<p>
												<span class="notice-s">产品信息导入只支持XLS格式文件</span>
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
	<!-- InstanceEndEditable -->
	<div class="area footer">Powered by 3S 2015 Shangsi CORPORATION. All Rights Reserved.</div>
</body>
<!-- InstanceEnd --></html>