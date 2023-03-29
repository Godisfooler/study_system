<?php 
	header("Content-type:text/html;charset=utf-8");
	$res=@include 'c/'.$_GET['c'].'.php';
	if(!$res){
		fetch("error", ['msg'=>"抱歉，您访问的页面不存在"]);
		die();
	}
	if(function_exists($_GET['a'])){
		$_GET['a']();
	}
	else {
		fetch("error", ['msg'=>"抱歉，您访问的页面不存在"]);
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