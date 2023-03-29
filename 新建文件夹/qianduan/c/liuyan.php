<?php 
	function liuyan(){
		$res=sel_m_id("liuyan","id,text",[],['limit'=>[0,6],'orderby'=>"id DESC"]);
		$maxnum=sel_m_id("liuyan",'count(*) as num');
		$maxpage=ceil($maxnum[0]['num']/6);
		fetch(__FUNCTION__, ['liuyan'=>$res,'maxpage'=>$maxpage]);
	}
	function lytj(){
		$res=insert_m_id("liuyan",['text'=>$_POST['text'],
				'time'=>date("ymd")
		]);
		$res2=sel_m_id("liuyan");
		echo json_encode($res2);
	}
	function lyfy(){
		$res=sel_m_id("liuyan","id,text",[],['limit'=>[($_POST['nowpage']-1)*6,6],'orderby'=>"id DESC"]);
		echo json_encode($res);
	}
?>