<?php
	include 'm/model.php';
	include 'c/news.php';
	include 'c/job.php';
	include 'c/qiye.php';
	include 'c/focus.php';
	include 'c/about.php';
	include 'c/liuyan.php';
	function login(){
		fetch(__FUNCTION__, []);
	}
	function ck_login(){
		$res[0]['id'] = 1;
		//$res=sel_m_id("testdb","*",[["=",'username'=>$_POST['username']],["=",'pwd'=>$_POST['pwd']]]);
		@$_SESSION['user']=$res[0]['id'];
		if (!empty($_SESSION['user'])){
		fetch("success",['msg'=>"恭喜您，登录成功！","_URL_"=>"?c=index&a=index"]);
		}
		else {
			fetch("error",['msg'=>"请输入正确的账号或密码！","_URL_"=>"javascript:history.back()"]);
		}	
	}
	function reg(){
		fetch(__FUNCTION__,[]);
	}
	function zhuce(){
		if (!empty($_POST['username']) and !empty($_POST['pwd'])){
		$res=insert_m_id("testdb",['username'=>$_POST['username'],'pwd'=>$_POST['pwd']]);
		if ($res){
			fetch("success", ['msg'=>"恭喜你，注册成功！,即将进入登录页面！",'_URL_'=>'?c=index&a=login']);
		}
		else{
			fetch("error", ['msg'=>"对不起，注册失败！",'_URL_'=>'javascript:history.back()']);
		}
		}
		else {
			fetch("error", ['msg'=>"请输入账号或密码！",'_URL_'=>'javascript:history.back()']);
		}
	}
	function table(){
		$res=sel_m_id("testdb","username,pwd",
				[],
				['term'=>"or"]);
		fetch(__FUNCTION__, $res);
	}
	function index(){
		fetch(__FUNCTION__, []);
	}
	function conf(){
		$res=sel_m_id("contact");
		fetch(__FUNCTION__,$res);
	}
	function getKey(){
		$str['key'] = date("Ymd").rand(1000,5000);
		echo json_encode($str);
	}
	
	function saveKey(){		
		$result=sel_m_id("lottery_key",
				['key_str'=>$_POST['key']]);
		if(!$result){
			$res=insert_m_id("lottery_key",['key_str'=>$_POST['key'],
				'createTime'=>time(),
				'status' => 1
			]);
		}
		if($res){
			echo "<script>alert('添加成功！')</script>";
		}else{
			echo "<script>alert('该卡密已存在，请重新输入！');history.go(-1);</script>";
		}
	}
	
	function tijiao(){
		move_uploaded_file($_FILES['wx']['tmp_name'],"D:/phpStudy/WWW/web_test/qianduan/v/wximg/".date("his").".png");
		move_uploaded_file($_FILES['wx2']['tmp_name'],"D:/phpStudy/WWW/web_test/qianduan/v/wximg/".date("Y-m-d").".png");
		$img="v/wximg/".date("his").".png";
		$img2="v/wximg/".date("Y-m-d").".png";
		if (!empty($_FILES['wx']['tmp_name'])){
		$res=up_m_id("contact",array('tel'=>$_POST['tel'],
				'tel2'=>$_POST['tel2'],
				'QQ'=>$_POST['qq'],
				'cz'=>$_POST['cz'],				
				'address'=>$_POST['address'],
				'email'=>$_POST['email'],
				'wx'=>$img,
				'wx2'=>$img2,
		),['id'=>'1']);
		if($res){
			echo"修改成功";
		}
		else{
			echo "修改不成功";
		}
		}
		else{
			echo "修改失败";
		}
	}
	function home(){
		fetch(__FUNCTION__, []);
	}
	function tijiao2(){
		$res=insert_m_id("news",['title'=>$_POST['title'],
				'content'=>$_POST['content']
		]);
		if($res){
		echo "添加成功";
		}
		else {
			"添加失败";
		}
	}
?>