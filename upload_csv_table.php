<?php
require('connect.php');
require('session_handler.php');
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
	<title>
		<?php include('site_title.php'); ?>
	</title>
	<link rel="stylesheet" type="text/css" href="dragtable.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script src="jquery.dragtable.js"></script>
	<script type="text/javascript">
  $(document).ready(function() {
	  $("#upload_submit_button").on("click",function(){
		$("#loading_message_2").hide();
		$("#loading_indicator").fadeIn();
		var cycles = 0;
		var interval = 4000;
		window.setInterval(function(){
			if(cycles%2==0){
				$("#loading_message_1").fadeOut("slow",function(){
					$("#loading_message_2").fadeIn("slow");
				});
			}
			else{
				$("#loading_message_2").fadeOut("slow",function(){
					$("#loading_message_1").fadeIn("slow");
				});
			}
			cycles++;
		}, interval);
	});
});
</script>
</head>
<body>
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<?php include('sidebar.php'); ?>
			<div class="content">
				<form enctype="multipart/form-data" action="script_upload_csv_table.php" method="post">
					<input type="text" placeholder="Table Name" name="table_name" />
					<br />
					<input size="50" type="file" name="filename">
					<br />
					<p>
						Permissions for everyone:
					</p>
					<input type="checkbox" name="read_access" /> Read Access
					<br />
					<input type="checkbox" name="write_access" /> Write Access
					<br />
					<input type="checkbox" name="admin_access" /> Admin Access
					<br />
					<input type="submit" name="submit" id="upload_submit_button" value="Upload">
					<div id="loading_indicator" style="font-size:20px;display:none;">
						<img src="images/icons/original/loading.gif" style="height:20px;" id="loading_icon" />
						<div id="loading_message_1" style="display:inline;"> Uploading...</div>
						<div id="loading_message_2" style="display:inline;"> Do not close this page.</div>
					</div>
				</form>
			</div>
		</div>
	</center>
</body>
</html>