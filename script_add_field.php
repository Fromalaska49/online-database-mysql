<?php
require('session_handler.php');
require('connect.php');
if(isset($_GET['table_name'])&&isset($_GET['field_name'])){
	$table_name = rawurldecode($_GET['table_name']);
	$sanitized_table_name = mysql_real_escape_string($table_name);
	$field_name=rawurldecode($_GET['field_name']);
	$sanitized_field_name = mysql_real_escape_string($field_name);
	$sanitized_uid = (int) $_SESSION['uid'];
	$sql="SELECT `admin_access` FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `name`='$sanitized_table_name' AND `uid`=$sanitized_uid";
	$row = mysql_fetch_array(mysql_query($sql));
	if($row['admin_access']==1){
		//authorized to add field
		if(mysql_query("ALTER TABLE `$sanitized_table_name` ADD COLUMN `$sanitized_field_name` VARCHAR(1) NOT NULL AFTER `id`")){
			/*
			$field_type=$field_metadata['data_type'];
			$sanitized_field_type=mysql_real_escape_string($field_type);
			$sanitized_field_size=(int)$field_metadata['character_maximum_length'];
			$sql="ALTER TABLE `$sanitized_table_name` CHANGE `$sanitized_field_name` `$sanitized_value` $sanitized_field_type($sanitized_field_size)";
			*/
			
			$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
			$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
			unset($name_row);
			$sanitized_description = mysql_real_escape_string($user_name.' added the field "'.$field_name.'" in table '.$table_name);
			unset($user_name);
			$id_row = mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
			$sanitized_target_table_id = (int) $id_row->id;
			$sanitized_time = (int) time();
			$sanitized_action_id = 6;
			mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`target_field_name`,`new_value`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_field_name','$sanitized_field_name','$sanitized_description')");
			echo('<script type="text/javascript">window.location.href=\'manage_table.php?table_name='.rawurlencode($table_name).'\';</script>');
		}
		else{
			//Query failed, probably because the field already exists
			die('<script type="text/javascript">alert("Could not add field: A field with that name may already exist.");window.history.back();</script>');
		}
	}
	else{
		//user is not authorized to modify this save
		die('insuficient privileges\n');
	}
}
?>