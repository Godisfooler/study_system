<?php if (!defined('THINK_PATH')) exit();?><html>
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
		<title>问题讨论</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/Application/Home/Static/css/main.css" />

	<style>
.reply-container {
  margin-top: 20px;
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.reply-container label {
  display: block;
  margin-bottom: 10px;
  font-weight: bold;
}

.reply-container textarea {
  width: 100%;
  height: 100px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  resize: none;
  font-size: 16px;
  line-height: 1.5;
}

.reply-container input[type="file"] {
  margin-top: 10px;
}

.reply-container button {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #b1ddab;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}
.comment button{
  padding: 10px 20px;
  background-color: #b1ddab;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}
.dianping{
	float: right;
	position: relative;
	top: -30px;
}
.dianping button:hover {
  background-color: #4CAF50;
}
.reply-container button:hover {
  background-color: #4CAF50;
}
.reply_container {
	max-width: 1200px;
	margin: 0 auto;
	padding: 20px;
	background-color: #fff;
	border: 1px solid #ddd;
}
h1 {
	font-size: 24px;
	margin-top: 0;
	margin-bottom: 20px;
	text-align: center;
}
.comment {
	margin-bottom: 20px;
	padding: 10px;
	background-color: #f2f2f2;
	border: 1px solid #ddd;
}
.comment .meta {
	font-size: 12px;
	line-height: 1;
	color: #999;
}
.comment .content {
	font-size: 16px;
	line-height: 1;
	margin-bottom: 10px;
}
.back_href{
	text-decoration: underline;
}
.back_href:hover{
	text-decoration: underline;
}
.setpadding{
	padding: 20px;
}
header p{
	color: #333;
}
#dianping_form{
	padding-top: 40px;
	padding-left: 10px;
}
#dianping_form input{
	margin-bottom: 10px;
	width: 80%;
}
#dianping_form button:hover {
  background-color: #4CAF50;
}
#dianping_list{
	margin-top: 60px;
}
#list_detail{
	max-width: 1200px;
	margin: 10px 10px 10px 10px;
	padding: 10px;
	background-color: #fff;
	border: 1px solid #ddd;
}
#dianping_list p{
	margin-bottom:0;
	border-bottom:  1px solid #ddd;
}
.downSpan:hover{
	cursor:pointer;
}
</style>	
</head>
<body class="is-preload">
		<div id="page-wrapper">
			<!-- Main -->
				<section id="main">
					<div class="container">
						<div class="row">
							<div class="col-12-medium setpadding">
								<h3><a class="back_href" href="<?php echo U('Home/Index/questionList');?>">>返回列表</a></h3>
								<div class="content">
										<article class="box page-content">
											<header>
												<h2>问题详情</h2>
												<p calss="title"><?php echo ($questionDetail["sTitle"]); ?></p>
												<ul class="meta">
													<li class="icon fa-clock">发布时间：<?php echo ($questionDetail["date"]); ?></li>
													<li class="icon fa-comments">回答数：<?php echo ($questionDetail["count"]); ?></li>
												</ul>
											<section>
												<p>
												<?php echo ($questionDetail["sContent"]); ?>
												</p>
											</section>
											</header>
											<section>
												<h3>回复列表：</h3>

												<div class="reply_container">
													<?php if(is_array($answerList)): $i = 0; $__LIST__ = $answerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$answer): $mod = ($i % 2 );++$i;?><div class="comment">
															<div class="meta">
																<p>评论者：<?php echo ($answer["username"]); ?>  &nbsp;&nbsp;&nbsp;&nbsp;发表于：<?php echo ($answer["date"]); ?></p>
															</div>
															<div class="content">
																<p><?php echo ($answer["sContent"]); ?></p>
															</div>
															<?php if($answer["sOriName"] != ''): ?><span class="downSpan" style="float:left;position: relative;top: -30px;text-decoration: underline;">附件：<a href="<?php echo U('Home/Index/downloadById');?>?fileId=<?php echo ($answer["iFileId"]); ?>"><?php echo ($answer["sOriName"]); ?></a></span><?php endif; ?>	
															<?php if($userInfo["iType"] > 0 || $userInfo["iIsHeadman"] == 1): ?><div class="dianping"><button onclick="showList(this)">查看点评</button> <button class="dianping_btn" onclick="showForm(this)">点评</button></div>
																<div id="dianping_list" style="display:none;">
																	<?php if(is_array($answer["appriseList"])): $i = 0; $__LIST__ = $answer["appriseList"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$appriselist): $mod = ($i % 2 );++$i;?><div id="list_detail">
																			<div class="meta">
																				<p>点评人：<?php echo ($appriselist["username"]); ?>  &nbsp;&nbsp;&nbsp;&nbsp;发表于：<?php echo ($appriselist["date"]); ?></p>
																			</div>
																			<label for="pros">优点：</label>
																			<p><?php echo ($appriselist["sMerit"]); ?></p>
																			<label for="cons">缺点：</label>
																			<p><?php echo ($appriselist["sShortComing"]); ?></p>
																			<label for="suggestions">指导建议：</label>
																			<p><?php echo ($appriselist["sSuggestions"]); ?></p>
																		</div><?php endforeach; endif; else: echo "" ;endif; ?>
																</div>
																<div id="dianping_form" style="display:none;">
																	<label for="pros">优点：</label><br>
																	<input type="text" id="pros" name="pros" value=""><br>
																	<label for="cons">缺点：</label><br>
																	<input type="text" id="cons" name="cons" value=""><br>
																	<label for="suggestions">指导建议：</label><br>
																	<input type="text" id="suggestions" name="suggestions" value=""><br>
																	<button onclick="submitForm(this)" answerId="<?php echo ($answer["id"]); ?>">提交</button>
																</div>
																<div id="dianping_msg"></div><?php endif; ?>
														</div><?php endforeach; endif; else: echo "" ;endif; ?>
												</div>
											</section>
										</article>
										<div class="reply-container">
											  <label for="reply">回复：</label>
											  <textarea id="reply" name="reply"></textarea>
											  <br>
											  <label for="file">上传文件：</label>
											  <input type="file" id="file" name="file" onchange="getFileSize(this)">
											  <br>
											  <button onclick="submitReply(this)">提交</button><span class="reply_text"> </span>
										  </div>
										  
								</div>
							</div>
						</div>
					</div>
				</section>

			<!-- Footer -->

		</div>

		<script>
			function showList(obj){
				$(obj).parent().parent().find("#dianping_list").show();
				$(obj).attr('onclick','hideList(this)');
			}
			function hideList(obj) {
			  	$(obj).parent().parent().find("#dianping_list").hide();
				$(obj).attr('onclick','showList(this)');
			}

			function showForm(obj) {
			  	$(obj).parent().parent().find("#dianping_form").show();
				$(obj).attr('onclick','hideForm(this)');
			}

			function hideForm(obj) {
			  	$(obj).parent().parent().find("#dianping_form").hide();
				$(obj).attr('onclick','showForm(this)');
			}

	  
			function submitForm(obj) {
			  var answerId = $(obj).attr('answerId');
			  var pros = $(obj).parent().find("#pros").val();
			  var cons = $(obj).parent().find("#cons").val();
			  var suggestions = $(obj).parent().find("#suggestions").val();
			  if(pros == '' && cons =='' && suggestions==''){
			  }else{
				$.ajax({
					url: '/Home/Index/answerAppraise', // 后端处理数据的URL
					type: 'POST',
					data: {'answerId':answerId,'pros':pros,'cons':cons,'suggestions':suggestions},
					success: function(response) {
						// 处理成功响应
						if(response.status == 1){
							var tempHtml = '<div id="list_detail">'+
												'<div class="meta">'+
													'<p>点评人：'+response.data.username+'  &nbsp;&nbsp;&nbsp;&nbsp;发表于：'+response.data.date+'</p>'+
												'</div>'+
												'<label for="pros">优点：</label>'+
												'<p>'+pros+'</p>'+
												'<label for="cons">缺点：</label>'+
												'<p>'+cons+'</p>'+
												'<label for="suggestions">指导建议：</label>'+
												'<p>'+suggestions+'</p>'+
											'</div>';
							$(obj).parent().parent().find('#dianping_list').append(tempHtml);
							$(obj).parent().find("#pros").val('');
							$(obj).parent().find("#cons").val('');
						    $(obj).parent().find("#suggestions").val('');
							$(obj).parent().parent().find(".dianping .dianping_btn").attr('onclick','showForm(this)');
							$(obj).parent().parent().find("#dianping_form").hide();
						}else{
							$('#dianping_msg').html(response.msg);
						}
					},
					error: function(response) {
						// 处理错误响应
					}
				});
			  }
			}

		var upfiles = {};
		function getFileSize(fileObj){
			upfiles = fileObj.files;
			console.log(upfiles);
    	}
		function submitReply(){
			var questionId = "<?php echo ($_GET['questionId']); ?>";
			var reply_text = $("#reply").val();
			var formData = new FormData();
			formData.append('file',upfiles[0]);
			formData.append('questionId',questionId);
			formData.append('reply_text',reply_text);
			$.ajax({
				url: '/Home/Index/saveReply', // 后端处理数据的URL
				type: 'POST',
				data:formData,
				dataType:'JSON',
				processData:false,
				contentType:false,
				success: function(response) {
					if(response.status == 1){
						$('.reply_text').html(' '+response.msg);
						window.location.reload();
					}else{
						var tempHtml = '<span style="color:red">'+response.msg+'</span>';
						$('.reply_text').html(tempHtml);
					}
				},
				error: function(response) {
					// 处理错误响应
				}
			});
		}
</script>
	</body>
</html>