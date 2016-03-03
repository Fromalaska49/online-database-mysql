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
		if($field_name!='id'){
			//delete field
			//echo('$sql = '.htmlentities("ALTER TABLE `$sanitized_table_name` DROP COLUMN `$field_name`").'<br />');
			//MySQL was reading the escaped field name as unescaped text i.e. When querying for `field_name'''` the escaped `field_name\'\'\'` could not be found
			if(mysql_query("ALTER TABLE `$sanitized_table_name` DROP COLUMN `$field_name`")){
				//Remove deleted field from saved searches
				$searches_result = mysql_query("SELECT `saved_searches`.`id` FROM `saved_searches` INNER JOIN `tables` ON `saved_searches`.`table_id`=`tables`.`id` WHERE `tables`.`name`='$sanitized_table_name'");
				while($searches_row = mysql_fetch_array($searches_result)){
					$sql="SELECT * FROM `".$sanitized_table_name."`";
					$english_sql="results from ".$table_name."";
					$search_get_vars="?table_name=".rawurlencode($table_name);
					$sanitized_search_id=(int)$searches_row['id'];
					$sql.=" WHERE";
					$english_sql.=" where";
					$num_params=mysql_num_rows(mysql_query("SELECT `search_id` FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id'"));
					for($i=0;$i<$num_params;$i++){
						while(mysql_num_rows(mysql_query("SELECT `field_name`,`selector`,`andor`,`text` FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id' AND `n`='$i' LIMIT 1"))!=1&&$i<1000){
							$num_params++;
							$i++;
						}
						$get = mysql_fetch_array(mysql_query("SELECT `field_name`,`selector`,`andor`,`text` FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id' AND `n`='$i' LIMIT 1"));
						//echo('$get["field_name"] = '.$get["field_name"].'; $field_name = '.$field_name.';<br />');
						if($get["field_name"]!=$field_name){
							$andor="";
							$get_var_andor;
							if($i>0){
								if($i==1){
									$get_0 = mysql_fetch_array(mysql_query("SELECT `field_name` FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id' AND `n`='$i'"));
									if($get_0["field_name"]==$field_name){
										//the first field was deleted, so the syntax must be readjusted
										$get_var_andor='';
									}
									unset($get_0);
								}
								$andor=(int) $get["andor"];
								$get_var_andor='&andor-'.$i.'='.$get["andor"];
							}
							else{
								$get_var_andor='';
							}
							$search_get_vars.=$get_var_andor.'&column-'.$i.'='.$get["field_name"].'&selector-'.$i.'='.$get["selector"].'&text-'.$i.'='.$get["text"];
							unset($get_var_andor);
							
							$text=$get["text"];
							$decoded_column_i=rawurldecode($get["field_name"]);
							$escaped_field=mysql_real_escape_string($decoded_column_i);
							$english_field=" ".htmlentities($decoded_column_i);
							unset($decoded_column_i);
							$selector=(int) $get["selector"];
							$text_i=$get["text"];
							$escaped_text=mysql_real_escape_string($text_i);
							$english_andor;
							$english_selection;
							if($andor==1){
								$escaped_andor=" AND";
								$english_andor=" and";
							}
							else if($andor==2){
								$escaped_andor=" OR";
								$english_andor=" or";
							}
							else{
								$escaped_andor="";
								$english_andor="";
							}
							if($selector==1){
								$escaped_selection="='".$escaped_text."'";
								$english_selection=" is '".htmlentities($text_i)."'";
							}
							elseif($selector==2){
								$escaped_selection="<>'".$escaped_text."'";
								$english_selection=" is not '".htmlentities($text_i)."'";
							}
							elseif($selector==3){
								$escaped_selection="<'".$escaped_text."'";
								$english_selection=" is less than '".htmlentities($text_i)."'";
							}
							elseif($selector==4){
								$escaped_selection=">'".$escaped_text."'";
								$english_selection=" is greater than '".htmlentities($text_i)."'";
							}
							elseif($selector==5){
								$escaped_selection="<='".$escaped_text."'";
								$english_selection=" is less than or equal to '".htmlentities($text_i)."'";
							}
							elseif($selector==6){
								$escaped_selection=">='".$escaped_text."'";
								$english_selection=" is greater than or equal to '".htmlentities($text_i)."'";
							}
							elseif($selector==7){
								$escaped_selection=" LIKE '%".$escaped_text."%'";
								$english_selection=" contains '".htmlentities($text_i)."'";
							}
							elseif($selector==8){
								$escaped_selection=" NOT LIKE '%".$escaped_text."%'";
								$english_selection=" does not contain '".htmlentities($text_i)."'";
							}
							elseif($selector==9){
								$escaped_selection="=''";
								$english_selection=" is blank";
							}
							elseif($selector==10){
								$escaped_selection="<>''";
								$english_selection=" is not blank";
							}
							elseif($selector==11){
								$escaped_selection="='".$escaped_text."%'";
								$english_selection=" begins with '".htmlentities($text_i)."'";
							}
							elseif($selector==12){
								$escaped_selection="<>'".$escaped_text."%'";
								$english_selection=" does not begin with '".htmlentities($text_i)."'";
							}
							elseif($selector==13){
								$escaped_selection="='%".$escaped_text."'";
								$english_selection=" ends with '".htmlentities($text_i)."'";
							}
							elseif($selector==14){
								$escaped_selection="<>'%".$escaped_text."'";
								$english_selection=" does not end with '".htmlentities($text_i)."'";
							}
							else{
								die('Call a doctor! This page is dead!');
							}
							unset($text_i);
							unset($selector);
							unset($escaped_text);
							$english_sql.=" ".$english_andor.$english_field.$english_selection;
							$sql.=$escaped_andor." `".$escaped_field."`".$escaped_selection;
							
							unset($english_andor);
							unset($english_field);
							unset($english_selection);
							unset($escaped_field);
							unset($escaped_selection);
						}
						else{
							//field deleted
							//do nothing; skip deleted field
						}
					}
					$sanitized_sql=mysql_real_escape_string($sql);
					$sanitized_english=mysql_real_escape_string($english_sql);
					$sanitized_get=mysql_real_escape_string($search_get_vars);
					$sql_query="UPDATE `saved_searches` SET `sql_version`='$sanitized_sql',`english_version`='$sanitized_english',`get_version`='$sanitized_get' WHERE `id`='$sanitized_search_id'";
					mysql_query($sql_query);
					mysql_query("DELETE FROM `saved_search_terms` WHERE `search_id`='$sanitized_search_id' AND `field_name`='$sanitized_field_name'");
				}
				$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
				$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
				unset($name_row);
				$sanitized_description = mysql_real_escape_string($user_name.' deleted the field "'.$field_name.'" from table '.$table_name);
				unset($user_name);
				$sanitized_target_table_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"))->id;
				$sanitized_time = (int) time();
				$sanitized_action_id = 7;
				mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`target_field_name`,`original_value`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_field_name','$sanitized_field_name','$sanitized_description')");
				
				echo('<script type="text/javascript">window.location.replace("manage_table.php?table_name='.rawurlencode($table_name).'");</script>');
			}
		}
		else{
			die('You cannot delete the id field.<br />');
		}
	}
	else{
		//user is not authorized to modify this save
		die('insufficient privileges<br />');
	}
}
?>