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
<script type="text/javascript">
function checkboxValue(tinyintValue){
	if(tinyintValue==1){
		echo "checked";
	}
}
</script>
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
								<dd><a href="#" >导入产品</a></dd>
							</dl>
							<dl>
								<dt>
									<i class="icon dropdown-s"></i><strong>产品信息管理</strong>								</dt>
								<dd><a href="<?php echo U('Product/Product/productInfo',array('country'=>'us'));;?>" >产品信息</a></dd>
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
			<dd ><a href="#">批量打印单品条码</a></dd>
		</dl>	
	</div>
</div>
	<div class="content">
	<div id="ProductInfo" class="main">
		<form method="POST" id="edit_productImg" action="<?php echo U(Product/Product/update);?>" enctype="multipart/form-data">
		
		<div class="product-upload-wrap">
			<input type="hidden" name="ProductID" value="1030634"> 
			<input type="hidden" name="CountryID" value="122">
		</div>
		<input type="hidden" name="__hash__" value="48007adec5871053582d206bccb8d2ac_c6d32f4df480a79e2054461b3dc60b86" /></form>
		<form method="POST" id="edit_productInfo" action="<?php echo U('Product/Product/update');?>">
		<div class="block-outer BaseInfo">
			<div class="block-outer-hd"><strong>基本信息</strong></div>
			<div class="block-outer-bd">
				<div class="inline-block block-indent">
					<div class="item">
						<div class="form-group">
							<label for="sku" class="control-label">产品编码</label>
							<div class="control-wrap">
								<input type="text" name="skuValue" value="<?php echo ($product[0]['sku']); ?>" id="skuValue" />
								<input type="hidden" name="ProductCode" value="<?php echo ($product[0]['id']); ?>" id="ProductCode"  />
							</div>
						</div>
						<div class="form-group">
							<label for="manager" class="control-label">产品经理</label>
							<div class="control-wrap">
								<input type="text"  id="managerValue" name="managerValue" value="<?php echo ($product[0]['manager']); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
						<div class="form-group">
							<label for="Name" class="control-label">中文名称</label>
							<div class="control-wrap">
								<input type="text" name="Name" id="Name" value="<?php echo ($product[0]['title-cn']); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="EName" class="control-label">英文名称</label>
							<div class="control-wrap">
								<input type="text" name="EName" id="EName" value="<?php echo ($product[0]['title-en']); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="weight" class="control-label">重量g</label>
							<div class="control-wrap">
								<input type="text"  id="weightValue" name="weightValue" value="<?php echo ($product[0]['weight']); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="length" class="control-label">长cm</label>
							<div class="control-wrap">
								<input type="text"  id="lengthValue" name="lengthValue" value="<?php echo ($product[0]['length']); ?>" />
							</div>
						</div>
					</div>
                    <div class="item">
                        <div class="form-group">
							<label for="width" class="control-label">宽cm</label>
							<div class="control-wrap">
								<input type="text"  id="widthValue" name="widthValue" value="<?php echo ($product[0]['width']); ?>" />
							</div>
						</div>
                        <div class="form-group">
							<label for="height" class="control-label">高cm</label>
							<div class="control-wrap">
								<input type="text"  id="heightValue" name="heightValue" value="<?php echo ($product[0]['height']); ?>" />
							</div>
						</div>
                    </div>
                    <div class="item">
                    	<div class="form-group checkbox">
							<div class="control-wrap">
								<?php
 if($product[0]['de']==0){ echo '<input type="checkbox" id="de" name="de" />'; } else{ echo '<input type="checkbox" id="de" name="de" checked />'; } ?>
							</div>
							<label for="de" class="control-label">德国</label>
						</div>
                        <div class="form-group">
							<label for="way-to-de" class="control-label">德国头程方式</label>
							<div class="control-wrap">
								<input type="text"  value="<?php echo ($product[0]['way-to-de']); ?>" id="way-to-de-value" name="way-to-de-value" />
							</div>
						</div>
						<div class="form-group">
							<label for="width" class="control-label">德国关税率</label>
							<div class="control-wrap">
								<input type="text"  id="de-rate-value" name="de-rate-value" value="<?php echo ($product[0]['de-rate']); ?>" />
							</div>
						</div>
                    </div>
                    <div class="item">
                    	<div class="form-group checkbox">	
							<div class="control-wrap">
								<?php
 if($product[0]['us']==0){ echo '<input type="checkbox" id="us" name="us" />'; } else{ echo '<input type="checkbox" id="us" name="us" checked />'; } ?>
							</div>
							<label for="us" class="control-label">美国</label>
						</div>
                        <div class="form-group">
							<label for="way-to-us" class="control-label">美国头程方式</label>
							<div class="control-wrap">
								<input type="text"  value="<?php echo ($product[0]['way-to-us-value']); ?>" id="way-to-us-value" name="way-to-us-value" />
							</div>
						</div>
						<div class="form-group">
							<label for="height" class="control-label">美国关税率</label>
							<div class="control-wrap">
								<input type="text"  id="us-rate-value" name="us-rate-value" value="<?php echo ($product[0]['us-rate']); ?>" />
							</div>
						</div>
                    </div>
					<div class="item">						
						<div class="form-group checkbox">
							<div class="control-wrap">
								<?php
 if($product[0]['battery']==0){ echo '<input type="checkbox" id="battery" name="battery" />'; } else{ echo '<input type="checkbox" id="battery" name="battery" checked />'; } ?>							
							</div>
							<label for="IsBattery" class="control-label">有电池</label>
						</div> 
						<div class="form-group">
							<label for="height" class="control-label">供货商信息</label>
							<div class="control-wrap">
								<input type="text"  id="supplierValue" name="supplierValue" value="<?php echo ($product[0]['supplier']); ?>" />
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
		</div>
		<input type="hidden" name="__hash__" value="48007adec5871053582d206bccb8d2ac_c6d32f4df480a79e2054461b3dc60b86" /></form> 
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