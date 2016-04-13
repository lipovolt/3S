<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Untitled-14.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>美国出库单</title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" href="__PUBLIC__/Css/base.css">
<link rel="stylesheet" href="__PUBLIC__/Css/zh-cn.css">
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
<script>
function del()
{
    if(confirm("确定要删除吗？"))
    {
        return true;
    }
    else
    {
        return false;
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
		<i class="icon dropdown-s"></i><strong>美国仓库存</strong>								
	</dt>
	<dd><a href="<?php echo U('Storage/Storage/usstorage');?>"  mark="Outbound">自建仓库存</a></dd>
</dl><!-- 
<dl>
	<dt>
		<i class="icon dropdown-s"></i><strong>深圳仓库存</strong>								
	</dt>
	<dd><a href="#"  mark="Outbound">深圳仓库存</a></dd>
</dl> -->

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
		<a href="#" mark="USSW"><span>美国自建仓</span></a>
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
	<dd ><a href="<?php echo U('Ussw/Ussw/usswManage');?>">库存信息</a></dd>
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
			<div class="sidenav-hd"><strong>美国自建仓</strong></div>
			<div class="sidenav-bd">
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
	<dd ><a href="<?php echo U('Ussw/Ussw/usswManage');?>">库存信息</a></dd>
</dl>
	
			</div>
		</div>
	<div class="content">
<!-- 主页面开始  -->
<div id="WarehouseOutbound" class="main">
<!--基本信息-->
<div class="block-outer">
    <div class="block-outer-hd">
        <strong>基本信息</strong>
    </div>
    <div class="block-outer-bd viewBaseCheckStatus">
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">出库单编号</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_ID')]); ?></span>
               	</div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">状态：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_STATUS')]); ?></span>
                </div>
            </div>
        </div>
        
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">ebay卖家ID：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_SELLER_ID')]); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">ebay买家ID：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_ID')]); ?></span>
                </div>
            </div>
        </div>        
        
        <div class="item">
            <div class="form-group">
                <label for="" class="control-label">卖家订单号：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_MARKET_NO')]); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">派送供应商：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_SHIPPING_COMPANY')]); ?></span>
                </div>
            </div>
        </div>
       <div class="item">
            <div class="form-group">
                <label for="" class="control-label">发货方式：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_SHIPPING_WAY')]); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">快递单号：</label>
                <div class="control-wrap">
                	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_TRACKING_NUMBER')]); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--订单跟踪-->
<div class="block-outer">
    <div class="block-outer-hd">
        <strong>订单跟踪</strong>
    </div>
        <table class="tablelist">      
				<tr>
					<th>时间</th>
					<th>追踪信息</th>
					<th>地点</th>
				</tr>         
			<tr>
					<td colspan='4'>暂无数据资料</td>
				</tr>                  
					</table>
</div>
<!--订单信息-->
<div class="block-outer viewBillingCheckStatus">
    <div class="block-outer-hd">
        <strong>订单信息</strong>
    </div>
    <div class="block-indent">
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">收件人姓名：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_NAME')]); ?></span>
		         </div>
		     </div>
		     
		     <div class="form-group">
		         <label for="" class="control-label">收件人国家：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')]); ?></span>
		         </div>
		     </div>
		</div>
		
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">收件人电话：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_TEL')]); ?></span>
		         </div>
		     </div>
		     <div class="form-group">
		         <label for="" class="control-label">收件人邮编：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_ZIP')]); ?></span>
		         </div>
		     </div>
		</div>
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">收件人州：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_STATE')]); ?></span>
		         </div>
		     </div>
		     <div class="form-group">
		         <label for="" class="control-label">收件人城市：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_CITY')]); ?></span>
		         </div>
		     </div>
		</div>
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">收件人Email：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_EMAIL')]); ?></span>
		         </div>
		     </div>
		</div>
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">收件人街道1：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')]); ?></span>
		         </div>
		     </div>
		</div>
		<div class="item">
		     <div class="form-group">
		         <label for="" class="control-label">收件人街道2：</label>
		         <div class="control-wrap">
		         	<span><?php echo ($order[0][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')]); ?></span>
		         </div>
		     </div>
		</div>
			</div>
</div>
<!--产品列表-->
<div class="block-outer">
	<div class="block-outer-hd">
		<strong>商品列表</strong>
    </div>
	<table  id="warehouseProduct" class="tablelist">              
		
			<tr>
				<th>货位</th>
				<th>商品编码</th>
				<th>商品名称</th>				
				<th>英文名称</th>
				<th>出库数量</th>
				<th>平台交易号</th>
				<th>平台商品ID</th>
			</tr> 

			<?php if(is_array($outboundOrderItems)): $i = 0; $__LIST__ = $outboundOrderItems;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_POSITION')]); ?></td>
				<td><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_SKU')]); ?></td>
				<td><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_CNAME')]); ?></td>
				<td><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_ENAME')]); ?></td>						
				<td class="num"><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_QUANTITY')]); ?></td>
				<td><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]); ?></td>
				<td><?php echo ($vo[c('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]); ?></td>
				<td>
				</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>	
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