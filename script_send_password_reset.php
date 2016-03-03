<?php
session_start();
include('connect.php');
if(isset($_POST['email'])){
	$email=$_POST['email'];
	$sanitized_email=mysql_real_escape_string($email);
	$sql = "SELECT * FROM `users` WHERE `email`='$sanitized_email' LIMIT 1";
	$user_result = mysql_query($sql);
	unset($sql);
	if(mysql_num_rows($user_result)==1){
		$user_data = mysql_fetch_array($user_result);
		
		$fname=$user_data['fname'];
		$lname=$user_data['lname'];
		
		if(mysql_num_rows(mysql_query("SELECT * FROM `pw_reset_requests` WHERE `email`='$sanitized_email'"))>0){
			mysql_query("DELETE FROM `pw_reset_requests` WHERE `email`='$sanitized_email'");
		}

		$characters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$key='';
		$key_length=128;
		for($i=0;$i<$key_length;$i++){
			$key.=$characters[rand(0,strlen($characters)-1)];
		}
		mysql_query("DELETE FROM `pw_reset_requests` WHERE `email`='$sanitized_email'");
		$sanitized_key=mysql_real_escape_string($key);
		$sanitized_time = (int) time();
		$sql="INSERT INTO `pw_reset_requests` (`email`,`activation_key`,`time`) VALUES ('$sanitized_email','$sanitized_key','$sanitized_time')";
		if(mysql_query($sql)){
			$site = $_SERVER['HTTP_HOST'];
			$verification_link = 'http://'.$site.'/reset_password.php?key='.$key;

			$from = 'do-not-reply@texasweddings.com';
			$to = $email;
			$subject = 'Password Reset';
			
			$text = 'This email has been sent to verify that you registered for an account at '.$site.' '.$verification_link.'\nThis email indicates that an account was registered with '.$site.'. If you did not authorize the creation of this acount, then you may ignore this email, and we will automatically remove the acount.';
							$html ='<body style="font-family:arial;"><center><div style="max-width:600px;">';
							$html.='This email has been sent so you can reset the password for your account at <a href="http://'.$site.'">'.$site.'</a><br /><br />';
							//CSS is too complicated for some email clients
							//$html.='<a href="'.$verification_link.'"><div style="display:inline-block;padding:10px;font-size:25px;color:white;border-radius:5px;border-style:solid;border-color:#444444;corder-width:1px;background-color:gray;">Reset Password</div></a>';
							$html.='<a href="'.$verification_link.'" style="margin:10px;font-size:30px;">Reset Password</a>';
							$html.='<br />';
							$html.='<p style="color:gray;text-align:left;">This email indicates that a password reset was requested on '.$site.'. If you do not wish to reset your password, then you may delete this email and your password will remain unchanged.</p>';
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
			$mail->Subject    = "Password Reset";
			$mail->AltBody    = $text;
			$mail->Body    = $html;
			
			if(!$mail->Send())
			{
			  echo "Mailer Error: " . $mail->ErrorInfo;
			  die('email not sent');
			}

			$_SESSION['email']=$email;
			$_SESSION['fname']=$fname;
			$_SESSION['lname']=$lname;
			
			$url='pw_reset_requested.php';
			header('Location: '.$url);
		}
		else{
			//not entered into preusers
			echo('not entered into preusers');
		}
	}
	else{
		//this email is already associated with an account
		echo('this email is already associated with an account');
	}
	
}
else{
	echo('else');
	$url='signup.php';
	header('Location: '.$url);
}
?>