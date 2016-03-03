<?php
$hour = date('G',time());
//if($hour >= 17){
	if(isset($_GET['wait_time'])){
		$minutes_remaining = (int) $_GET['wait_time'];
		$minutes_remaining++;
		while($minutes_remaining>0){
			$minutes_remaining--;
			$url = 'script_task_scheduler.php?wait_time='.$minutes_remaining;
			
			
			////////////////////////////////////////////////////////
			$from = 'do-not-reply@texasweddings.com';
			$to = 'ahutton@texasweddings.com';
			$subject = 't-'.$minutes_remaining.' minutes';
			$text = $minutes_remaining.' minutes remaining.';
			
			$html ='<body style="font-family:arial;"><center><div style="max-width:600px;">';
			$html.='<p>'.$minutes_remaining.' minutes remaining.</p>';
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
			
			$mail->IsHTML(true);
			$mail->Subject = $subject;
			$mail->AltBody = $text;
			$mail->Body = $html;
			
			if(!$mail->Send()){
				echo "Mailer Error: " . $mail->ErrorInfo;
				die('email not sent');
			}
			////////////////////////////////////////////////////////
			set_time_limit(70);
			sleep(60);
			//include($url);
			//shell_exec('"C:\Program Files (x86)\PHP\v5.3\php.exe" '.$url.' > /dev/null &');
			
			//exit();
		}
		//
			//script has executed
			$minutes_remaining = 0;
			
			$from = 'do-not-reply@texasweddings.com';
			$to = 'ahutton@texasweddings.com';
			$subject = 't-'.$minutes_remaining.' minutes';
			$text = $minutes_remaining.' minutes remaining.';
			
			$html ='<body style="font-family:arial;"><center><div style="max-width:600px;">';
			$html.='<p>'.$minutes_remaining.' minutes remaining.</p>';
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
			
			$mail->IsHTML(true);
			$mail->Subject = $subject;
			$mail->AltBody = $text;
			$mail->Body = $html;
			
			if(!$mail->Send()){
				echo "Mailer Error: " . $mail->ErrorInfo;
				die('email not sent');
			}
		//
	}
//}
?>