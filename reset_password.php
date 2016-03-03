<html>
<head>
	<?php include('head-includes.php'); ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#password_reset_form").submit(function(e){
				if($("#pw").val() == "" || $("#pw_confirm").val() == ""){
					alert("You must enter the password.");
					e.preventDefault();
				}
				else if($("#pw").val() !== $("#pw_confirm").val()){
					alert("The passwords must match.");
					e.preventDefault();
				}
			});
		});
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
							Reset Password
						</h1>
						<p>
							Enter a new password below.
						</p>
						<form id="password_reset_form" action="script_reset_password.php" method="POST">
							<input type="text" name="key" style="display:none;" value="<?php echo($_GET['key']); ?>" />
							<input type="password" id="pw" name="password" placeholder="New Password" class="page-form-input" />
							<br />
							<input type="password" id="pw_confirm" placeholder="Confirm Password" class="page-form-input" />
							<br />
							<input type="submit" value="Set Password" class="button-inactive" />
						</form>
					</div>
				</center>
			</div>
		</div>
	</center>
</body>
</html>