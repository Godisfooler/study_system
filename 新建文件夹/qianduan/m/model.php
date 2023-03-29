<?php 
/**
 * 标示查询表中所有数据
 * @param 表示表名 以数组方式传入
 * @author lhl
 *
 */


function sel_m_table($table){
	return mysql_db("select * from $table");
}
/**
 * 标示通过条件查询数据
 * @param 表示表名 以数组方式传入
 * @param 表示判断条件 数组名为数据库字段名称 如果参数为空 则表示查询所有
 * @author lhl
 *
 */
function insert_m_id($table,$data){
	if(empty($table)){return ;}
	if(empty($data)){return ;}
	$q=1;
	$choice='';
	$value='';
	foreach ($data as $key=>$d){
		if($q==count($data)){$code='';}else{$code=' , ';}
		$choice.=$key.$code;
		$value.="'".$d."'".$code;
		$q++;
	}
	$choices= "(".$choice.")";
	$values= "(".$value.")";
	$sql="insert into $table $choices values $values";
	return mysql_db_cz($sql);
}
function del_m_id($table,$data){
	$action='';
	$q=1;
	foreach ($data as $key=>$a){
		if($q==count($data)){$code='';}else{$code=' and ';}
		$action.=$key."='".$a."'$code";
		$q++;
	}
	 $sql="delete from $table where $action";
	return mysql_db_cz($sql);
	
}
function up_m_id($table,$data,$action){
	$choice='';
	$actions='';
	$i=1;
	$q=1;
	foreach ($data as $key=>$d){
		if($i==count($data)){$code='';}else{$code=',';}
		$choice.=$key."='".$d."'$code";
		$i++;
	}
	foreach ($action as $key=>$a){
		if($q==count($action)){$code='';}else{$code=' and ';}
		$actions.=$key."='".$a."'$code";
		$q++;
	}
	 $sql="update $table set $choice where $actions";
	return  mysql_db_cz($sql);
}
/**
 *		 "user","username,pwd",
			[["=",'username'=>"1"],["like",'pwd'=>"1"]],
			["term"=>"or",
			"limit"=>[0,5],
			"orderby"=>"DESC",
			"groupby"=>"username",
			"join"=>["joinkey"=>"right","table"=>"test2","on"=>["t1"=>"id","t2"=>"id2"]]		
			]
 * @param string $table 表名 user
 * @param string $ds 需要查询字段 username,pwd
 * @param array $data 查询条件 [["=",'username'=>"1"],["like",'pwd'=>"1"]]
 * @param array $action 需要的操作 ["term"=>"or",
			"limit"=>[0,5],
			"orderby"=>"DESC",
			"groupby"=>"username",
			"join"=>["joinkey"=>"right","table"=>"test2","on"=>["t1"=>"id","t2"=>"id2"]]		
			]
 * @return Ambigous <string, multitype:unknown >
 */
function sel_m_id($table,$ds=null,$data=null,$action=null){//查询方法
	$co='';
	if(empty($action['term'])){$action['term']='and';}
	if(empty($ds)){$ds='*';}
	$pd='';
	$i=1;
	foreach ((array)$data as $d){
		$eq=$d['0'];
		if($eq =='like'){
			$co='%';
		}
		foreach ($d as $key=>$d1){		
			if($key=='0'){continue;}
			if($i>=count($data)){
				$code="";
			}else{ $code=$action['term']; }
				 $pd.=" ".$key." $eq '$co".$d1."$co' ".$code;
		}
		$i++;
	}
	
		if(!empty($action['groupby'])){
			$groupby="group by ".$action['groupby'];
		}
		if(!empty($action['limit'])){
			$limit=" limit ".$action['limit'][0].",".$action['limit'][1];
		}
		if(!empty($action['orderby'])){
			$orderby="order by ".$action['orderby'];
		}
		if(!empty($action['join'])){
				$join=$action['join']['joinkey']." join ".$action['join']['table']
				." on "."$table.".$action['join']['on']['t1']."=".$action['join']['table'].".".$action['join']['on']['t2'];
			}
		if(!empty($data)){$where="where";}
	  @$sql="select $ds from $table $join  $where $pd ".$groupby." ".$orderby.$limit;
	return $res=mysql_db($sql);
}
/**
 * 标示通过条件查询数据
 * @param 表示表名 第一个参数标示跟新的内容 第二个标示条件 都以数组方式传入 
 * @param 第三个参数标示加入条件 如or group by等
 * @param 表示判断条件 数组名为数据库字段名称 如果参数为空 则表示查询所有
 * @return 返回影响行数
 * @author lhl
 *
 */

function db_query($sql){
	$DB_Conf=DB_C();
	$conn=mysql_connect($DB_Conf['HOST'].":".$DB_Conf['Port'],$DB_Conf['DB_Name'],$DB_Conf['DB_Pwd']);
	mysql_select_db($DB_Conf['DB_Database'],$conn);
	$res=mysql_query($sql,$conn);
	if($res){
	return $re=mysql_affected_rows($conn);
	}else{
	return '';
	}
}
function mysql_db_cz($sql){
	$DB_Conf=DB_C();
	$conn=mysql_connect($DB_Conf['HOST'].":".$DB_Conf['Port'],$DB_Conf['DB_Name'],$DB_Conf['DB_Pwd']);
	mysql_select_db($DB_Conf['DB_Database'],$conn);
	mysql_query("set names 'utf8'");//操作  写入 修改编码为utf8
	$res=mysql_query($sql,$conn);
	return $re=mysql_affected_rows($conn);//表示mysql当前链接操作影响函数查询
}
function mysql_db($sql){
	$rows=array();
	$es=array();
	$DB_Conf=DB_C();
	$conn=mysql_connect($DB_Conf['HOST'].":".$DB_Conf['Port'],$DB_Conf['DB_Name'],$DB_Conf['DB_Pwd']);
	mysql_select_db($DB_Conf['DB_Database'],$conn);
	mysql_query("set character set 'utf8'");//读数据库 设置编码为UTF8格式
	$res=mysql_query($sql,$conn);
	while (@$row=mysql_fetch_array($res)){
		@$rows[]=$row;
	}
	$i=0;
	if(!$rows){
		return "";
	}
	foreach ($rows as $r){//三次
		foreach ($r as $key=>$r1){
			if(!is_numeric($key)){
				$e[$key]=$r1;
			}
		}
		$es[]=$e;
	};
	return $es;

}

?>