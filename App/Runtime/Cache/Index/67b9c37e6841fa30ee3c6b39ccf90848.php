<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>添加角色</title>	

</head>
<body>
	<form action="" method="post">
		<table class="table">
			<tr>
				<th colspan="2">添加角色</th>		
			</tr>
			<tr>
				<td align="right">角色名称</td>
				<td><input type="text" name='name' /></td>
			</tr>
			<tr>
				<td align="right">角色描述</td>
				<td><input type="text" name='reamrk' /></td>
			</tr>
			<tr>
				<td align="right">是否开启</td>
				<td><input type="radio" name='status' value='1' checked='checked' /> &nbsp; 开启 &nbsp;</td>
				<td><input type="radio" name='status' value='0' /> &nbsp; 关闭 &nbsp;</td>
			</tr>
			<tr>
				<td colspan='2' align="center">
					<input type="submit" value='添加' />
				</td>
			</tr>
		</table>
	</form>
</body>
</html>