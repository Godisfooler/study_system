<?php 
	function head(){
		$res=sel_m_id("company");
		$res2=sel_m_id("contact");
		fetch(__FUNCTION__, ["li"=>$res,"lianxi"=>$res2]);
	}
	function contact(){
		$res=sel_m_id("contact");
		fetch(__FUNCTION__, $res);
	}
	function about(){
		$res=sel_m_id("aboutli");
		fetch(__FUNCTION__, $res);
		footer();
	}
	function about_son(){
		$id=$_POST['id'];
		$res=sel_m_id("aboutli","body",[["=",'id'=>$id]]);
		echo $res[0]['body'];
	}
	function cooperation(){
		$res=sel_m_id("coop");
		fetch(__FUNCTION__,$res);
	}
	function coop_son(){
		$id=$_POST['id'];
		$res=sel_m_id("coop","body",[["=",'id'=>$id]]);
		echo $res[0]['body'];
	}
	function coop_son2(){
		$id=$_POST['id'];
		$res=sel_m_id("coop","body",[["=",'id'=>$id]]);
		echo $res[0]['body'];
		fetch("cooperation",$res);
	}
?>