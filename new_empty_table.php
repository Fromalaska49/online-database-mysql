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
			
		});
	</script>
</head>
<body>
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<?php include('sidebar.php'); ?>
			<div class="content">
				<form action="script_new_empty_table.php" method="post">
					Table Name: 
                    <input type="text" placeholder="Table Name" name="table_name" />
					<br />
                    Number of Fields: 
					<input type="text" placeholder="Fields" name="num_fields" />
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
					<input type="submit" name="submit" id="upload_submit_button" value="Create">
				</form>
			</div>
		</div>
	</center>
</body>
</html>