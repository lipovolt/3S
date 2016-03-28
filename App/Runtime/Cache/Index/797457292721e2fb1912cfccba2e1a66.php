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
								<dd><a href="<?php echo U('Index/Manage/productBatchAdd');?>" >导入产品</a></dd>
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
					<dd ><a href="<?php echo U('Index/Manage/productBatchAdd');?>">导入产品</a></dd>
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
<script type="text/javascript">
	//给index.js提供全局数据
	var GlobalData = {
		url : "/Manage/index.php?s=",
		option : "请选择"
	}
</script>

	<div id="ProductInfo" class="main">
		<form name="search_product" id="search_product" action="/Manage/index.php?s=/ProductInfo/index/" method="POST">
		<div class="search-area">
			<div class="item">
				<div class="form-group">
					<label for="keyword" class="control-label">关键字</label>
					<div class="control-wrap">
						<select name="keyword" id="keyword" data-value="">
							<option value="value">产品编码</option>
							<option value="name">产品名称</option>
						</select>
					</div>
					<div class="control-wrap">
						<input type="text" class="form-control"  name="keywordValue" id="keywordValue" value="">
					</div>
				</div>
				<input type="hidden" name="country" value="122" />
				<button class="btn btn-s btn-blue" onClick="search_product.submit();">
					<i class="icon search"></i>
					<i class="vline-inline"></i>
					<span>查询</span>
				</button>
			</div>			
		</div>
		<input type="hidden" name="__hash__" value="ff49ed719b3da9a91e3fa1b682fe6f2c_58292026a894f750e9cf920bd524eb81" /></form>
		<div>
			<div class="tab">
				<ul>
					<li>
						<i class="before"></i>
						<a href="/Manage/index.php?s=/ProductInfo/index/country/100">
							<span>美国</span>
						</a>
						<i class="after"></i>
					</li>
					<li>
						<i class="before"></i>
						<a href="/Manage/index.php?s=/ProductInfo/index/country/101">
							<span>德国</span>
						</a>
						<i class="after"></i>
					</li>					
				</ul>
			</div>
			<div class="tab-content">	
				<table id="tablelist" class="tablelist">
					<tr>
						<th width="110">产品编码</th>
						<th><div class="tl">中文名称</div></th>						
						<th><div class="tl">英文名称</div></th>
						<th><div class="tl">重量g</div></th>
						<th>长cm</th>
						<th>宽cm</th>
						<th><div class="tl">高cm</div></th>
						<th><div>带电</div></th>
						<th width="60">头程运输方式</th>
						<th width="60">产品经理</th>
						<th width="230">操作</th>
					</tr>
					<tr>
						<td><a href='/Manage/index.php?s=/ProductInfo/view/ProductID/1030634/CountryID/122'>12v 5a</a></td>
						<td><div class="tl"></div></td>
						<td><div class="tl">12v 5a 电源适配器</div></td>						<td><div class="tl"><a href='http://www.ebay.com/itm/AC-220V-TO-DC-12V-5A-60W-Power-Supply-AC-adaptor-EU-US-Line-Office-New-/290873513218?pt=US_Lighting_Parts_and_Accessories&var=&hash=item43b9699502' target='_blank'>12v 5a power supply</a></div></td>
						<td>&nbsp;</td>
						<td><div>WINIT承运</td>
						<td><div class="tl"></div></td>
						<td><div>CN</div></td>
						<td><input type='checkbox' class="checkbox" disabled='disabled' checked></td>
						<td><input type='checkbox' class="checkbox" disabled='disabled' checked></td>      
						<td>
							<a href="/Manage/index.php?s=/ProductInfo/edit/ProductID/1030634/CountryID/122">编辑</a>&nbsp;&nbsp;
							<a class="blue PrintSingCode" href="javascript:;" data-title="标签纸打印单品条码" data-href="/Manage/index.php?s=/ProductInfo/printNumberProduct/sku/M010000000000027008/count/">打印单品条码</a>
						</td>    	
					</tr>				
				</table>
				<div class="tr">
					<!-- 分页开始  -->
					<div id="pagination" class="pagination">
						<span>共1条记录</span>
						<span>
							<!--每页-->
							<select class="page-count-select" onchange='changePageCount(1,this.value);'>
                                                                        <option value='10'>10</option><option value='20' selected>20</option>
                                                                           <option value='50'>50</option>                                        <option value='100'>100</option>                                        <option value='200'>200</option>                            </select>
							条&nbsp;每页						</span>
						<a href="javascript:;" class="page-prev-to-first disabled" title="第一页"></a>
												
						<a href="javascript:;" class="page-prev disabled" title="上一页"></a>
												
						<span>1 / 1</span>
						
						<a href="javascript:;" class="page-next disabled" title="下一页"></a>
												
						<a href="javascript:;" class="page-next-to-last disabled" title="最后一页"></a>	
														
						<input type="text" maxlength="5"  id='goPage' value="1">
						
						<input type='button' class='btn btn-s btn-blue' onClick="listChangePage(1,document.getElementById('goPage').value)" value=' 跳转 '>
					</div>
				
					<script language="JavaScript">
					//指定当前组模块URL地址  
					var URL = '/Manage/index.php?s=/ProductInfo';
					var APP	 =	 '/Manage/index.php?s=';
					var PUBLIC = '/Public';
					
					function isInteger(str) {
						var regu = /^[-]{0,1}[0-9]{1,}$/;
						return regu.test(str);
					}


					//分页类
					function listChangePageKeydown(max,page,e) {
						e = e ? e : window.event;
						var keyCode = e.which ? e.which : e.keyCode;
							
						if ( keyCode == 13 ){
							if (!isInteger(page)){
								page = 1;
							}								
							if (page > max){
								page = max;
							}								
							if (page < 1){
								page = 1;
							}
							location.href='/Manage/index.php?s=/ProductInfo/index/pageCount/20/page/'+page;
						}
					}

					function listChangePage(max,page) {
						if (!isInteger(page)){
							page = 1;
						}								
						if (page > max){
							page = max;
						}								
						if (page < 1){
							page = 1;
						}
						location.href='/Manage/index.php?s=/ProductInfo/index/pageCount/20/page/'+page;
					}

					function changePageCount(page,pageCount) {
						location.href='/Manage/index.php?s=/ProductInfo/index/page/1' + "/pageCount/" + pageCount;	
					}
					</script>					<!-- 分页 结束 -->
				</div>
			</div>
		</div>
	</div>
        <script type="text/javascript">
            var isUsUser = ""; 
        </script>


		</div>
	</div>
	</div>
	<!-- InstanceEndEditable -->
	<div class="area footer">
		Powered by Shangsi CORPORATION. All &copy; Rights Reserved.

	</div> 
</body>
<!-- InstanceEnd --></html>