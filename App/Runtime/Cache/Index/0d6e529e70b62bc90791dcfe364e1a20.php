<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>登录系统</title>
<style type="text/css">
<!--
.style1 {color: #00000}
.wenbenkuang {
  font-family: "宋体";
  font-size: 9pt;
  color: #333333;
  border: 1px solid #999999;
}
-->
</style>
<script type="text/javascript">
  var verifyURL = 'index.php/Index/Index/verify';
</script>
<script type="text/javascript">
function Myenter(str){
  if (event.keyCode == 13){  
    str.focus();}
}
function change_code(obj){
  var newCode = document.getElementById('code');
  newCode.src = "index.php/Index/Index/verify/" + Math.random();
}
</script>
</head>
<body style="font-size:12px">
<table width="532" border="0" align="center" cellpadding="0" cellspacing="0">
        <form action="index.php/Index/Index/login" method="post" name="form1">
          <tr>
            <td height="27" colspan="2" align="center" class="font_white"><span class="style1">用户登陆</span></td>
          </tr>
          <tr>
            <td width="172" height="22" align="right">用户名:&nbsp;</td>
            <td width="328" height="22"><input name="username" type="text" class="wenbenkuang" id="username" maxlength="50" onKeyPress="Myenter(form1.password)" /></td>
          </tr>
          <tr>
            <td width="172" height="22" align="right">密码:&nbsp;</td>
            <td width="328" height="22"><input name="password" type="password" class="wenbenkuang" id="password" maxlength="50" onKeyPress="Myenter(form1.code)" /></td>
          </tr>
          <tr>
            <td width="172" height="22" align="right">验证码:&nbsp;</td>
            <td width="50" height="22"><input name="code" type="code" class="wenbenkuang" onKeyPress="Myenter(form1.add)"/>&nbsp;<img src="index.php/Index/Index/verify" id="code">&nbsp;<a href="javascript:void(change_code(this));">看不清</a></td>
          </tr>
          <tr><td>&nbsp;</td></tr>
          <tr>
            <td height="22" colspan="2" align="center"><input name="add" type="button" class="button" id="add" value="提 交" onClick="form1.submit();" /></td>
          </tr>
        </form>
    </table>
</body>
</html>