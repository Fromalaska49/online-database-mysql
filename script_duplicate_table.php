<?php
require('session_handler.php');
require('connect.php');
if(isset($_POST['table_name'])){
	$table_name = $_POST['table_name'];
	$new_table_name = $_POST['new_table_name'];
	if($new_table_name==''){
		$new_table_name=$table_name;
	}
	$sanitized_table_name = mysql_real_escape_string($table_name);
	$sanitized_new_table_name = mysql_real_escape_string($new_table_name);
	$sanitized_uid = (int) $_SESSION['uid'];
	$row = mysql_fetch_array(mysql_query("SELECT `read_access`,`write_access`,`admin_access`,`table_id` FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `tables`.`name`='$sanitized_table_name' AND `table_permissions`.`uid`='$sanitized_uid'"));
	if($row['read_access']==1||$row['write_access']==1||$row['admin_access']==1){
		//authorized to access this table
		$num_tables_result=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'mainall' AND table_name = '$sanitized_new_table_name'"));
		if($num_tables_result['COUNT(*)']>0){
			//duplicate table name
			$duplicate_table_number=1;
			do{
				$duplicate_table_number++;
				$sanitized_new_table_name=mysql_real_escape_string($new_table_name.' '.$duplicate_table_number);
				$num_tables_result=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'mainall' AND table_name = '$sanitized_new_table_name'"));
				$num_tables=$num_tables_result['COUNT(*)'];
			}while(!($num_tables==0));
			$new_table_name=$new_table_name.' '.$duplicate_table_number;
		}
		mysql_query("CREATE TABLE `$sanitized_new_table_name` LIKE `$sanitized_table_name`;") or die(mysql_error());
		mysql_query("INSERT `$sanitized_new_table_name` SELECT * FROM `$sanitized_table_name`;") or die(mysql_error());
		mysql_query("INSERT INTO `tables` (`name`) VALUES ('$sanitized_new_table_name')") or die(mysql_error());
		$table_id_row=mysql_fetch_array(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_new_table_name'"));
		$sanitized_table_id=(int)$table_id_row['id'];
		$sanitized_uid=(int)$_SESSION['uid'];
		mysql_query("INSERT INTO `table_permissions` (`uid`,`table_id`,`read_access`,`write_access`,`admin_access`) VALUES ('$sanitized_uid','$sanitized_table_id','1','1','1')");
		
		$sanitized_read_access = 0;
		$sanitized_write_access = 0;
		$sanitized_admin_access = 0;
		if(isset($_POST['read_access'])){
			$sanitized_read_access = 1;
		}
		if(isset($_POST['write_access'])){
			$sanitized_write_access = 1;
		}
		if(isset($_POST['admin_access'])){
			$sanitized_admin_access = 1;
		}
		$i=0;
		$sql = 'INSERT INTO `table_permissions` (`uid`,`table_id`,`read_access`,`write_access`,`admin_access`) VALUES';
		$result = mysql_query("SELECT `uid` FROM `users` WHERE `uid` != '$sanitized_uid'");
		while($row = mysql_fetch_array($result)){
			$sanitized_uid = (int) $row['uid'];
			if($i>0){
				$sql.=', (\''.$sanitized_uid.'\',\''.$sanitized_table_id.'\',\''.$sanitized_read_access.'\',\''.$sanitized_write_access.'\',\''.$sanitized_admin_access.'\')';
			}
			else{
				$sql.=' (\''.$sanitized_uid.'\',\''.$sanitized_table_id.'\',\''.$sanitized_read_access.'\',\''.$sanitized_write_access.'\',\''.$sanitized_admin_access.'\')';
			}
			$i++;
		}
		mysql_query($sql);
		unset($result);
		unset($row);
		unset($sql);
		
		header('Location: view_table.php?table_name='.rawurlencode($new_table_name));
	}
	else{
		//user is not authorized to modify this save
	}
}
?>