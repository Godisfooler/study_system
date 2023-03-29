<?php 
	session_start();
	header("Content-type:text/html;charset=utf-8");
	$res=@include 'c/'.$_GET['c'].'.php';
	if(!$res){
		fetch("error", ['msg'=>"抱歉，您访问的页面不存在"]);
		die();
	}
	if(function_exists($_GET['a'])){
		if (!empty($_SESSION['user']) or $_GET['a']=='login' or $_GET['a']=='ck_login' or $_GET['a']=='reg' or $_GET['a']=='zhuce'){
		$_GET['a']();
		}
		else{
			fetch("error", ['msg'=>"请登录！","_URL_"=>"?c=index&a=login"]);
		}
	}
	else {
		fetch("error", ['msg'=>"抱歉，您访问的页面不存在","_URl_"=>"javascript:history.back()"]);
		die();
	}
	 function fetch($funcname,$data){
	 	include 'm/webconfig.php';
		include "v/$funcname.html";
	}
	function DB_C(){
		include 'm/dbconfig.php';
		return ($conf);
	}
?>