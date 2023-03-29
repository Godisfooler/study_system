<?php 
	function connectDb($sql){
		include 'dbconfig.php';
		$con=mysql_connect($conf['host'],$conf['username'],$conf['pwd']);
		mysql_select_db($conf['database'],$con);
		$res=mysql_query($sql,$con);
		while ($row=mysql_fetch_assoc($res)){
			 $rows[]=$row;
		}
		return $rows;
	}
?>