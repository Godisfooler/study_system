<?php 
 	date_default_timezone_set('Asia/Shanghai');
	function job_i(){
		if (!empty($_POST['company'])){
		$res=insert_m_id("job",['company'=>$_POST['company'],
				'job'=>$_POST['job'],
				'money'=>$_POST['money'],				
				'place'=>$_POST['place'],
				'time'=>date("Y-m-d")]);
		if($res){
			echo "添加成功";
		}
		else{
			echo "添加失败";
		}
		}
		else {
			echo "添加失败";
		}
	}
	function jobgl(){
		fetch(__FUNCTION__,[]);
	}
	function jlck(){
		$res=sel_m_id("jianli","姓名,性别,出生年份,地区,手机,学历,婚否,目前单位,目前职务,行业,参加工作年份,自我评价,添加时间",[],['orderby'=>"id"]);
		fetch(__FUNCTION__, $res);
	}
	function jlgl(){
		$res=sel_m_id("jianli","*",[[">",'id'=>0]]);
		fetch(__FUNCTION__, $res);
	}
	function jlchoose(){
		$res=sel_m_id("jianli");
	}
	function jldel(){
	if ($_POST['id']!=0){
	$res=del_m_id("jianli",['id'=>$_POST['id']]);
		if($res){
			echo "删除成功!";
		}
	}
	else {
		echo "请选择要删除的简历！";
	}
	}
?>