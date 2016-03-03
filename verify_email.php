<?php
session_start();
require('connect.php');
$key=urldecode($_GET['key']);
$sanitized_key=mysql_real_escape_string($_GET['key']);
$sql="SELECT * FROM `preusers` WHERE `activation_key`='$sanitized_key'";
$result=mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($result)==1){
	while($row=mysql_fetch_array($result)){
		$sanitized_email=mysql_real_escape_string($row['email']);
		$sanitized_password=mysql_real_escape_string($row['password']);
		$sanitized_fname=mysql_real_escape_string($row['fname']);
		$sanitized_lname=mysql_real_escape_string($row['lname']);
		if(mysql_query("INSERT INTO `users` (`email`,`password`,`fname`,`lname`) VALUES ('$sanitized_email','$sanitized_password','$sanitized_fname','$sanitized_lname')")){
			$fetched_row=mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `email`='$sanitized_email'"));
			$_SESSION['uid']=$fetched_row['uid'];
			$_SESSION['email']=$fetched_row['email'];
			$_SESSION['fname']=$fetched_row['fname'];
			$_SESSION['lname']=$fetched_row['lname'];
			mysql_query("DELETE FROM `preusers` WHERE 'activation_key'='$sanitized_key'");
			
			$sanitized_uid=(int)$_SESSION['uid'];
			$tables_result=mysql_query("SELECT `id` FROM `tables`");
			while($tables_row=mysql_fetch_array($tables_result)){
				$sanitized_table_id=(int)$tables_row['id'];
				mysql_query("INSERT INTO `table_permissions` (`uid`,`table_id`,`read_access`,`write_access`,`admin_access`) VALUES ('$sanitized_uid','$sanitized_table_id','0','0','0')");
			}
			mysql_query("INSERT INTO `user_permissions` (`uid`,`table_permissions`,`user_permissions`) VALUES ('$sanitized_uid','0','0')");
			
			$url_redirect='home.php';
			header('Location: '.$url_redirect);
		}
	}
}
else{
	echo('mysql_num_rows($result)='.mysql_num_rows($result));
}
?>