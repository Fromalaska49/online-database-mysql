<?php
session_start();
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
</head>
<body>

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
							Thank You
						</h1>
						<p style="text-align:;">
							<?php
							echo('Thank you for signing up '.$_SESSION['fname'].' '.$_SESSION['lname'].', to verify your email address, we have sent a confirmation email to '.$_SESSION['email'].'<br /><br /><input type="button" class="button-inactive" value="Resend Verification" />');
							?>
						</p>
					</div>
				</center>
			</div>
		</div>
	</center>
</body>
</html>