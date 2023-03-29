<?php 
	date_default_timezone_set('Asia/Shanghai');
	define("DEFNUM", 6);
	function news(){
		$res=sel_m_id("news","id,title",[],['limit'=>['0',DEFNUM],'orderby'=>"id DESC"]);
		$maxnum=sel_m_id("news",'count(*) as num');
		$maxpage=ceil($maxnum[0]['num']/DEFNUM);	
		fetch(__FUNCTION__, ['news'=>$res,'maxpage'=>$maxpage]);
	}
// 	function news2(){
// 		$res=sel_m_id("news","id,title",[],['limit'=>['0',DEFNUM]]);
// 		fetch(__FUNCTION__, $res);
// 	}
	function fy(){
		$res=sel_m_id("news","id,title",[],['limit'=>[($_POST['nowpage']-1)*6,DEFNUM],'orderby'=>"id DESC"]);
		echo json_encode($res);
		}
	function news_c(){
			$res=sel_m_id("news","content",[["=",'id'=>$_GET['id']]]);
			fetch(__FUNCTION__, $res);
		}
	function jl_up(){
		$mode="/^[\x80-\xff]{6,30}$/";
		$str=$_POST['name'];
// 		$mode2="/^13[0-9]{1}[0-9]{8}$|15[01689]{1}[0-9]{8}$|189[0-9]{8}$/";
// 		$str2=$_POST['tel'];
		if(preg_match($mode,$str)){
		$res=insert_m_id("jianli",
				['姓名'=>$_POST['name'],
				'性别'=>$_POST['sex'],
				'出生年份'=>$_POST['birth'],				
				'地区'=>$_POST['address'],
				'手机'=>$_POST['tel'],
				'学历'=>$_POST['edu'],
				'婚否'=>$_POST['marry'],
				'目前单位'=>$_POST['company'],
				'目前职务'=>$_POST['job'],
				'行业'=>$_POST['hangye'],
				'参加工作年份'=>$_POST['year'],
				'自我评价'=>$_POST['pingjia'],
				'添加时间'=>date("Y-m-d")]);
		if ($res){
			fetch("success", ['msg'=>"恭喜你，提交成功"]);
		}
		}
	else {
		fetch("error", ['msg'=>"对不起，请输入正确的姓名"]);
	}
	}
?>