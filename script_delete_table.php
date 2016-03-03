<?php
require('session_handler.php');
require('connect.php');
if(isset($_GET['table_name'])){
	$table_name=$_GET['table_name'];
	$sanitized_table_name = mysql_real_escape_string($table_name);
	$sanitized_uid = (int) $_SESSION['uid'];
	$row = mysql_fetch_array(mysql_query("SELECT `admin_access`,`table_id` FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `tables`.`name`='$sanitized_table_name' AND `table_permissions`.`uid`='$sanitized_uid'"));
	if($row['admin_access']==1){
		//delete table
		$table_id = $row['table_id'];
		$sanitized_table_id = (int) $table_id;
		unset($table_name_row);
		$time = time();
		$sanitized_new_table_name = mysql_real_escape_string($time.'_'.$table_name);
		$sanitized_time = (int) $time;
		$sql_rename_table = "RENAME TABLE `$sanitized_table_name` TO `$sanitized_new_table_name`";
		mysql_query($sql_rename_table);
		
		$table_id_row = mysql_fetch_array(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
		$sanitized_table_id = (int) $table_id_row['id'];
		
		$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
		$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
		unset($name_row);
		$sanitized_description = mysql_real_escape_string($user_name.' deleted the table "'.$table_name.'"');
		unset($user_name);
		$sanitized_target_table_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
		$sanitized_time = (int) time();
		$sanitized_action_id = 5;
		mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_description')");
		
		$saved_searches_result = mysql_query("SELECT `id` FROM `saved_searches` WHERE `table_id`='$sanitized_table_id'");
		while($saved_searches_row = mysql_fetch_array($saved_searches_result)){
			$sanitized_search_id = (int) $saved_searches_row['id'];
			mysql_query("DELETE FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id'");
		}
		$sanitized_search_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `saved_searches` WHERE `table_id`='$sanitized_table_id'"))->id;
		mysql_query("DELETE FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id'");
		mysql_query("DELETE FROM `saved_searches` WHERE `table_id`='$sanitized_table_id'");
		
		mysql_query("INSERT INTO `deleted_tables` (`table_id`,`authorizer_uid`,`time`,`table_name`,`deleted_table_name`) VALUES ('$sanitized_table_id','$sanitized_uid','$sanitized_time','$sanitized_table_name','$sanitized_new_table_name')");
		mysql_query("DELETE FROM `table_permissions` WHERE `table_id`='$sanitized_table_id'");
		mysql_query("DELETE FROM `tables` WHERE `id`='$sanitized_table_id'");
		mysql_query("INSERT INTO `log` (`entry`,`uid`,`time`) VALUES ('table deleted: $sanitized_table_name','$sanitized_uid','$sanitized_time')");
		header('Location: tables.php');
	}
	else{
		//user is not authorized to modify this save
	}
}
?>