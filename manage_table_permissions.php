
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
		th{
		color:white;
		padding:10px;
		text-align:center;
		}
	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script src="jquery.dragtable.js"></script>
	<?php
	$table_name=rawurldecode($_GET['table_name']);
	$encoded_table_name=rawurlencode($table_name);
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".permissions_checkbox").on("click",function(){
				var res = $(event.target).attr('id').split("_");
				var id = Number(res[res.length-1]);
				if(res[0]=="admin"){
					if($(this).prop("checked")==true){
						$("#read_access_"+id).prop("checked", true);
						$("#write_access_"+id).prop("checked", true);
						$.ajax({
							url: "script_manage_table_permissions.php",
							data: {
								table_name: "<?php echo($encoded_table_name); ?>",
								uid: id,
								read_access: "1",
								write_access: "1",
								admin_access: "1"
							}
						});
					}
					else{
						$.ajax({
							url: "script_manage_table_permissions.php",
							data: {
								table_name: "<?php echo($encoded_table_name); ?>",
								uid: id,
								read_access: "1",
								write_access: "1",
								admin_access: "0"
							}
						});
					}
				}
				else if(res[0]=="write"){
					if($(this).prop("checked")==true){
						$("#read_access_"+id).prop("checked", true);
						$.ajax({
							url: "script_manage_table_permissions.php",
							data: {
								table_name: "<?php echo($encoded_table_name); ?>",
								uid: id,
								read_access: "1",
								write_access: "1",
								admin_access: "0"
							}
						});
					}
					else{
						$.ajax({
							url: "script_manage_table_permissions.php",
							data: {
								table_name: "<?php echo($encoded_table_name); ?>",
								uid: id,
								read_access: "1",
								write_access: "0",
								admin_access: "0"
							}
						});
					}
					$("#admin_access_"+id).prop("checked", false);
				}
				else if(res[0]=="read"){
					if($(this).prop("checked")==true){
						$.ajax({
							url: "script_manage_table_permissions.php",
							data: {
								table_name: "<?php echo($encoded_table_name); ?>",
								uid: id,
								read_access: "1",
								write_access: "0",
								admin_access: "0"
							}
						});
					}
					else{
						$.ajax({
							url: "script_manage_table_permissions.php",
							data: {
								table_name: "<?php echo($encoded_table_name); ?>",
								uid: id,
								read_access: "0",
								write_access: "0",
								admin_access: "0"
							}
						});
					}
					$("#write_access_"+id).prop("checked", false);
					$("#admin_access_"+id).prop("checked", false);
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
					$sanitized_uid = (int) $_SESSION['uid'];
					$sql="SELECT `table_permissions` FROM `user_permissions` WHERE `uid`='$sanitized_uid'";
					$row=mysql_fetch_array(mysql_query($sql));
					unset($sql);
					if($row['table_permissions']==1){
						unset($row);
						//$sub_result = mysql_query("SELECT * FROM tables WHERE id='$sanitized_table_id'");
						//$sub_row = mysql_fetch_array($sub_result);
						$table_name = rawurldecode($_GET['table_name']);
						$html_table_name = htmlentities($table_name);
						echo('<div style="font-size:50px;display:inline-block;cursor:pointer;" onclick="window.location.href=\'view_table.php?table_name='.rawurlencode($table_name).'\'">'.$html_table_name.'</div><br /><br />');
						
						$sanitized_table_name = mysql_real_escape_string($table_name);
						echo('<p>Check the box to grant user permissions.</p>');
						echo('<table>');
						
						echo('<tr>');
							echo('<th>Last, First</th>');
							echo('<th>Read</th>');
							echo('<th>Write</th>');
							echo('<th>Admin</th>');
						echo('</tr>');
						
						$sql="SELECT `users`.`uid`,`users`.`fname`,`users`.`lname`,`table_permissions`.`read_access`,`table_permissions`.`write_access`,`table_permissions`.`admin_access` FROM `table_permissions` INNER JOIN `users` ON `table_permissions`.`uid`=`users`.`uid` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `tables`.`name`='$sanitized_table_name' ORDER BY `users`.`lname` ASC";
						$result=mysql_query($sql);
						while($row=mysql_fetch_array($result)){
							echo('<tr>');
								echo('<td style="text-align:left;">'.htmlentities($row['lname']).', '.htmlentities($row['fname']).'</td>');
								$admin_access='';
								$write_access='';
								$read_access='';
								if($row['admin_access']==1){
									$admin_access=' checked';
									$write_access=' checked';
									$read_access=' checked';
								}
								else if($row['write_access']==1){
									$write_access=' checked';
									$read_access=' checked';
								}
								else if($row['read_access']==1){
									$read_access=' checked';
								}
								echo('<td><input type="checkbox" id="read_access_'.$row['uid'].'" class="permissions_checkbox" title="Toggle read access"'.$read_access.' /></td>');
								echo('<td><input type="checkbox" id="write_access_'.$row['uid'].'" class="permissions_checkbox" title="Toggle write access"'.$write_access.' /></td>');
								echo('<td><input type="checkbox" id="admin_access_'.$row['uid'].'" class="permissions_checkbox" title="Toggle admin access"'.$admin_access.' /></td>');
							echo('</tr>');
						}
						echo('</table>');
						echo('<br />');
					}
					else{
						echo('<div style="font-size:50px;display:inline-block;cursor:pointer;" onclick="window.location.href=\'view_table.php?table_name='.rawurlencode($table_name).'\'">'.$html_table_name.'</div><br />');
						echo('<div style="display:inline-block;margin:20px;padding:20px;border-style:solid;border-color:#AA0000;border-width:1px;border-radius:3px;background-color:#FF3333;box-shadow:0px 1px 10px -6px black;color:white;text-shadow:0px -1px 1px #AA0000;">Access denied</div><div>You do not have permission to manage this table.</div>');
					}
					unset($decoded_table_name);
					unset($table_name);
					unset($sanitized_table_name);
					unset($encoded_table_name);
					unset($html_table_name);
				?>
			</div>
		</div>
	</center>
</body>
</html>