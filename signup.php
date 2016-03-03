<?php
session_start();
/*
if(isset($_SERVER['HTTP_REFERER'])){
	$target=$_SERVER['HTTP_REFERER'];
}
else{
	$target='home.php';
}
$target=rawurlencode($target);
*/
$email='';
if(isset($_GET['email'])){
	$email.=rawurldecode($_GET['email']);
}
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
</head>
<body>
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<div class="head-spacer">
			</div>
			<div style="padding:25px;">
				<center>
					<div class="page-form-container">
						<h1>
							Sign Up
						</h1>
						<form action="script_signup.php" method="POST">
							<input type="text" name="email" placeholder="Email" value="<?php echo($email); ?>" />
							<br />
							<input type="password" name="password" placeholder="Password" />
							<br />
							<input type="text" name="fname" placeholder="First Name" />
							<br />
							<input type="text" name="lname" placeholder="Last Name" />
							<br />
							<input type="submit" onsubmit="return true" class="button-inactive" />
						</form>
					</div>
				</center>
			</div>
		</div>
	</center>
</body>
</html>