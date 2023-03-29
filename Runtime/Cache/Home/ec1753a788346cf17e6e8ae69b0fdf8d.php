<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>个人信息</title>
	<link href="/Application/Home/Static/css/boot.css" rel="stylesheet">
</head>
<body class="left">
  <div class="info_list">
    <p><span class="" data-rid="">组号：<?php echo ($userInfo["iGroupId"]); ?></span></p>
    <p><span class="" data-rid="">是否为组长：<?php echo ($userInfo["iIsHeadman"]); ?></span></p>
    <p><span class="" data-rid="">兴趣爱好：<?php echo ($userInfo["sLike"]); ?></span></p>
    <p><span class="" data-rid="">特长：<?php echo ($userInfo["sSkill"]); ?></span></p>
  </div>
</body>
</html>