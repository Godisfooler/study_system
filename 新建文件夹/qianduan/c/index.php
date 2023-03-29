<?php 
	include 'm/model.php';
	include 'c/head.php';
	include 'c/news.php';
	include 'c/chat.php';
	include 'c/liuyan.php';
	function footer(){
		$res=sel_m_id("contact");
		fetch(__FUNCTION__, $res);
	}
	function login(){
		fetch(__FUNCTION__, []);
	}
	
	function img(){
		$res=sel_m_id("img","img_path");
		fetch(__FUNCTION__, $res);
	}
	function job(){
		$res=sel_m_id("job","company,job,money,place,time",[],['limit'=>[0,6],'orderby'=>"time DESC"]);
		fetch(__FUNCTION__, $res);
	}
	function partner(){
		$res=sel_m_id("partner","id,img_path,span,url");
		fetch(__FUNCTION__, $res);
	}
	function partner2(){
		$res=sel_m_id("partner2","img_path,url");
		fetch(__FUNCTION__,$res);
	}
	function index(){
		fetch(__FUNCTION__,[]);
	}
	function apply(){
		$res=sel_m_id("job2","job_c");
		fetch(__FUNCTION__, $res);
		footer();
	}
	function person(){
		fetch(__FUNCTION__, []);
		footer();
	}
	function focus(){
		$res=sel_m_id("focus");	
		fetch(__FUNCTION__,$res);
	}
	function focus_son(){
		$res2=sel_m_id("focus","content",[["=",'id'=>$_POST['id']]]);
		echo $res2[0]['content'];
	}
	function join1(){
		fetch(__FUNCTION__, []);
		footer();
	}
	function ck_login(){
		if($_POST['username']=='zwc' and $_POST['pwd']=='1996'){
			fetch("success",['msg'=>"恭喜您，登录成功！","_URL_"=>"?c=index&a=index"]);
		}
		else{
			fetch("error",['msg'=>"对不起，登录失败","_URL_"=>"?c=index&a=login"]);
		}
	}
	function table(){
		$res=sel_m_id("testdb","username,pwd",
				[],
				['term'=>"or"]);
		fetch(__FUNCTION__, $res);
	}
?>
