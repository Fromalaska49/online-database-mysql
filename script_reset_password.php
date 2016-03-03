<?php
session_start();
require('connect.php');
$key=$_POST['key'];
$sanitized_key=mysql_real_escape_string($key);
echo("SELECT * FROM `pw_reset_requests` WHERE `activation_key`='$sanitized_key' LIMIT 1");
$result = mysql_query("SELECT * FROM `pw_reset_requests` WHERE `activation_key`='$sanitized_key' LIMIT 1") or die(mysql_error());
if(mysql_num_rows($result)==1){
echo('asdasd');
	$request_data = mysql_fetch_array($result);
	unset($result);
	$sanitized_email = mysql_real_escape_string($request_data['email']);
	$sanitized_password = mysql_real_escape_string(md5($_POST['password']));
	mysql_query("UPDATE `users` SET `password`='$sanitized_password' WHERE `email`='$sanitized_email'");
	sleep(1);
	$result=mysql_query("SELECT * FROM `users` WHERE `email`='$sanitized_email' AND `password`='$sanitized_password'") or die(mysql_error());
	if(mysql_num_rows($result)>0){
		mysql("DELETE FROM `pw_reset_requests` WHERE `activation_key`='$sanitized_key' OR `email`='$sanitized_email'");
		$row=mysql_fetch_array($result);
		$_SESSION['uid']=$row['uid'];
		$_SESSION['email']=$row['email'];
		$_SESSION['fname']=$row['fname'];
		$_SESSION['lname']=$row['lname'];
		$redirect_url='http://'.$_SERVER['HTTP_HOST'].'/home.php';
		header('Location: '.$redirect_url);
		//echo('<script type="text/javascript">window.location.replace("'.$redirect_url.'");</script>');
	}
}
else{
	
}
mysql_query("DELETE FROM `pw_reset_requests` WHERE `activation_key`='$sanitized_key'");
?>