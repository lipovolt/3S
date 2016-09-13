<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>修改产品信息</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" href="__PUBLIC__/Css/base.css">
<link rel="stylesheet" href="__PUBLIC__/Css/zh-cn.css">
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
<script type="text/javascript">
function GenerateUpc(){

	var upc = document.getElementById("upc").value;
	if(upc==0 || upc==null){
		var used_upc = document.getElementById("used_upc").value;
		var oddSum=parseInt(used_upc.substr(0,1))+parseInt(used_upc.substr(2,1))+parseInt(used_upc.substr(4,1))+parseInt(used_upc.substr(6,1))+parseInt(used_upc.substr(8,1))+parseInt(used_upc.substr(10,1))+1;
	    var evenSum=parseInt(used_upc.substr(1,1))+parseInt(used_upc.substr(3,1))+parseInt(used_upc.substr(5,1))+parseInt(used_upc.substr(7,1))+parseInt(used_upc.substr(9,1));
	    var sum=oddSum*3+evenSum;
	    var verifyNumber = 10-parseInt(String(sum).substr(String(sum).length-1,1));
	    var int_upc = parseInt(used_upc.substr(0,11))+1;
	    if(verifyNumber==10){
	        document.getElementById("upc").value= String(int_upc)+0;
	    }else{
	        document.getElementById("upc").value= String(int_upc)+verifyNumber;
	    }
	}else{
		var oddSum=parseInt(upc.substr(0,1))+parseInt(upc.substr(2,1))+parseInt(upc.substr(4,1))+parseInt(upc.substr(6,1))+parseInt(upc.substr(8,1))+parseInt(upc.substr(10,1))+1;
	    var evenSum=parseInt(upc.substr(1,1))+parseInt(upc.substr(3,1))+parseInt(upc.substr(5,1))+parseInt(upc.substr(7,1))+parseInt(upc.substr(9,1));
	    var sum=oddSum*3+evenSum;
	    var verifyNumber = 10-parseInt(String(sum).substr(String(sum).length-1,1));
	    var int_upc = parseInt(upc.substr(0,11))+1;
	    if(verifyNumber==10){
	        document.getElementById("upc").value= String(int_upc)+0;
	    }else{
	        document.getElementById("upc").value= String(int_upc)+verifyNumber;
	    }
	}
}
</script>
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
	<dd><a href="<?php echo U('Sale/GgsUsswSale/usswSaleSuggest');?>" >美国自建仓销售建议表</a></dd>
	<dd><a href="<?php echo U('Sale/GgsUsswSale/index');?>" >美国自建仓销售表</a></dd>
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
<dl>
	<dt><i class="icon dropdown-s"></i><strong>邮费管理</strong></dt>
	<dd ><a href="<?php echo U('Ussw/Postage/firstclass');?>">USPS First Class</a></dd>
	<dd > </dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priorityflatrate');?>">USPS Priority Falt Rate</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/priority');?>">USPS Priority</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexSmartPost');?>">Fedex Smart Post</a></dd>
	<dd ><a href="<?php echo U('Ussw/Postage/fedexHomeDelivery');?>">Fedex Home Delivery</a></dd>
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
			<div class="sidenav-hd"><strong>产品管理</strong></div>
			<div class="sidenav-bd">
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
		</div>
	<div class="content">
	<div id="ProductInfo" class="main">
		<form method="POST" id="edit_productInfo" action="<?php echo U('Product/Product/update');?>">
		<div class="block-outer BaseInfo">
			<div class="block-outer-bd">
				<div class="inline-block block-indent">
					<div class="item">
						<div class="form-group">
							<label for="sku" class="control-label">产品编码</label>
							<div class="control-wrap">
								<input type="text" name="sku" value="<?php echo ($product[0][C('DB_PRODUCT_SKU')]); ?>" id="sku" readonly="true"/>
								<input type="hidden" name="<?php echo C('DB_PRODUCT_ID');?>" value="<?php echo ($product[0][C('DB_PRODUCT_ID')]); ?>" id="ProductCode"  />
							</div>
						</div>
						<div class="form-group">
							<label for="manager" class="control-label">产品经理</label>
							<div class="control-wrap">
								<select name="<?php echo C('DB_PRODUCT_MANAGER');?>"  id="<?php echo C('DB_PRODUCT_MANAGER');?>">
									<?php  if($product[0][C('DB_PRODUCT_MANAGER')] == '张昱'){ echo '<option value="" >请选择</option>
												<option value="张昱" selected>张昱</option>
												<option value="杨杰" >杨杰</option>
												<option value="王宁" >王宁</option>'; }elseif($product[0]['manager'] == '杨杰'){ echo '<option value="" >请选择</option>
												<option value="张昱" >张昱</option>
												<option value="杨杰" selected>杨杰</option>
												<option value="王宁" >王宁</option>'; }elseif($product[0]['manager'] == '王宁'){ echo '<option value="" >请选择</option>
												<option value="张昱" >张昱</option>
												<option value="杨杰" >杨杰</option>
												<option value="王宁" selected>王宁</option>'; }else{ echo '<option value="" selected>请选择</option>
												<option value="张昱" >张昱</option>
												<option value="杨杰" >杨杰</option>
												<option value="王宁" >王宁</option>'; } ?>							
								</select>
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="price" class="control-label">采购价￥</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_PRICE');?>" name="<?php echo C('DB_PRODUCT_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_PRICE')]); ?>" />
							</div>
						</div>
						<div class="form-group checkbox">
							<label for="IsBattery" class="control-label">有电池</label>
							<div class="control-wrap">
								<select name="<?php echo C('DB_PRODUCT_BATTERY');?>"  id="<?php echo C('DB_PRODUCT_BATTERY');?>">
									<?php  if($product[0][C('DB_PRODUCT_BATTERY')] == '不带电'){ echo '<option value="" >请选择</option>
												<option value="不带电" selected>不带电</option>
												<option value="内置电" >内置电</option>
												<option value="纯电" >纯电</option>'; }elseif($product[0][C('DB_PRODUCT_BATTERY')] == '内置电'){ echo '<option value="" >请选择</option>
												<option value="不带电" >不带电</option>
												<option value="内置电" selected>内置电</option>
												<option value="纯电" >纯电</option>'; }elseif($product[0][C('DB_PRODUCT_BATTERY')] == '纯电'){ echo '<option value="" >请选择</option>
												<option value="不带电" >不带电</option>
												<option value="内置电" >内置电</option>
												<option value="纯电" selected>纯电</option>'; }else{ echo '<option value="" selected>请选择</option>
												<option value="不带电" >不带电</option>
												<option value="内置电" >内置电</option>
												<option value="纯电" >纯电</option>'; } ?>							
								</select>
							</div>							
						</div>
					</div>
					<div class="item">
						<div class="form-group">
							<label for="Name" class="control-label">中文名称</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_PRODUCT_CNAME');?>" id="<?php echo C('DB_PRODUCT_CNAME');?>" value="<?php echo ($product[0][C('DB_PRODUCT_CNAME')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="EName" class="control-label">英文名称</label>
							<div class="control-wrap">
								<input type="text" name="<?php echo C('DB_PRODUCT_ENAME');?>" id="<?php echo C('DB_PRODUCT_ENAME');?>" value="<?php echo ($product[0][C('DB_PRODUCT_ENAME')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="weight" class="control-label">重量g</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_WEIGHT');?>" name="<?php echo C('DB_PRODUCT_WEIGHT');?>" value="<?php echo ($product[0][C('DB_PRODUCT_WEIGHT')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="length" class="control-label">长cm</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_LENGTH');?>" name="<?php echo C('DB_PRODUCT_LENGTH');?>" value="<?php echo ($product[0][C('DB_PRODUCT_LENGTH')]); ?>" />
							</div>
						</div>
					</div>
                    <div class="item">
                        <div class="form-group">
							<label for="width" class="control-label">宽cm</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_WIDTH');?>" name="<?php echo C('DB_PRODUCT_WIDTH');?>" value="<?php echo ($product[0][C('DB_PRODUCT_WIDTH')]); ?>" />
							</div>
						</div>
                        <div class="form-group">
							<label for="height" class="control-label">高cm</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_HEIGHT');?>" name="<?php echo C('DB_PRODUCT_HEIGHT');?>" value="<?php echo ($product[0][C('DB_PRODUCT_HEIGHT')]); ?>" />
							</div>
						</div>
                    </div>
                    <div class="item">
                        <div class="form-group">
							<label for="way-to-de" class="control-label">德国头程方式</label>
							<div class="control-wrap">
								<select name="<?php echo C('DB_PRODUCT_TODE');?>"  id="<?php echo C('DB_PRODUCT_TODE');?>">
									<?php  if($product[0][C('DB_PRODUCT_TODE')] == '空运'){ echo '<option value="" >请选择</option>
												<option value="空运" selected>空运</option>
												<option value="海运" >海运</option>
												<option value="无" >无</option>'; }elseif($product[0][C('DB_PRODUCT_TODE')] == '海运'){ echo '<option value="" >请选择</option>
												<option value="空运" >空运</option>
												<option value="海运" selected>海运</option>
												<option value="无" >无</option>'; }elseif($product[0][C('DB_PRODUCT_TODE')] == '无'){ echo '<option value="" >请选择</option>
												<option value="空运" >空运</option>
												<option value="海运" >海运</option>
												<option value="无" selected>无</option>'; }else{ echo '<option value="" selected>请选择</option>
												<option value="空运" >空运</option>
												<option value="海运" >海运</option>
												<option value="无" >无</option>'; } ?>							
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="width" class="control-label">德国关税率</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_DETARIFF');?>" name="<?php echo C('DB_PRODUCT_DETARIFF');?>" value="<?php echo ($product[0][C('DB_PRODUCT_DETARIFF')]); ?>" />
							</div>
						</div>
                    </div>
                    <div class="item">
                        <div class="form-group">
							<label for="way-to-us" class="control-label">美国头程方式</label>
							<div class="control-wrap">
								<select name="<?php echo C('DB_PRODUCT_TOUS');?>"  id="<?php echo C('DB_PRODUCT_TOUS');?>">
									<?php  if($product[0][C('DB_PRODUCT_TOUS')] == '空运'){ echo '<option value="" >请选择</option>
												<option value="空运" selected>空运</option>
												<option value="海运" >海运</option>
												<option value="无" >无</option>'; }elseif($product[0][C('DB_PRODUCT_TOUS')] == '海运'){ echo '<option value="" >请选择</option>
												<option value="空运" >空运</option>
												<option value="海运" selected>海运</option>
												<option value="无" >无</option>'; }elseif($product[0][C('DB_PRODUCT_TOUS')] == '无'){ echo '<option value="" >请选择</option>
												<option value="空运" >空运</option>
												<option value="海运" >海运</option>
												<option value="无" selected>无</option>'; }else{ echo '<option value="" selected>请选择</option>
												<option value="空运" >空运</option>
												<option value="海运" >海运</option>
												<option value="无" >无</option>'; } ?>							
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="height" class="control-label">美国关税率</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_USTARIFF');?>" name="<?php echo C('DB_PRODUCT_USTARIFF');?>" value="<?php echo ($product[0][C('DB_PRODUCT_USTARIFF')]); ?>" />
							</div>
						</div>
                    </div>
					<div class="item">
                        <div class="form-group">
							<label for="ggs-ussw-sp" class="control-label">greatgoodshop美自建仓售价$</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_GGS_USSW_SALE_PRICE');?>" name="<?php echo C('DB_PRODUCT_GGS_USSW_SALE_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="ggs-ussw-sp" class="control-label">rc-helicar万邑通美国仓售价$</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE');?>" name="<?php echo C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="length" class="control-label">rc-helicar万邑通德国仓售价€</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE');?>" name="<?php echo C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="ggs-ussw-sp" class="control-label">亚马逊美国仓售价$</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_AMAZON_USSW_SALE_PRICE');?>" name="<?php echo C('DB_PRODUCT_AMAZON_USSW_SALE_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_AMAZON_USSW_SALE_PRICE')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="upc" class="control-label">UPC 生成之后需要保存</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_UPC');?>" name="<?php echo C('DB_PRODUCT_UPC');?>" value="<?php echo ($product[0][C('DB_PRODUCT_UPC')]); ?>" />
								<input type="hidden"  id="<?php echo C('DB_METADATA_USED_UPC');?>" name="<?php echo C('DB_METADATA_USED_UPC');?>" value="<?php echo ($product[0][C('DB_METADATA_USED_UPC')]); ?>" />
							</div>
							<a class="btn btn-s btn-grey" href="javascript:GenerateUpc();">生成</a>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="sz-us-sp" class="control-label">直发美国售价$</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_SZ_US_SALE_PRICE');?>" name="<?php echo C('DB_PRODUCT_SZ_US_SALE_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_SZ_US_SALE_PRICE')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="sz-de-sp" class="control-label">直发德国售价€</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_SZ_DE_SALE_PRICE');?>" name="<?php echo C('DB_PRODUCT_SZ_DE_SALE_PRICE');?>" value="<?php echo ($product[0][C('DB_PRODUCT_SZ_DE_SALE_PRICE')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="ebaycombest" class="control-label">ebay.com 销量链接</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_EBAY_COM_BEST_MATCH');?>" name="<?php echo C('DB_PRODUCT_EBAY_COM_BEST_MATCH');?>" value="<?php echo ($product[0][C('DB_PRODUCT_EBAY_COM_BEST_MATCH')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="ebaycomcheapest" class="control-label">ebay.com 最低价链接</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_EBAY_COM_PRICE_LOWEST');?>" name="<?php echo C('DB_PRODUCT_EBAY_COM_PRICE_LOWEST');?>" value="<?php echo ($product[0][C('DB_PRODUCT_EBAY_COM_PRICE_LOWEST')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">
                        <div class="form-group">
							<label for="ebaydebest" class="control-label">ebay.de 销量链接</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_EBAY_DE_BEST_MATCH');?>" name="<?php echo C('DB_PRODUCT_EBAY_DE_BEST_MATCH');?>" value="<?php echo ($product[0][C('DB_PRODUCT_EBAY_DE_BEST_MATCH')]); ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="ebaydecheapest" class="control-label">ebay.de 最低价链接</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_EBAY_DE_PRICE_LOWEST');?>" name="<?php echo C('DB_PRODUCT_EBAY_DE_PRICE_LOWEST');?>" value="<?php echo ($product[0][C('DB_PRODUCT_EBAY_DE_PRICE_LOWEST')]); ?>" />
							</div>
						</div>
					</div>
					<div class="item">						
						<div class="form-group">
							<label for="height" class="control-label">供货商编号</label>
							<div class="control-wrap">
								<input type="text"  id="<?php echo C('DB_PRODUCT_SUPPLIER');?>" name="<?php echo C('DB_PRODUCT_SUPPLIER');?>" value="<?php echo ($product[0][C('DB_PRODUCT_SUPPLIER')]); ?>" />
							</div>
						</div>  
					</div>
				</div>
			</div>
		</div>
		<div class="item tc"><input type='hidden' name='ProductID' value='1030634'>
			<a class="btn btn-s btn-grey" href="javascript:history.back();">返回</a>
			<button class="btn btn-blue btn-s" id="saveProductInfo" name="saveProductInfo">保存	</button>
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