<?php
session_start();
require('connect.php');
if(isset($_POST['login'])){
	$sanitized_email=mysql_real_escape_string($_POST['email']);
	$sanitized_password=mysql_real_escape_string(md5($_POST['password']));
	$result=mysql_query("SELECT * FROM `users` WHERE `email`='$sanitized_email' AND `password`='$sanitized_password'");
	if(mysql_num_rows($result)>0){
		$row=mysql_fetch_array($result);
		$_SESSION['uid']=$row['uid'];
		$_SESSION['email']=$row['email'];
		$_SESSION['fname']=$row['fname'];
		$_SESSION['lname']=$row['lname'];
		$redirect_url=$_POST['target'];
		header('Location: '.$redirect_url);
	}
	else{
		$redirect_url='login.php?attempt='.$_SESSION['attempts'].'&email='.rawurlencode($_POST['email']).'&target='.rawurlencode($_POST['target']);
		header('Location: '.$redirect_url);
	}
}
else if(isset($_POST['signup'])){
	$redirect_url='signup.php';
	if(isset($_POST['email'])){
		$redirect_url.='?email='.rawurlencode($_POST['email']);
	}
	header('Location: '.$redirect_url);
}
?>