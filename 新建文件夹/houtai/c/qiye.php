<?php 
	function qiyegl(){
		$res=sel_m_id("coop");
		fetch(__FUNCTION__,$res);
	}
	function qiyebody(){
		$res=sel_m_id("coop","body",[["=",'id'=>$_POST['id']]]);
		echo json_encode($res);
	}
	function qyupdate(){
		$res=up_m_id("coop",['body'=>$_POST['body']],
				['id'=>$_POST['id']]);
		if($res){
		echo "更改成功！";
		}
	}
	function qydel(){
		$res=del_m_id("coop",['id'=>$_POST['id']]);
		if($res){
			echo "删除成功";
		}
		
	}
?>