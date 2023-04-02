<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
	<head>
		<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="/Application/Home/Static/css/main.css" />
    <style>
        .top_right{
            float: right;
            padding: 10px;
            position: relative;
            top:-130px;
            right:20px;
            z-index: 1001;
        }
    </style>
</head>
<body class="homepage is-preload">
    <div id="page-wrapper">
        <!-- Nav -->
            <nav id="nav">
                <ul>
                    <li data-active="index"><a href="index.html">首页</a></li>
                    <li data-active="questionList"><a href="<?php echo U('Home/Index/questionList');?>">问题讨论</a></li>
                    <li data-active="appraiseList"><a href="<?php echo U('Home/Index/appraiseList');?>">学习评价</a></li>
                    <li data-active="personInfo"><a href="<?php echo U('Home/Index/personInfo');?>">个人中心</a></li>
                 
                </ul>
                <div class="top_right">
                    <?php if($userInfo["id"] > 0): ?><p><?php echo ($userInfo["username"]); ?>/<a href="<?php echo U("Home/Member/logout");?>">退出</a></p>
                    <?php else: ?>
                        <span><a href="#">登录</a>/<a href="#">注册</a><?php echo ($userInfo["username"]); ?></span><?php endif; ?>
                </div>
            </nav>
    </div>

    <script src="/Application/Home/Static/js/jquery.min.js"></script>
    <script src="/Application/Home/Static/js/jquery.dropotron.min.js"></script>
    <script src="/Application/Home/Static/js/jquery.scrolly.min.js"></script>
    <script src="/Application/Home/Static/js/browser.min.js"></script>
    <script src="/Application/Home/Static/js/breakpoints.min.js"></script>
    <script src="/Application/Home/Static/js/util.js"></script>
    <script src="/Application/Home/Static/js/main.js"></script>
</body>
<script>
    var pageType = "<?php echo ($pageType); ?>";
    var navItems = document.querySelectorAll('nav li');
    // 遍历所有选项
    navItems.forEach(function(item) {
    // 如果选项的 data-active 值与当前页面标识相同，则设置为选中状态
    if (item.dataset.active === pageType) {
        item.classList.add('current');
    } else {
        item.classList.remove('current');
    }
    });
</script>
</html>
		<title>学习系统</title>
	</head>
	
	<body class="homepage is-preload">
	</body>
</html>