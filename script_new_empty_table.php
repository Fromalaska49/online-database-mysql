<?php
require('session_handler.php');
require('connect.php');
if(isset($_POST['table_name'])&&isset($_POST['num_fields'])){
	$table_name = $_POST['table_name'];
	$num_fields = (int) $_POST['num_fields'];
	$sanitized_table_name = mysql_real_escape_string($table_name);
	
	$sql="CREATE TABLE `".$sanitized_table_name."` (";
	$sql.=" `id` int (8) NOT NULL AUTO_INCREMENT";
	for($i = 0;$i < $num_fields;$i++){
		$sql .= ", `Field_" . ($i+1) . "` varchar (1)";
	}
	$sql.=", PRIMARY KEY (id))";
	mysql_query($sql);
	
	mysql_query("INSERT INTO `tables` (`name`) VALUES ('$sanitized_table_name')");
	$table_id_row = mysql_fetch_array(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"));
	$sanitized_table_id = (int) $table_id_row['id'];
	$sanitized_uid = (int) $_SESSION['uid'];
	mysql_query("INSERT INTO `table_permissions` (`uid`,`table_id`,`read_access`,`write_access`,`admin_access`) VALUES ('$sanitized_uid','$sanitized_table_id','1','1','1')");
	unset($table_id_row);
	
	
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
	
	header('Location: view_table.php?table_name='.rawurlencode($table_name));	
}
?>