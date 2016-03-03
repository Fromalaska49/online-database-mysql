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
							Forgotten Password
						</h1>
						<p>
							To receive a link to reset your password, enter your email address below.
						</p>
						<form action="script_send_password_reset.php" method="POST">
							<input type="text" id="email" name="email" value="<?php if(isset($_GET['email'])){echo(rawurldecode($_GET['email']));} ?>" placeholder="Email" class="page-form-input" />
							<br />
							<input type="submit" value="Submit" class="button-inactive" />
						</form>
					</div>
				</center>
			</div>
		</div>
	</center>
</body>
</html>