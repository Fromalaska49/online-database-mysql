<?php
require('connect.php');
require('session_handler.php');

$sanitized_uid = (int) $_SESSION['uid'];
$sql="SELECT `table_permissions` FROM `user_permissions` WHERE `uid`='$sanitized_uid'";
$row=mysql_fetch_array(mysql_query($sql));
unset($sql);
if($row['table_permissions']==1){
	unset($row);
	//$sub_result = mysql_query("SELECT * FROM tables WHERE id='$sanitized_table_id'");
	//$sub_row = mysql_fetch_array($sub_result);
	$table_name = rawurldecode($_GET['table_name']);
	$sanitized_table_name = mysql_real_escape_string($table_name);
	$sanitized_subject_uid = (int) $_GET['uid'];
	$sanitized_read = (int) $_GET['read_access'];
	$sanitized_write = (int) $_GET['write_access'];
	$sanitized_admin = (int) $_GET['admin_access'];
	$sql="UPDATE `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` SET `table_permissions`.`read_access`='$sanitized_read', `table_permissions`.`write_access`='$sanitized_write', `table_permissions`.`admin_access`='$sanitized_admin' WHERE `tables`.`name`='$sanitized_table_name' AND `table_permissions`.`uid`='$sanitized_subject_uid'";
	mysql_query($sql);
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