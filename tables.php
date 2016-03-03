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
	<script type="text/javascript">
		$(document).ready(function(){
			//Dynamically sets width of input.saved-search-item by accounting for the associated img and parentElement's width, padding, margin, and border
			$("input.saved-search-item").width($("input.saved-search-item").parent().width()-$("#img-0").outerWidth(true)-($("input.saved-search-item").outerWidth(true)-$("input.saved-search-item").width()));
			$("#permission").on("change",function(){
				//needs to submit new page load with active #sort-by option
				var url = "tables.php?permission="+$("#permission").find(":selected").val();
				window.location.replace(url);
			});
			$("li").on("mouseenter",function(){
				var res = $(event.target).attr('id').split("-");
				var id = Number(res[res.length-1]);
				$("#p-"+id).hide();
				$("#img-"+id).fadeIn(200);
				$("#input-"+id).fadeIn(200);
			});
			$("li.saved-search-item").on("mouseleave",function(){
				var res = $(event.target).attr('id').split("-");
				var id = Number(res[res.length-1]);
				$("#img-"+id).finish();
				$("#img-"+id).hide();
				$("#input-"+id).finish();
				$("#input-"+id).hide();
				$("#input-"+id).blur();
				var name = $("#input-"+id).val();
				if(name==''){
					name='Untitled Search';
				}
				$("#p-"+id).html(name);
				$("#p-"+id).finish();
				$("#p-"+id).fadeIn(200);
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
					$permission;
					if(isset($_GET['permission'])){
						$permission=(int)$_GET['permission'];
					}
					else{
						$permission=0;//default
					}
					$sanitized_permission;
					if($permission==0){
						$sanitized_permission="`read_access`=1";
					}
					else if($permission==1){
						$sanitized_permission="`write_access`=1";
					}
					else if($permission==2){
						$sanitized_permission="`admin_access`=1";
					}
					else if($permission==3){
						$sanitized_permission="`read_access`=0 AND `write_access`=0 AND `admin_access`=0";
					}
					else{
						$permission=0;
						$sanitized_permission="`read_access`=1";//unexpected error
					}
					
					
					$sanitized_uid=(int)$_SESSION['uid'];
					$sql="SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `uid`=$sanitized_uid LIMIT 50";
					$table_permissions_result=mysql_query($sql);
					$admin_tables=array();
					$write_tables=array();
					$read_tables=array();
					$void_tables=array();
					$admin_index=0;
					$write_index=0;
					$read_index=0;
					$void_index=0;
					while($table_permissions_row=mysql_fetch_array($table_permissions_result)){
						if($table_permissions_row['admin_access']==1){
							//user has administrative access
							$admin_tables[$admin_index]=$table_permissions_row['name'];
							$admin_index++;
						}
						if($table_permissions_row['write_access']==1){
							//user has write access
							$write_tables[$write_index]=$table_permissions_row['name'];
							$write_index++;
						}
						if($table_permissions_row['read_access']==1){
							//user has read access
							$read_tables[$read_index]=$table_permissions_row['name'];
							$read_index++;
						}
						if($table_permissions_row['admin_access']!=1&&$table_permissions_row['write_access']!=1&&$table_permissions_row['read_access']!=1){
							//user has no permission to access this table
							$void_tables[$void_index]=$table_permissions_row['name'];
							$void_index++;
						}
						else{
							//unexpected error
						}
					}
				?>
				
				
				
				
				
				
				<!--
				<p>
					Showing tables where you have 
				<select id="permission" name="permission">
					<option value="0"<?php if($permission==0){echo(' selected');} ?>>
						read
					</option>
					<option value="1"<?php if($permission==1){echo(' selected');} ?>>
						write
					</option>
					<option value="2"<?php if($permission==2){echo(' selected');} ?>>
						admin
					</option>
					<option value="3"<?php if($permission==3){echo(' selected');} ?>>
						no
					</option>
				</select>
					 access.
				</p>
				-->
				<?php
				$uid=$_SESSION['uid'];
				$sanitized_uid=(int)$uid;
				$sql="SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `uid`=$sanitized_uid";
				$result=mysql_query($sql);
				echo('<ul style="margin:0px;padding:1px;border-style:solid;border-width:1px;border-color:#555555;border-radius:3px;display:block;">');
				echo('<a href="new_table_type.php" class="saved-search-item"><li class="saved-search-item" title="New search"><img src="images/icons/s/add-button.png" class="saved-search-item" /><p class="saved-search-item">New Table</p></li></a>');
				while($row=mysql_fetch_array($result)){
					if($row['admin_access']==1||$row['write_access']==1||$row['read_access']==1){
						echo('<a href="view_table.php?table_name='.rawurlencode($row['name']).'" class="saved-search-item"><li class="saved-search-item" title="'.htmlentities($row['name']).'"><img src="images/icons/s/remove-button.png" class="saved-search-item" id="img-'.$row['id'].'" style="display:none;" title="remove" /><p class="saved-search-item">'.htmlentities($row['name']).'</p></li></a>');
					}
				}
				echo('</ul>');
				?>
				<br />
				
				
				
				
				
				<!--
				<div>
					<?php
						$num_admin_tables=count($admin_tables);
						if($num_admin_tables==0){
							//no admin tables
							echo('<h3>You are not an adminstrator for any table.</h3>');
						}
						else{
							echo('<h3>You are an adminstrator for:</h3>');
							echo('<ul>');
							for($i=0;$i<$num_admin_tables;$i++){
								echo('<li>'.$admin_tables[$i].'</li>');
							}
							echo('</ul>');
						}
					?>
				</div>
				<div>
					<?php
						$num_write_tables=count($write_tables);
						if($num_write_tables==0){
							//no write tables
							echo('<h3>You do not have permission to edit any table.</h3>');
						}
						else{
							echo('<h3>You have permission to edit:</h3>');
							echo('<ul>');
							for($i=0;$i<$num_write_tables;$i++){
								echo('<li>'.$write_tables[$i].'</li>');
							}
							echo('</ul>');
						}
					?>
				</div>
				<div>
					<?php
						$num_read_tables=count($read_tables);
						if($num_read_tables==0){
							//no read tables
							echo('<h3>You do not have permission to view any table.</h3>');
						}
						else{
							echo('<h3>You have permission to view:</h3>');
							echo('<ul>');
							for($i=0;$i<$num_read_tables;$i++){
								echo('<li>'.$read_tables[$i].'</li>');
							}
							echo('</ul>');
						}
					?>
				</div>
				<div>
					<?php
						$num_void_tables=count($void_tables);
						if($num_void_tables==0){
							//no void tables
						}
						else{
							echo('<h3>You do not have permission to access:</h3>');
							echo('<ul>');
							for($i=0;$i<$num_void_tables;$i++){
								echo('<li>'.$void_tables[$i].'</li>');
							}
							echo('</ul>');
						}
					?>
				</div>
				-->
			</div>
		</div>
	</center>
</body>
</html>