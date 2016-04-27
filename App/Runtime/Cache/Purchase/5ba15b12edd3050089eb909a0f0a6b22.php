<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>编辑供货商</title>
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
			<div class="sidenav-hd"><strong>采购管理</strong></div>
			<div class="sidenav-bd">
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
	<dd><a href="<?php echo U('Purchase/Supplier/addNewSupplier');?>" >添加供货商</a></dd>
</dl>	
			</div>
		</div>
	<div class="content">
		<script type="text/javascript">
		function checkForm()
		{
			var company = document.getElementById("<?php echo C('DB_SUPPLIER_COMPANY');?>").value;
			var person = document.getElementById("<?php echo C('DB_SUPPLIER_PERSON');?>").value;
			var wangwang = document.getElementById("<?php echo C('DB_SUPPLIER_WANGWANG');?>").value;
			var qq = document.getElementById("<?php echo C('DB_SUPPLIER_QQ');?>").value;
			var tel = document.getElementById("<?php echo C('DB_SUPPLIER_TEL');?>").value;
			var website = document.getElementById("<?php echo C('DB_SUPPLIER_WEBSITE');?>").value;
		    if(company.trim()==("") && person.trim()==""){
		    	alert("公司名称或者联系人，必填一项");
		    	return false;
		    }
		    else if(wangwang.trim()==("") && qq.trim()=="" && tel.trim()==("") && website.trim()=="" ){
		    	alert("旺旺，qq，电话，网址，必填一项");
		    	return false;
		    }else{
		    	return true;
		    }

		}
		</script>
	<div id="Supplier" class="main">
		<form method="POST" id="add_new_supplier" action="<?php echo U('Purchase/Supplier/edit');?>">
		<div class="block-outer BaseInfo">
			<div class="block-outer-bd">
				<div class="inline-block block-indent">
					<div class="item">
                        <div class="form-group">
							<label for="price" class="control-label">供货商公司名</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_SUPPLIER_COMPANY');?>" name="<?php echo C('DB_SUPPLIER_COMPANY');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_COMPANY')]); ?>" />
								<input type="hidden"  id="<?php echo C('DB_SUPPLIER_ID');?>" name="<?php echo C('DB_SUPPLIER_ID');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_ID')]); ?>" />
							</div>
						</div>
						<div class="form-group checkbox">
							<label for="IsBattery" class="control-label">联系人</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_SUPPLIER_PERSON');?>" name="<?php echo C('DB_SUPPLIER_PERSON');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_PERSON')]); ?>" />
							</div>							
						</div>
					</div>
					<div class="item">
						<div class="form-group">
							<label for="Name" class="control-label">旺旺</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_SUPPLIER_WANGWANG');?>" id="<?php echo C('DB_SUPPLIER_WANGWANG');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_WANGWANG')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="EName" class="control-label">QQ</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_SUPPLIER_QQ');?>" id="<?php echo C('DB_SUPPLIER_QQ');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_QQ')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="weight" class="control-label">电话</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_SUPPLIER_TEL');?>" name="<?php echo C('DB_SUPPLIER_TEL');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_TEL')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="length" class="control-label">网址</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_SUPPLIER_WEBSITE');?>" name="<?php echo C('DB_SUPPLIER_WEBSITE');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_WEBSITE')]); ?>" />
							</div>
						</div>
					</div>
                    <div class="item">
                        <div class="form-group">
							<label for="width" class="control-label">地址</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_SUPPLIER_ADDRESS');?>" name="<?php echo C('DB_SUPPLIER_ADDRESS');?>" value="<?php echo ($supplier[0][C('DB_SUPPLIER_ADDRESS')]); ?>" />
							</div>
						</div>
                    </div>
				</div>
			</div>
		</div>
		<div class="item tc">
			<a class="btn btn-s btn-grey" href="javascript:history.back();">返回</a>
			<button class="btn btn-blue btn-s" id="saveProductInfo" onclick="return checkForm()">保存</button>
		</div>
	</form> 
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