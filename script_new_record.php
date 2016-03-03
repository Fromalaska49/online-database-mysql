<?php
require('session_handler.php');
require('connect.php');
$sanitized_uid = (int) $_SESSION['uid'];
$table_name = rawurldecode($_GET['table_name']);
$sanitized_table_name = mysql_real_escape_string($table_name);
if(mysql_num_rows(mysql_query("SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND `tables`.`name`='$sanitized_table_name' AND `write_access`>0"))>0){
	//user has permissions to write to table
	//Insert null record
	mysql_query("START TRANSACTION;");// or die(mysql_error());
	mysql_query("UPDATE `$sanitized_table_name` SET `id`=`id`+1 WHERE `id`>=1 ORDER BY `id` DESC;");// or die(mysql_error());
	mysql_query("INSERT INTO `$sanitized_table_name` (`id`) VALUES ('1');");// or die(mysql_error());
	mysql_query("COMMIT;");// or die(mysql_error());
	//send user to location of null record so that they can enter data
	$page_size = 30;
	$num_records = mysql_num_rows(mysql_query("SELECT `id` FROM `$sanitized_table_name`"));
	$page = floor($num_records/$page_size);
	$url_redirect = 'edit_table.php?table_name='.rawurlencode($table_name);//.'&result_page='.$page;
	header('Location: '.$url_redirect);
	echo('<script type="text/javascript">$(document).ready(function(){window.location.href=\''.$url_redirect.'\'});</script>');
}
?>