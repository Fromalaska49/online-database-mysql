<?php
require('session_handler.php');
require('connect.php');
if(isset($_GET['table_name'])&&isset($_GET['field_name'])&&isset($_GET['new_field_name'])){
	$table_name = rawurldecode($_GET['table_name']);
	$sanitized_table_name = mysql_real_escape_string($table_name);
	$field_name=rawurldecode($_GET['field_name']);
	$sanitized_field_name = mysql_real_escape_string($field_name);
	$new_field_name=rawurldecode($_GET['new_field_name']);
	$sanitized_new_field_name = mysql_real_escape_string($new_field_name);
	$sanitized_uid = (int) $_SESSION['uid'];
	$sql="SELECT `admin_access` FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `name`='$sanitized_table_name' AND `uid`=$sanitized_uid";
	$row = mysql_fetch_array(mysql_query($sql));
	if($row['admin_access']==1&&$field_name!='id'){
		//authorized to add field
		unset($row);
		$row=mysql_fetch_array(mysql_query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = '$sanitized_table_name' AND COLUMN_NAME = '$sanitized_field_name'"));
		$sanitized_definition=mysql_real_escape_string($row['COLUMN_TYPE']);
		if(mysql_query("ALTER TABLE `$sanitized_table_name` CHANGE COLUMN `$sanitized_field_name` `$sanitized_new_field_name` $sanitized_definition")){
			$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
			$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
			unset($name_row);
			$sanitized_description = mysql_real_escape_string($user_name.' renamed the field "'.$field_name.'" to "'.$new_field_name.'" in table '.$table_name);
			unset($user_name);
			$sanitized_target_table_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
			$sanitized_time = (int) time();
			$sanitized_action_id = 3;
			mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`target_field_name`,`original_value`,`new_value`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_field_name','$sanitized_field_name','$sanitized_new_field_name','$sanitized_description')") or die(mysql_error());
			
			header('Location: manage_table.php?table_name='.rawurlencode($table_name));
		}
		else{
			//Query failed, probably because the field already exists
			die('<script type="text/javascript">alert("Could not add field: A field with that name may already exist.");window.history.back();</script>');
		}
	}
	else{
		//user is not authorized to modify this save
		die('insufficient privileges\n');
	}
}
?>