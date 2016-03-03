<?php
require('session_handler.php');
require('connect.php');
$sql_query;
$table_name='';
if(isset($_GET['table_name'])){
	$table_name=rawurldecode($_GET['table_name']);
	$sanitized_table_name=mysql_real_escape_string($table_name);
	if(isset($_GET['change'])&&isset($_GET['field_id'])){
		//$sanitized_id=(int)$_GET['id'];
		$sanitized_uid=(int)$_SESSION['uid'];
		$result=mysql_query("SELECT `write_access` FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND `tables`.`name`='$sanitized_table_name'");
		$row=mysql_fetch_array($result);
		if($row['write_access']==1){
			//user is authorised to edit values
			if(isset($_GET['text'])){
				$value=rawurldecode($_GET['text']);
				$sanitized_value=mysql_real_escape_string($value);
				$sanitized_field_id=(int)$_GET['field_id'];
				
				$result=mysql_query('SELECT * FROM `'.$sanitized_table_name.'` LIMIT 1');
				$num_fields=mysql_num_fields($result);
				$field_name=mysql_fetch_field($result,$sanitized_field_id)->name;
				$sanitized_field_name=mysql_real_escape_string($field_name);
				
				if($field_name!='id'){
					$field_metadata=mysql_fetch_array(mysql_query("SELECT column_name, data_type, character_maximum_length FROM information_schema.columns WHERE  table_schema = 'mainall' AND table_name = '$sanitized_table_name' AND column_name = '$sanitized_field_name'"));
					if($_GET['change']==2&&isset($_GET['record_id'])){
						//edit table-cell td
						$sanitized_record_id=(int)$_GET['record_id'];
						
						//must check if new value's length exceeds the field's maximum length
						$field_max_size=$field_metadata['character_maximum_length'];
						if($field_max_size<strlen($value)){
							$field_type=$field_metadata['data_type'];
							$sanitized_field_type=mysql_real_escape_string($field_type);
							$update_field_size_sql="ALTER TABLE `$sanitized_table_name` MODIFY `$sanitized_field_name` $sanitized_field_type(".strlen($value).")";
							mysql_query($update_field_size_sql);
						}
						
						$sanitized_target_table_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
						$sanitized_time = (int) time();
						$sanitized_action_id = 4;
						$original_value_row = mysql_fetch_array(mysql_query("SELECT `$sanitized_field_name` FROM `$sanitized_table_name` WHERE `id`='$sanitized_record_id' LIMIT 1"));
						$original_value = $original_value_row[$sanitized_field_name];
						$sanitized_original_value = mysql_real_escape_string($orginal_value);
						$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
						$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
						unset($name_row);
						$sanitized_description = mysql_real_escape_string($user_name.' edited the field "'.$field_name.'" at record '.$sanitized_record_id.'to "'.$new_field_name.'" in table '.$table_name);
						unset($user_name);
						mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`target_field_name`,`target_record_id`,`original_value`,`new_value`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_field_name','$sanitized_record_id','$sanitized_original_value','$sanitized_value','$sanitized_description')");
						
						$sql="UPDATE `$sanitized_table_name` SET `$sanitized_field_name`='$sanitized_value' WHERE `id`='$sanitized_record_id'";
						mysql_query($sql);
						
					}
					else if($_GET['change']==1){
						//edit table-cell th
						
						//must check if new value's length exceeds the field's maximum length
						/*
						$max_field_size_row=mysql_fetch_array(mysql_query("SELECT column_name, character_maximum_length FROM information_schema.columns WHERE  table_schema = 'mainall' AND table_name = '$sanitized_table_name' AND column_name = '$sanitized_field_name'"));
						$max_field_size=$max_field_size_row['character_maximum_length'];
						if($max_field_size<strlen($value)){
							$update_field_size_sql="ALTER TABLE `$sanitized_table_name` MODIFY `$sanitized_field_name` VARCHAR(".strlen($value).")";
							mysql_query($update_field_size_sql);
						}
						*/
						$field_type=$field_metadata['data_type'];
						$sanitized_field_type=mysql_real_escape_string($field_type);
						$sanitized_field_size=(int)$field_metadata['character_maximum_length'];
						$sql="ALTER TABLE `$sanitized_table_name` CHANGE `$sanitized_field_name` `$sanitized_value` $sanitized_field_type($sanitized_field_size)";
						
						$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
						$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
						unset($name_row);
						$sanitized_description = mysql_real_escape_string($user_name.' renamed the field "'.$field_name.'" to "'.$value.'" in table '.$table_name);
						unset($user_name);
						$sanitized_target_table_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
						$sanitized_time = (int) time();
						$sanitized_action_id = 3;
						mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`target_field_name`,`original_value`,`new_value`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_field_name','$sanitized_field_name','$sanitized_value','$sanitized_description')");
						
						
						mysql_query($sql);
					}
					/*
					else if($_GET['change']==1){
						//delete save
						mysql_query("DELETE FROM saved_searches WHERE id='$sanitized_id'");
					}
					*/
					else{
						//invalid change
					}
				}
			}
			else{
				//!isset($_GET['text']);
			}
		}
		else{
			//user is not authorized to modify this save
		}
	}
}
else{
	//!isset($_GET['table_name']);
}
?>