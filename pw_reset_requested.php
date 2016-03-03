<?php
session_start();
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
							Reset Requested
						</h1>
						<p style="text-align:;">
							<?php
							echo('Your password reset has been requested. Check your inbox for an email with further instructions.');
							?>
						</p>
					</div>
				</center>
			</div>
		</div>
	</center>
</body>
</html>