<?php
session_start();
include('connect.php');
if(isset($_POST['email'])&&isset($_POST['password'])&&isset($_POST['fname'])&&isset($_POST['lname'])){
	$email=$_POST['email'];
	$password=$_POST['password'];
	$fname=$_POST['fname'];
	$lname=$_POST['lname'];
	
	$sanitized_email=mysql_real_escape_string($email);
	$password=md5($password);
	$sanitized_password=mysql_real_escape_string($password);
	$sanitized_fname=mysql_real_escape_string($fname);
	$sanitized_lname=mysql_real_escape_string($lname);
	
	
	if(mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `email`='$sanitized_email'"))==0){
		sleep(1);
		if(mysql_num_rows(mysql_query("SELECT * FROM `preusers` WHERE `email`='$sanitized_email'"))==0){
			$characters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$key='';
			$key_length=128;
			for($i=0;$i<$key_length;$i++){
				$key.=$characters[rand(0,strlen($characters)-1)];
			}
			$sanitized_key=mysql_real_escape_string($key);
			$sanitized_time = (int) time();
			$sql="INSERT INTO `preusers` (`email`,`password`,`fname`,`lname`,`activation_key`,`time`) VALUES ('$sanitized_email','$sanitized_password','$sanitized_fname','$sanitized_lname','$sanitized_key','$sanitized_time')";
			if(mysql_query($sql)){
				$site = $_SERVER['HTTP_HOST'];
				$verification_link = 'http://'.$site.'/verify_email.php?key='.$key;
				
				$from = 'do-not-reply@texasweddings.com';
				$to = $email;
				$subject = 'Email Verification';
				
				$text = 'This email has been sent to verify that you registered for an account at '.$site.' '.$verification_link.'\nThis email indicates that an account was registered with '.$site.'. If you did not authorize the creation of this acount, then you may ignore this email, and we will automatically remove the acount.';
				$html ='<body style="font-family:arial;"><center><div style="max-width:600px;">';
				$html.='This email has been sent to verify that you registered for an account at <a href="http://'.$site.'">'.$site.'</a><br /><br />';
				//CSS too complicated for some email clients
				//$html.='<a href="'.$verification_link.'"><div style="display:inline-block;padding:10px;font-size:25px;color:white;border-radius:5px;border-style:solid;border-color:#444444;corder-width:1px;background-color:gray;">Verify Email</div></a>';
				$html.='<a href="'.$verification_link.'" style="margin:10px;font-size:30px;">Verify Email</a>';
				$html.='<br />';
				$html.='<p style="color:gray;text-align:left;">This email indicates that an account was registered with '.$site.'. If you did not authorize the creation of this account, then you may ignore this email, and the acount will be removed automatically.</p>';
				$html.='</div></center></body>';
				
				require_once('class.phpmailer.php');
				
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->CharSet="UTF-8";
				$mail->SMTPSecure = 'ssl';
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->Username = 'do-not-reply@texasweddings.com';
				$mail->Password = 'l%h2gOeOiN9W';
				$mail->SMTPAuth = true;
				
				$mail->From = $from;
				$mail->FromName = 'Do Not Reply';
				$mail->AddAddress($to);
				//$mail->AddReplyTo('do-not-reply@texasweddings.com', 'Information');
				
				$mail->IsHTML(true);
				$mail->Subject    = "Email Verificaion";
				$mail->AltBody    = $text;
				$mail->Body    = $html;
				
				if(!$mail->Send()){
				  //echo "Mailer Error: " . $mail->ErrorInfo;
				  //die('email not sent');
				}
				
				
				/*
				$site='example.com';
				$from = 'noreply@'.$site;
				$subject = 'Email Verification';
				
				$verification_link='http://'.$site.'/verfy_email.php?key='.rawurlencode($key);
				$body ='<body style="font-family:arial;"><center><divstyle="max-width:600px;"><br />';
				$body.='This email has been sent to verify that you registered for an account at <a href="http://'.$site.'"'.$site.'</a><br /><br />';
				$body.='<a href="'.$verification_link.'"><div style="display:inline-block;padding:10px;font-size:25px;color:white;border-radius:5px;border-style:solid;border-color:#444444;corder-width:1px;background-color:gray;">Verify Email</div></a>';
				$body.='<br />';
				$body.='<p style="color:gray;text-align:left;">This email indicates that an account was registered with '.$site.'. If you did not authorize the creation of this acount, then you may ignore this email, and we will automatically remove the acount.</p>';
				$body.='</div></center></body>';
				
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= "From: " . $from . "\r\n";
				
				$to=$email;
				
				*/
				
				$_SESSION['email']=$email;
				$_SESSION['fname']=$fname;
				$_SESSION['lname']=$lname;
				
				$url='signupsent.php';
				header('Location: '.$url);
				echo('<script type="text/javascript">window.location.href=\''.$url.'\';</script>');
				exit();
			}
			else{
				//not entered into preusers
				//echo('not entered');
				echo('error code: 4');
			}
		}
		else{
			//this email has already been entered into preusers
			//echo('this email has already been entered as a preuser');
			echo('error code: 3');
		}
	}
	else{
		//this email is already associated with an account
		//echo('this email is already associated with an account');
		echo('error code: 2');
	}
}
else{
	//echo('else');
	$url='signup.php';
	header('Location: '.$url);
}
?>