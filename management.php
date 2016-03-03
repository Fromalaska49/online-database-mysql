
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
					$sanitized_uid=(int) $_SESSION['uid'];
					/*
					$sql="SELECT * FROM table_permissions INNER JOIN tables ON table_permissions.table_id=tables.id WHERE uid='$sanitized_uid' ORDER BY tables.name ASC";
					$result=mysql_query($sql);
					while($row=mysql_fetch_array($result)){
						if($row['admin_access']==1){
							$table_id = $row['table_id'];
							echo('<div style="padding:10px;border-style:solid;border-color:#999999;border-width:1px;border-radius:10px;margin:10px;" id="table_permissions_container_'.$table_id.'">');
							$sanitized_table_id = (int) $table_id;
							//$sub_result = mysql_query("SELECT * FROM tables WHERE id='$sanitized_table_id'");
							//$sub_row = mysql_fetch_array($sub_result);
							$table_name = $row['name'];
							echo('<div>Access granted to <div id="table_name_'.$table_id.'" style="display:inline;">'.htmlentities($table_name).'</div></div>');
							
							//rename table
							echo('<p>Table Name <input type="text" placeholder="name" value="'.htmlentities($table_name).'" /></p>');
							
							echo('<p><a href="change_column_order.php?table_name='.rawurlencode($table_name).'">Change column order</a></p>');
							
							//rename fields
							//echo('<p>rename columns</p>');
							
							//create new fields
							echo('<p>new column</p>');
							
							echo('<input type="button" value="Delete Table" id="delete_table_button_'.$table_id.'" class="delete_table_button" /><br />');
							//delete records
							//echo('<p><a href="edit_table.php">Edit Table</a></p>');
							echo('</div>');
						}
					}
					*/
					$sql2="SELECT * FROM user_permissions WHERE uid='$sanitized_uid'";
					$result2=mysql_query($sql2);
					while($row2=mysql_fetch_array($result2)){
						if($row2['table_permissions']==1){
							echo('<p>Manage table permissions</p>');
							
							$result=mysql_query("SELECT * FROM `tables`");
							echo('<ul style="margin:0px;padding:1px;border-style:solid;border-width:1px;border-color:#555555;border-radius:3px;display:block;">');
					echo('<a href="new_table_type.php" class="saved-search-item"><li class="saved-search-item" title="Make a new search"><img src="images/icons/s/add-button.png" class="saved-search-item" /><p class="saved-search-item">New Table</p></li></a>');
							if(mysql_num_rows($result)>0){
								while($row=mysql_fetch_array($result)){
									echo('<a href="manage_table_permissions.php?table_name='.rawurlencode($row['name']).'" class="saved-search-item"><li class="saved-search-item" title="'.htmlentities($row['name']).'"><img src="images/icons/s/remove-button.png" class="saved-search-item" id="img-'.$row['id'].'" style="display:none;" title="remove" /><p class="saved-search-item">'.htmlentities($row['name']).'</p></li></a>');
								}
							}
							echo('</ul>');
							echo('<br />');
						}
						if($row2['user_permissions']==1){
							echo('<p>Super User Permissions</p>');
							echo('<br />');
						}
					}
				?>
			</div>
		</div>
	</center>
</body>
</html>