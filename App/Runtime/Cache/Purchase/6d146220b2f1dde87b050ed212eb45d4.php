<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>编辑采购单</title>
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
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'waiting confirm'));?>" >待确认</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'waiting pay'));?>" >待付款</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'no arrival'));?>" >待收货</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_CANCEL')=>1));?>" >已取消</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>供货商</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Supplier/index');?>" >供货商信息</a></dd>
</dl>
		</div>
	</li>
	<li>
		<a href="#" mark="ussw"><span>美国自建仓</span></a>
		<div class="subnav">
			<dl>
	<dt><i class="icon dropdown-s"></i><strong>入库管理</strong></dt>
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
	<dd ><a href="<?php echo U('Ussw/Storage/add');?>">新增库存</a></dd>
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
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'waiting confirm'));?>" >待确认</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'waiting pay'));?>" >待付款</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_STATUS')=>'no arrival'));?>" >待收货</a></dd>
	<dd><a href="<?php echo U('Purchase/Purchase/index',array(C('DB_PURCHASE_CANCEL')=>1));?>" >已取消</a></dd>
</dl>
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>供货商</strong>								
	</dt>
	<dd><a href="<?php echo U('Purchase/Supplier/index');?>" >供货商信息</a></dd>
</dl>	
			</div>
		</div>
	<div class="content">
<!-- 主页面开始  -->
<div id="WarehouseOutbound" class="main">
<script>
function checkForm()
{
	var status = document.getElementById("status").innerText;
    if(status.trim()==("待确认") || status.trim()=="已确认" || status.trim()=="待付款"){
    	return true;
    }else{
    	alert("已付款采购单只更新到货数量和目的仓！");
    	return false;
    }
}
</script>

<!--基本信息-->
<div class="block-outer">
    <div class="block-outer-hd">
        <strong>基本信息</strong>
    </div>
    <form method="POST" id="edit_productImg" action="<?php echo U('Purchase/Purchase/updatePurchaseOrder');?>" onsubmit="return checkForm()">
    <div class="block-outer-bd viewBaseCheckStatus">
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">采购单编号</label>
                <div class="control-wrap">
                	<span><?php echo ($purchaseOrder[0][C('DB_PURCHASE_ID')]); ?></span>
                	<input type="hidden" id="<?php echo C('DB_PURCHASE_ID');?>" name="<?php echo C('DB_PURCHASE_ID');?>" value="<?php echo ($purchaseOrder[0][C('DB_PURCHASE_ID')]); ?>"></input>
               	</div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">状态：</label>
                <div class="control-wrap" id="status">
                	<span><?php echo ($purchaseOrder[0][C('DB_PURCHASE_STATUS')]); ?></span>
                </div>
            </div>
        </div>
        
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">下单日期：</label>
                <div class="control-wrap">
                	<span><?php echo ($purchaseOrder[0][C('DB_PURCHASE_CREATE_DATE')]); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">采购日期：</label>
                <div class="control-wrap">
                	<span><?php echo ($purchaseOrder[0][C('DB_PURCHASE_PURCHASED_DATE')]); ?></span>
                </div>
            </div>
        </div>        
       <div class="item">
            <div class="form-group">
                <label for="" class="control-label">订单号：</label>
                <div class="control-wrap">
                	<input id="<?php echo C('DB_PURCHASE_ORDER_NUMBER');?>" name="<?php echo C('DB_PURCHASE_ORDER_NUMBER');?>" value="<?php echo ($purchaseOrder[0][C('DB_PURCHASE_ORDER_NUMBER')]); ?>"></input>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">快递单号：</label>
                <div class="control-wrap">
                	<input id="<?php echo C('DB_PURCHASE_TRACKING_NUMBER');?>" name="<?php echo C('DB_PURCHASE_TRACKING_NUMBER');?>" value="<?php echo ($purchaseOrder[0][C('DB_PURCHASE_TRACKING_NUMBER')]); ?>"></input>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">运费：</label>
                <div class="control-wrap">
                	<input id="<?php echo C('DB_PURCHASE_SHIPPING_FEE');?>" name="<?php echo C('DB_PURCHASE_SHIPPING_FEE');?>" value="<?php echo ($purchaseOrder[0][C('DB_PURCHASE_SHIPPING_FEE')]); ?>"></input>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">备注：</label>
                <div class="control-wrap">
                	<input id="<?php echo C('DB_PURCHASE_REMARK');?>" name="<?php echo C('DB_PURCHASE_REMARK');?>" value="<?php echo ($purchaseOrder[0][C('DB_PURCHASE_REMARK')]); ?>"></input>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">产品经理：</label>
                <div class="control-wrap">
                	<select name="<?php echo C('DB_PURCHASE_MANAGER');?>"  id="<?php echo C('DB_PURCHASE_MANAGER');?>">
						<option value="unSelected" >请选择</option>
						<option value="张昱" <?php echo $purchaseOrder[0][C('DB_PURCHASE_MANAGER')]=="张昱"?selected:'' ?>>张昱</option>
						<option value="杨杰" <?php echo $purchaseOrder[0][C('DB_PURCHASE_MANAGER')]=="杨杰"?selected:'' ?>>杨杰</option>
						<option value="王宁" <?php echo $purchaseOrder[0][C('DB_PURCHASE_MANAGER')]=="王宁"?selected:'' ?>>王宁</option>		
					</select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">订单总价格：</label>
                <div class="control-wrap">
                	<?php echo ($total); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="item tc"><input type='hidden' name='ProductID' value='1030634'>
	<a class="btn btn-s btn-grey" href="javascript:history.back();">返回</a>
	<input type="submit" class="btn btn-blue btn-s" value="保存基本信息" />
</div>
</form>
</div>
<!--订单信息-->
<div class="block-outer viewBillingCheckStatus">
    <div class="block-outer-hd">
        <strong>供应商信息</strong>
    </div>
    <div class="block-indent">
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">公司名：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_COMPANY')]); ?></span>
		         </div>
		     </div>
		     
		     <div class="form-group">
		         <label for="" class="control-label">联系人：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_PERSON')]); ?></span>
		         </div>
		     </div>
		</div>
		
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">旺旺：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_WANGWANG')]); ?></span>
		         </div>
		     </div>
		     <div class="form-group">
		         <label for="" class="control-label">QQ：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_QQ')]); ?></span>
		         </div>
		     </div>
		</div>
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">电话：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_TEL')]); ?></span>
		         </div>
		     </div>
		     <div class="form-group">
		         <label for="" class="control-label">网址：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_WEBSITE')]); ?></span>
		         </div>
		     </div>
		</div>
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">地址：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($supplier[0][C('DB_SUPPLIER_ADDRESS')]); ?></span>
		         </div>
		     </div>
		</div>
	</div>
</div>
<!--产品列表-->
<div class="block-outer">
	<div class="block-outer-hd">
		<strong>产品列表</strong>
		<a class="btn btn-blue btn-s" href="<?php echo U('Purchase/Purchase/addPurchaseItem',array(C('DB_PURCHASE_ID')=>$purchaseOrder[0][C('DB_PURCHASE_ID')]));?>" onclick='return checkForm()'>新增产品</a>
    </div>
	<table  id="warehouseProduct" class="tablelist">              
		
			<tr>
				<th>商品编码</th>
				<th>单价</th>				
				<th>采购数量</th>
				<th>到货数量</th>
				<th>仓库</th>
				<th>运输方式</th>
				<th>操作</th>
			</tr> 

			<?php if(is_array($purchaseItem)): $i = 0; $__LIST__ = $purchaseItem;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><form method="POST" id="edit_purchaseItem" action="<?php echo U('Purchase/Purchase/updatePurchaseItem',array(C('DB_PURCHASE_ITEM_ID')=>$vo[C('DB_PURCHASE_ITEM_ID')]));?>">
				<tr>
				<td><input id="<?php echo C('DB_PURCHASE_ITEM_SKU');?>" name="<?php echo C('DB_PURCHASE_ITEM_SKU');?>" value="<?php echo ($vo[C('DB_PURCHASE_ITEM_SKU')]); ?>"></input>
					<input type="hidden" id="<?php echo C('DB_PURCHASE_ITEM_ID');?>" name="<?php echo C('DB_PURCHASE_ITEM_ID');?>" value="<?php echo ($vo[C('DB_PURCHASE_ITEM_ID')]); ?>"></input>
				</td>
				<td><input id="<?php echo C('DB_PURCHASE_ITEM_PRICE');?>" name="<?php echo C('DB_PURCHASE_ITEM_PRICE');?>" value="<?php echo ($vo[C('DB_PURCHASE_ITEM_PRICE')]); ?>"></input></td>
				<td><input id="<?php echo C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY');?>" name="<?php echo C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY');?>" value="<?php echo ($vo[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')]); ?>"></input></td>
				<td><input id="<?php echo C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY');?>" name="<?php echo C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY');?>" value="<?php echo ($vo[C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY')]); ?>"></input></td>
				<td><input id="<?php echo C('DB_PURCHASE_ITEM_WAREHOUSE');?>" name="<?php echo C('DB_PURCHASE_ITEM_WAREHOUSE');?>" value="<?php echo ($vo[C('DB_PURCHASE_ITEM_WAREHOUSE')]); ?>"></input>
				</td>
				<td><?php echo ($vo[C('DB_PURCHASE_ITEM_TRANSPORT_METHOD')]); ?></td>
				<td>
					<button class="btn btn-blue btn-s" id="saveProductInfo" onclick="checkForm()">保存</button>
					<a class="btn btn-blue btn-s" href="<?php echo U('Purchase/Purchase/deletePurchaseItem',array(C('DB_PURCHASE_ITEM_ID')=>$vo[C('DB_PURCHASE_ITEM_ID')]));?>" onclick='return checkForm()'>删除</a>
				</td>
				</tr>
				</form><?php endforeach; endif; else: echo "" ;endif; ?>	
		</table>
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