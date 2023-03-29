<?php 
	function lychoose(){
		$res=sel_m_id("liuyan");
	}
	function lydel(){
		if ($_POST['id']!=0){
			$res=del_m_id("liuyan",['id'=>$_POST['id']]);
			if($res){
				echo "删除成功!";
			}
		}
		else {
			echo "删除失败！";
		}
	}
	function lygl(){
		$res=sel_m_id("liuyan","id,text",[],['limit'=>[0,6],'orderby'=>"id DESC"]);
		$res2=sel_m_id("liuyan","id,text",[],['orderby'=>"id DESC"]);
		$maxnum=sel_m_id("liuyan",'count(*) as num');
		$maxpage=ceil($maxnum[0]['num']/6);
		fetch(__FUNCTION__, ['liuyan'=>$res,'ly'=>$res2,'maxpage'=>$maxpage]);
	}
	function lyfy(){
		$res=sel_m_id("liuyan","id,text",[],['limit'=>[($_POST['nowpage']-1)*6,6],'orderby'=>"id DESC"]);
		echo json_encode($res);
	}
?>