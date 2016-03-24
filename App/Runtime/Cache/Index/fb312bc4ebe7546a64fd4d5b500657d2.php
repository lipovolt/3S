<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
	<title>3S 管理平台</title>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="format-detection" content="telephone=no" />
	<link rel="stylesheet" href="3S/Public/base.css">
	<link rel="stylesheet" href="3S/Public/zh-cn.css">
	<!--[if IE 7]>
	<link rel="stylesheet" href="/Public/styles/ie7hack.css">
	<![endif]-->
</head>
<body>
<div class="container">

	

<div class="header">
	<div class="top">
		<div class="area">
			<span>
				<i class="icon user"></i>
				<span class="blue">498307481@qq.com</span>
				<a class="blue" style="margin:0px 10px;" href="/Index/Index/index.php">退出</a>
			</span>
		</div>
	</div>

	
		<div class="nav">
			<div class="area">
				<!-- 头部菜单 -->
				<ul class="mainnav"><li  class = "on"  >
	    	<a href="/Manage/index.php?s=/Index/index" mark="">
				<i class="icon MMP"></i>
				<span>首页</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>首页</strong>
								</dt><dd><a href="/Manage/index.php?s=/SystemNotify/index" >系统公告</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/ProductInfo/index" mark="">
				<i class="icon MPD"></i>
				<span>产品管理</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>产品分类管理</strong>
								</dt><dd><a href="/Manage/index.php?s=/Category/index" >产品分类管理</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>导入产品</strong>
								</dt><dd><a href="/Manage/index.php?s=/ProductInfo/batchAddImportProduct" >导入进口产品</a></dd><dd><a href="/Manage/index.php?s=/ProductInfo/batchAdd"  mark="export_product_import">导入出口产品</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>产品信息管理</strong>
								</dt><dd><a href="/Manage/index.php?s=/ProductInfo/index" >产品信息</a></dd><dd><a href="/Manage/index.php?s=/ViewProductInfo/index" >产品维护信息</a></dd><dd><a href="/Manage/index.php?s=/BatchPrintBarcodes/index" >批量打印单品条码</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/Australia/index" mark="Outbound">
				<i class="icon MWO"></i>
				<span>库存</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>库存管理</strong>
								</dt><dd><a href="/Manage/index.php?s=/Australia/index"  mark="Outbound">海外仓库存</a></dd><dd><a href="/Manage/index.php?s=/ReturnWarehouse/index"  mark="Outbound">退货仓库存</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>库间调拨</strong>
								</dt><dd><a href="/Manage/index.php?s=/TransferringOrder/stepOne"  mark="Outbound">下调拨单</a></dd><dd><a href="/Manage/index.php?s=/TransferringOrder/index"  mark="Outbound">调拨单管理</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>库存共享</strong>
								</dt><dd><a href="/Manage/index.php?s=/InventoryShare/stepOne"  mark="Outbound">新增库存共享</a></dd><dd><a href="/Manage/index.php?s=/InventoryShare/index"  mark="Outbound">库存共享管理</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>智能补货</strong>
								</dt><dd><a href="/Manage/index.php?s=/Replenishment/index"  mark="Outbound">智能补货计划</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/WarehouseOrder/index" mark="Outbound">
				<i class="icon MWI"></i>
				<span>海外仓</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>入库</strong>
								</dt><dd><a href="/Manage/index.php?s=/WarehouseOrder/stepOne"  mark="Outbound">新增入库单</a></dd><dd><a href="/Manage/index.php?s=/WarehouseOrder/index"  mark="Outbound">全部订单</a></dd><dd><a href="/Manage/index.php?s=/WarehouseMerger/index"  mark="Outbound">已合并订单</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>出库</strong>
								</dt><dd><a href="/Manage/index.php?s=/WarehouseOutbound/stepOne"  mark="Outbound">单个录入</a></dd><dd><a href="/Manage/index.php?s=/WarehouseOutbound/batchImport"  mark="Outbound">批量上传</a></dd><dd><a href="/Manage/index.php?s=/WarehouseOutbound/index"  mark="Outbound">全部订单</a></dd><dd><a href="/Manage/index.php?s=/UncommittedOutbound/index"  mark="Outbound">未提交订单</a></dd><dd><a href="/Manage/index.php?s=/WinitRebateOrder/index"  mark="Outbound">Winit返利订单</a></dd><dd><a href="/Manage/index.php?s=/ReturnOfGoods/index"  mark="Outbound">退货订单</a></dd><dd><a href="/Manage/index.php?s=/ConsignmentOutboundOrder/stepOne"  mark="Outbound">下代销出库单</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>统计视图及工具</strong>
								</dt><dd><a href="/Manage/index.php?s=/OutboundOnTime/index"  mark="Outbound">出库单及时率</a></dd><dd><a href="/Manage/index.php?s=/InboundOntime/index"  mark="Outbound">入库单及时率</a></dd><dd><a href="/Manage/index.php?s=/QuickQuote/index"  mark="Outbound">计价工具</a></dd><dd><a href="/Manage/index.php?s=/IntelligentizeStorage/index"  mark="Outbound">仓储费用分析</a></dd><dd><a href="/Manage/index.php?s=/QueryPostCode/index"  mark="Outbound">邮编查询工具</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/GlobalTransfer/index" mark="Outbound">
				<i class="icon GlobalTransfer"></i>
				<span>国际送仓</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>国际送仓</strong>
								</dt><dd><a href="/Manage/index.php?s=/GlobalTransfer/stepOne"  mark="Outbound">新增订单</a></dd><dd><a href="/Manage/index.php?s=/GlobalTransfer/index"  mark="Outbound">全部订单</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/ISPOrder/index" mark="ISP">
				<i class="icon ISP"></i>
				<span>ISP</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>ISP订单（出口）</strong>
								</dt><dd><a href="/Manage/index.php?s=/ISPOrder/stepOne"  mark="ISP">新增订单</a></dd><dd><a href="/Manage/index.php?s=/ISPOrder/batchImport"  mark="ISP">批量导入订单</a></dd><dd><a href="/Manage/index.php?s=/ISPOrder/index"  mark="ISP">全部订单</a></dd><dd><a href="/Manage/index.php?s=/ISPOrder/uncommitted"  mark="ISP">未提交订单</a></dd><dd><a href="/Manage/index.php?s=/ISPOrder/generateQRCode"  mark="ISP">打印揽收信息</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>ISP订单（进口）</strong>
								</dt><dd><a href="/Manage/index.php?s=/ISPImport/batchImport"  mark="ISP">批量导入订单</a></dd><dd><a href="/Manage/index.php?s=/ISPImport/index"  mark="ISP">全部订单</a></dd><dd><a href="/Manage/index.php?s=/ISPImport/uncommitted"  mark="ISP">未提交订单</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>统计视图及工具</strong>
								</dt><dd><a href="/Manage/index.php?s=/TaxCalculator/index"  mark="ISP">税金计算器</a></dd><dd><a href="/Manage/index.php?s=/QuoteTools/index"  mark="ISP">费用计算器</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/GFSOrdersManage/index" mark="">
				<i class="icon GFS"></i>
				<span>平台同步</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>eBay</strong>
								</dt><dd><a href="/Manage/index.php?s=/EbayAccount/index" >账号管理</a></dd><dd><a href="/Manage/index.php?s=/UserCenter/ebayOrdersSetting" >同步基础配置</a></dd><dd><a href="/Manage/index.php?s=/StockConfig/index" >库存配置</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>订单管理</strong>
								</dt><dd><a href="/Manage/index.php?s=/GFSOrdersManage/index" >订单管理</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>自动发货规则配置</strong>
								</dt><dd><a href="/Manage/index.php?s=/AutoRulesSetting/index" >配置发货规则详情</a></dd></dl></div></li><li  >
	    	<a href="/Manage/index.php?s=/Balance/index" mark="">
				<i class="icon MSE"></i>
				<span>结算管理</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>账户信息</strong>
								</dt><dd><a href="/Manage/index.php?s=/Balance/index" >我的账户</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>充值</strong>
								</dt><dd><a href="/Manage/index.php?s=/OnlineRecharge/index" >线上充值</a></dd><dd><a href="/Manage/index.php?s=/Recharge/add" >线下充值</a></dd><dd><a href="/Manage/index.php?s=/Recharge/index" >线下充值单</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>交易明细</strong>
								</dt><dd><a href="/Manage/index.php?s=/OutboundDetail/index" >出库单交易明细</a></dd><dd><a href="/Manage/index.php?s=/WarehouseTradeDetail/index" >入库单交易明细</a></dd><dd><a href="/Manage/index.php?s=/StorageCharge/index" >仓储收费交易明细</a></dd><dd><a href="/Manage/index.php?s=/IspTradeDetail/index"  mark="ISP">ISP交易明细</a></dd><dd><a href="/Manage/index.php?s=/TransferOrderDetail/index" >库间调拨单交易明细</a></dd><dd><a href="/Manage/index.php?s=/InvoiceCharge/index" >充值交易明细</a></dd><dd><a href="/Manage/index.php?s=/Freeze/index" >冻结明细</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>帐户操作记录</strong>
								</dt><dd><a href="/Manage/index.php?s=/Record/index" >帐户操作记录</a></dd><dd><a href="/Manage/index.php?s=/DownloadStatement/index" >下载对账单</a></dd></dl></div></li><li  >
	    	<a href="/Finance/index.php" mark="">
				<i class="icon FSP"></i>
				<span>金融服务</span>
			</a>
			<div class="subnav"></div></li><li  >
	    	<a href="/Manage/index.php?s=/Index/todolist" mark="">
				<i class="icon USER"></i>
				<span>个人中心</span>
			</a>
			<div class="subnav"><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>系统设置</strong>
								</dt><dd><a href="/Manage/index.php?s=/Index/account" >修改账号</a></dd><dd><a href="/Manage/index.php?s=/Index/password" >修改密码</a></dd><dd><a href="/Manage/index.php?s=/UserCenter/unitsetting" >计量单位设置</a></dd><dd><a href="/Manage/index.php?s=/UserCenter/reportManage" >报表管理</a></dd><dd><a target="_blank" href="http://coupon.winit.com.cn/couponweb/index.jsp?token=635170549622F897DDD019696C3678AC&username=498307481@qq.com">积分及优惠券</a></dd><dd><a href="/Manage/index.php?s=/UserCenter/userinfo" >我的资料</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>服务设置</strong>
								</dt><dd><a href="/Manage/index.php?s=/Address/index" >地址管理</a></dd><dd><a href="/Manage/index.php?s=/IorEor/index" >出口商/进口商管理</a></dd></dl><dl>
								<dt>
									<i class="icon dropdown-s"></i>
									<strong>待办&消息</strong>
								</dt><dd><a href="/Manage/index.php?s=/Index/todolist" >待办事项</a></dd></dl></div></li></ul>
				<a href="/Manage/index.php?s=/Help/index/" class="help" target="_blank">帮助</a>

			</div>
		</div>
		<input type="hidden" name="agreements" id="agreements"	data-title="您暂时未开通{service}服务，请联系客服开通此服务!"
		value="Outbound ">
        
        <input type="hidden" id="supplierProtocal" data-title='暂未签署更新后的供应链管理协议，无法使用该功能。<br/>请点击<a  href="/Manage/index.php?s=/UserCenter/userinfo">这里</a>签署。' value="Y" />
	</div>
	<div class="area footer">
		Powered by 3S 2015 Shangsi CORPORATION. All Rights Reserved.
	</div>
</div>
</body>
</html>