
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
			$(".permissions_checkbox").on("click",function(){
				var res = $(event.target).attr('id').split("_");
				var id = Number(res[res.length-1]);
				if(res[0]=="table"){
					if($(this).prop("checked")==true){
						$("#read_access_"+id).prop("checked", true);
						$("#write_access_"+id).prop("checked", true);
						$.ajax({
							url: "script_manage_user_permissions.php",
							data: {
								user_id: id,
								table: "1"
							}
						});
					}
					else{
						$.ajax({
							url: "script_manage_user_permissions.php",
							data: {
								user_id: id,
								table: "0",
							}
						});
					}
				}
				else if(res[0]=="user"){
					if($(this).prop("checked")==true){
						$("#read_access_"+id).prop("checked", true);
						$("#write_access_"+id).prop("checked", true);
						$.ajax({
							url: "script_manage_user_permissions.php",
							data: {
								user_id: id,
								user: "1"
							}
						});
					}
					else{
						$.ajax({
							url: "script_manage_user_permissions.php",
							data: {
								user_id: id,
								user: "0"
							}
						});
					}
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
					$sql2="SELECT * FROM `user_permissions` WHERE `uid`='$sanitized_uid'";
					$result2=mysql_query($sql2);
					while($row2=mysql_fetch_array($result2)){
						if($row2['user_permissions']==1){
							echo('<table>');
							
							echo('<tr>');
								echo('<th>Last, First</th>');
								echo('<th>Table Permissions</th>');
								echo('<th>User Permissions</th>');
							echo('</tr>');
							
							$sql="SELECT `users`.`uid`,`users`.`fname`,`users`.`lname`,`user_permissions`.`table_permissions`,`user_permissions`.`user_permissions` FROM `user_permissions` INNER JOIN `users` ON `user_permissions`.`uid`=`users`.`uid` ORDER BY `users`.`lname` ASC";
							$result=mysql_query($sql);
							while($row=mysql_fetch_array($result)){
								echo('<tr>');
									echo('<td style="text-align:left;">'.htmlentities($row['lname']).', '.htmlentities($row['fname']).'</td>');
									$table_permissions='';
									$user_permissions='';
									if($row['table_permissions']==1){
										$table_permissions=' checked';
									}
									if($row['user_permissions']==1){
										$user_permissions=' checked';
									}
									echo('<td><input type="checkbox" id="table_permissions_'.$row['uid'].'" class="permissions_checkbox" title="Toggle read access"'.$table_permissions.' /></td>');
									echo('<td><input type="checkbox" id="user_permissions_'.$row['uid'].'" class="permissions_checkbox" title="Toggle write access"'.$user_permissions.' /></td>');
								echo('</tr>');
							}
							echo('</table>');
						}
					}
				?>
			</div>
		</div>
	</center>
</body>
</html>