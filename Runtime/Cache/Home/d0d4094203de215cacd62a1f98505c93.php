<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>登录</title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="col-md-4 col-md-offset-4 center">
<form action="?c=index&a=ck_login" method="post" enctype="multipart/form-data">
  <div class="form-group text-center">
    <label for="exampleInputEmail1"><h2>登录</h2></label>
    <input type="username" name="username" class="form-control username"  placeholder="请输入账号">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1"></label>
    <input type="password" name="pwd" class="form-control" placeholder="请输入密码">
  </div>
  <button type="submit" onclick="dianji()" style="width: 100%" class="btn btn-success">立即登录</button>
</form>
</body>
</html>