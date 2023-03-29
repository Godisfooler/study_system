<?php 
function news(){
	$res=sel_m_id("news");
	fetch(__FUNCTION__,$res);
	}
function newsgl(){
	fetch(__FUNCTION__,[]);
}
function newsbt(){
	$res=sel_m_id($_POST['name'],"id,title");
	echo json_encode($res);
}
function newsct(){
	$res=sel_m_id("news","content",[["=",'id'=>$_POST['id']]]);
	echo json_encode($res);
}
function update(){
	$res=up_m_id("news",array('title'=>$_POST['title'],	
				'content'=>$_POST['content']),
				['id'=>$_POST['id']]);
	if($res){
		echo "更新成功！";
	}
	else {
		echo "更新失败！";
	}
}
function del(){
	$res=del_m_id("news",['id'=>$_POST['id']]);
	if ($res){
		echo "删除成功!";
	}
	else {
		echo "删除失败!";
	}
}
?>