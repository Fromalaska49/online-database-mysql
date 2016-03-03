
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
		$(document).ready(function(){
			$("input.delete_table_button").on("click",function(){
				var res = $(event.target).attr('id').split("_");
				var id = Number(res[res.length-1]);
				if(confirm("Are you sure you want to delete "+$("#table_name_"+id).text()+"?")){
					$.ajax({
						url:"script_delete_table.php",
						data: {
							id: id
						}
					});
					$("#table_permissions_container_"+id).hide();
				}
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
				<?php
					$decoded_table_name=rawurldecode($_GET['table_name']);
					$sanitized_table_name=mysql_real_escape_string($decoded_table_name);
					$sanitized_uid=(int) $_SESSION['uid'];
					$sql="SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `uid`='$sanitized_uid' AND `name`='$sanitized_table_name' ORDER BY `tables`.`name` ASC";
					$row=mysql_fetch_array(mysql_query($sql));
					if($row['admin_access']==1){
						$table_id = $row['table_id'];
						$sanitized_table_id = (int) $table_id;
						$table_name = $row['name'];
						echo('<div>Access granted to <div id="table_name_'.$table_id.'" style="display:inline;">'.htmlentities($table_name).'</div></div>');
						
						//rename table
						echo('<p>Rename Table</p>');
						echo('<div style="padding:10px;border-style:solid;border-color:#999999;border-width:1px;border-radius:10px;margin:10px;">');
							echo('<input type="text" placeholder="Table Name" value="'.htmlentities($table_name).'" id="new_table_name" />');
							echo('<input type="button" id="save_table_button" value="Save" />');
						echo('</div>');
					}
				?>
			</div>
		</div>
	</center>
</body>
</html>