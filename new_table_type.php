
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
	<style type="text/css">
		.new_table_type_selector_container{
		width:200px;
		height:200px;
		margin:10px;
		text-align:center;
		box-shadow:0px 1px 10px -1px black;
		border-style:none;
		border-radius:10px;
		display:inline-block;
		cursor:pointer;
		background-color:#555555;
		color:white;
		text-shadow:0px -1px 1px black;
		}
		.new_table_type_selector{
		display:table-cell;
		vertical-align:middle;
		height:inherit;
		width:inherit;
		}
	</style>
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
				<p style="padding:10px;">
					How would you like to create a new table?
				</p>
				<div>
					<a href="new_empty_table.php">
						<div class="new_table_type_selector_container">
							<div class="new_table_type_selector">
								New Empty Table
							</div>
						</div>
					</a>
					<a href="upload_csv_table.php">
						<div class="new_table_type_selector_container">
							<div class="new_table_type_selector">
								Upload CSV
							</div>
						</div>
					</a>
					<a href="duplicate_table.php">
						<div class="new_table_type_selector_container">
							<div class="new_table_type_selector">
								Duplicate Table
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
	</center>
</body>
</html>