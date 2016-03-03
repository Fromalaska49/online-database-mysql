<?php
session_start();
$login_script='script_login.php';
$_SERVER['PHP_SELF'];
$decoded_target='';
if(isset($_GET['target'])){
	$decoded_target=rawurldecode($_GET['target']);
}
else if(isset($_SERVER['HTTP_REFERER'])){
	$decoded_target=$_SERVER['HTTP_REFERER'];
}
else{
	$decoded_target='home.php';
}
$decoded_target_length=strlen($decoded_target);
$file_headers = @get_headers($decoded_target);
$file_exists = true;
if(strpos($file_headers[0],'404')) {
    $file_exists = false;
}
if(!$file_exists||$decoded_target_length==0){
	$decoded_target='home.php';
}
else if($decoded_target_length>strlen($_SERVER['PHP_SELF'])){
	if(substr($decoded_target,$decoded_target_length-strlen($_SERVER['PHP_SELF'])-1,$decoded_target_length-1)==$_SERVER['PHP_SELF']){
		$decoded_target='home.php';
	}
}
else if($decoded_target_length>strlen($login_script)){
	if(substr($decoded_target,$decoded_target_length-strlen($login_script)-1,$decoded_target_length-1)==$login_script){
		$decoded_target='home.php';
	}
}
$attempts;
$secondary_attempt=false;
$secondary_attempt_visible=false;
$excessive_attempts=false;
$excessive_attempts_visible=false;
if(isset($_SESSION['attempts'])){
	if($_SESSION['attempts']>=0){
		$secondary_attempt=true;
		if($_SESSION['attempts']>=10){
			$excessive_attempts=true;
		}
		$attempts=$_SESSION['attempts']+1;
		if(isset($_GET['attempt'])){
			if($_GET['attempt']>=1){
				$secondary_attempt_visible=true;
				if($_GET['attempt']>=10){
					$excessive_attempts_visible=true;
				}
			}
		}
	}
	else{
		$attempts=1;
	}
}
else{
	$attempts=1;
}
if(isset($_GET['email'])){
	$email=rawurldecode($_GET['email']);
}
else{
	$email='';
}
$_SESSION['attempts']=$attempts;
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
	<script type="text/javascript">
		function signupRedirect(){
			var email=document.getElementById('email').value;
			var url='signup.php?email='+email;
			window.location=url;
		}
		function forgotRedirect(){
			var email=document.getElementById('email').value;
			var url='forgot_password.php?email='+email;
			window.location=url;
		}
		<?php
		if(isset($_SESSION['uid'])){
			echo("$(document).ready(function(){window.location='home.php';});");
		}
		?>
	</script>
</head>
<body onload="">
	<div class="head">
		<ul class="head-menu">
			<li class="head-menu">
				<?php include('site_name.php'); ?>
			</li>
		</ul>
	</div>
	<center>
		<div class="body">
			<div class="head-spacer">
			</div>
			<div style="padding:25px;">
				<center>
					<div class="page-form-container">
						<h1>
							Login
						</h1>
						<?php
						if($secondary_attempt_visible&&!$excessive_attempts_visible){
							echo('<p style="color:red;">The email and password don\'t match.</p>');
						}
						else if($excessive_attempts_visible){
							echo('<p>You didn\'t say the magic word!</p>');
							echo('<p style="color:red;">Your account has been locked.</p>');
						}
						?>
						<form action="<?php echo($login_script); ?>" method="POST">
							<input type="text" name="target" placeholder="Ignore this." style="display:none;" value="<?php echo($decoded_target); ?>" />
							<input type="text" id="email" name="email" value="<?php echo($email); ?>" placeholder="Email" class="page-form-input" />
							<br />
							<input type="password" name="password" placeholder="Password" class="page-form-input" />
							<br />
							<input type="button" name="signup" value="Sign Up" onclick="signupRedirect()" class="button-inactive" />
							<input type="submit" id="butto" name="login" value="Login" onsubmit="return true" class="button-inactive" />
							<br />
							<div style="display:block;margin:0px;padding:0px;text-align:center;">
								<p style="color:#AAA;padding:0px;margin:0px 0px 0px 10px;font-size:12px;text-align:center;cursor:pointer;text-decoration:underline;" onclick="forgotRedirect()">
									Forgot Password
								</p>
							</div>
						</form>
					</div>
				</center>
			</div>
		</div>
	</center>
</body>
</html>