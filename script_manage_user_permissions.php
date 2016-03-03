<?php
require('connect.php');
require('session_handler.php');

$sanitized_uid = (int) $_SESSION['uid'];
$sql="SELECT `user_permissions` FROM `user_permissions` WHERE `uid`='$sanitized_uid'";
$row=mysql_fetch_array(mysql_query($sql));
unset($sql);
if($row['user_permissions']==1){
	unset($row);
	//$sub_result = mysql_query("SELECT * FROM tables WHERE id='$sanitized_table_id'");
	//$sub_row = mysql_fetch_array($sub_result);
	$sanitized_user_id = mysql_real_escape_string(rawurldecode($_GET['user_id']));
	if(isset($_GET['table'])){
		$sanitized_table_permission = (int) $_GET['table'];
		$sql="UPDATE `user_permissions` SET `table_permissions`='$sanitized_table_permission' WHERE `uid`='$sanitized_user_id'";
		mysql_query($sql);
	}
	else if(isset($_GET['user'])){
		$sanitized_user_permission = (int) $_GET['user'];
		$sql="UPDATE `user_permissions` SET `user_permissions`='$sanitized_user_permission' WHERE `uid`='$sanitized_user_id'";
		mysql_query($sql);
	}
}
else{
	//user does not have permission to make this change
}
unset($decoded_table_name);
unset($table_name);
unset($sanitized_table_name);
unset($encoded_table_name);
unset($html_table_name);
?>