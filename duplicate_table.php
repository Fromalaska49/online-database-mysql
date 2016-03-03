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
				<form enctype="multipart/form-data" action="script_duplicate_table.php" method="POST">
					Table to duplicate:
					<br />
					<select name="table_name">
						<?php
						$result=mysql_query("SELECT `name` FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND (`table_permissions`.`read_access`='1' OR `table_permissions`.`write_access`='1' OR `table_permissions`.`admin_access`='1')");
						while($row=mysql_fetch_array($result)){
							echo('<option value="'.$row['name'].'">'.$row['name'].'</option>');
						}
						?>
					</select>
					<br />
					<input type="text" placeholder="New Table Name" name="new_table_name" />
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
					<input type="submit" name="submit" value="Create">
				</form>
			</div>
		</div>
	</center>
</body>
</html>