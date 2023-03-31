<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>跳转提示</title>
	<style>
		body {
			background-color: #f2f2f2;
			font-family: Arial, sans-serif;
			text-align: center;
			padding-top: 100px;
		}
		h1 {
			font-size: 36px;
			color: #0066cc;
			margin-bottom: 30px;
		}
		p {
			font-size: 18px;
			color: #333;
			margin-bottom: 20px;
		}
		a {
			color: #0066cc;
			text-decoration: none;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<h1><?php echo($message); ?></h1>
	<p class="jump">
	页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
	</p>
	<script type="text/javascript">
	(function(){
	var wait = document.getElementById('wait'),href = document.getElementById('href').href;
	var interval = setInterval(function(){
		var time = --wait.innerHTML;
		if(time <= 0) {
			location.href = href;
			clearInterval(interval);
		};
	}, 1000);
	})();
</script>
</body>
</html>

