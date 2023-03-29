<?php 
	function focusgl(){
		$res=sel_m_id("focus");
		fetch(__FUNCTION__,$res);
	}
	function focusbd(){
		$res=sel_m_id("focus","content",[["=",'id'=>$_POST['id']]]);
		echo json_encode($res);
	}
	function focusup(){
		$res=up_m_id("focus",['content'=>$_POST['content']],
				['id'=>$_POST['id']]);
		if($res){
		echo "更改成功!";
		}
	}
	function focusdel(){
		$res=del_m_id("focus",['id'=>$_POST['id']]);
		if($res){
			echo "删除成功!";
		}
		
	}
?>