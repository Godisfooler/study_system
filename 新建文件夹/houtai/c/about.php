<?php 
	function aboutgl(){
		$res=sel_m_id("aboutli");
		fetch(__FUNCTION__,$res);
	}
	function aboutbd(){
		$res=sel_m_id("aboutli","body",[["=",'id'=>$_POST['id']]]);
		echo json_encode($res);
	}
	function aboutup(){
		$res=up_m_id("aboutli",['body'=>$_POST['body']],
				['id'=>$_POST['id']]);
		if($res){
			echo "更改成功！";
		}
		else {
			echo "更改失败";
		}
	}
	function aboutdel(){
		$res=del_m_id("aboutli",['id'=>$_POST['id']]);
		if($res){
			echo "删除成功";
		}
		else {
			echo "删除失败";
		}
	}
?>