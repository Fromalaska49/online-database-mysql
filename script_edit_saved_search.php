<?php
require('session_handler.php');
require('connect.php');
$sql_query;
if(isset($_GET['change'])&&isset($_GET['id'])){
	$sanitized_id=(int) $_GET['id'];
	$result=mysql_query("SELECT `authuid` FROM `saved_searches` WHERE `id`='$sanitized_id'");
	$row=mysql_fetch_array($result);
	if($row['authuid']==$_SESSION['uid']){
		//user is authorized to modify this search
		if($_GET['change']==2&&isset($_GET['text'])){
			//rename save
			$sanitized_name=mysql_real_escape_string($_GET['text']);
			if($_GET['text']==''){
				$sanitized_name=mysql_real_escape_string('Untitled Search');
			}
			mysql_query("UPDATE `saved_searches` SET `name`='$sanitized_name' WHERE `id`='$sanitized_id'");
		}
		else if($_GET['change']==1){
			//delete save
			mysql_query("DELETE FROM `saved_searches` WHERE `id`='$sanitized_id'");
			mysql_query("DELETE FROM `saved_search_terms` WHERE `search_id`='$sanitized_id'");
		}
		else{
			//invalid change
		}
	}
	else{
		//user is not authorized to modify this save
	}
}
?>