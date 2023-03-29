<?php 
	function chat(){
		fetch(__FUNCTION__,[]);
	}
	function send(){
	$res=insert_m_id("chat",['text'=>$_POST['msg'],
							'state'=>'0']);
	}
	function recive(){
		$res=sel_m_id("chat","*",[["=",'state'=>'0']]);
		foreach ($res as $r){
			up_m_id("chat", ['state'=>'1'],['id'=>$r['id']]);
		}
		echo json_encode($res);
	}
?>