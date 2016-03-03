
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
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="dragtable.css" />
	<style type="text/css">
		th{
		background-color:green;
		cursor:move;
		}
		.delete,td.rename,th{
		color:white;
		padding:10px;
		text-shadow:0px 0px 1px black;
		text-align:center;
		}
		.delete,td.rename,.plus{
		cursor:pointer;
		}
		td.rename{
		background-color:#FFAD03;
		}
		.delete{
		background-color:#CC0000;
		}
		.data_cell{
		color:#808080;
		text-shadow:0px 1px 0px #EEEEEE;
		text-align:center;
		background-color:#C0C0C0;
		}
		.data_cell_container{
		max-height:50px;
		overflow:hidden;
		display:inline-block;
		padding:10px;
		margin:0px;
		border-style:none;
		}
		.plus{
		background-color:#0077FF;
		color:white;
		text-shadow:0px 0px 1px black;
		text-align:left;
		padding:0px;
		min-width:100px;
		max-width:100px;
		width:100px;
		font-size:37px;
		}
		p.rename,p.done{
		width:100%;
		height:100%;
		margin:0px;
		}
	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script src="jquery.dragtable.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.defaultTable').dragtable();
		$('#footerTable').dragtable({excludeFooter:true});
		$('#onlyHeaderTable').dragtable({maxMovingRows:1});
		$('#persistTable').dragtable({persistState:'/someAjaxUrl'});
		$('#handlerTable').dragtable({dragHandle:'.some-handle'});
		$('#constrainTable').dragtable({dragaccept:'.accept'});
		/*
		$('#customPersistTable').dragtable({persistState: function(table) {
			table.el.find('th').each(function(i) {
				if(this.id != '') {table.sortOrder[this.id]=i;}
			});
			$.ajax({
				url: 'script_reorder_columns.php?',
				data: table.sortOrder
			});
			}
		});
		*/
		/*
		$('#localStorageTable').dragtable({
			persistState: function(table) {
				if (!window.sessionStorage) return;
				var ss = window.sessionStorage;
				table.el.find('th').each(function(i) {
				if(this.id != '') {table.sortOrder[this.id]=i;}
			});
				ss.setItem('tableorder',JSON.stringify(table.sortOrder));
			},
			restoreState: eval('(' + window.sessionStorage.getItem('tableorder') + ')')
		});
		*/
		$('#customPersistTable').dragtable({persistState: function(table) {
			var index=0;
			table.el.find('th').each(function() {
				if(this.id != '') {
					var res = $(this).attr('id').split("_");
					var id = Number(res[res.length-1]);
					table.sortOrder[$("#p_original_"+id).html()]=index;
					index++;
				}
			});
			/*
			$.ajax({
				url: "script_reorder_columns.php?table_name=<?php echo(rawurlencode(rawurldecode($_GET['table_name']))) ?>",
				data: {
				}
			});
			*/
			$("body").css("cursor","wait");
			$("a").css("cursor","wait");
			$("input").css("cursor","wait");
			$("textarea").css("cursor","wait");
			$(".draggable").css("cursor","wait");
			$(".rename").css("cursor","wait");
			$(".delete").css("cursor","wait");
			$url="script_reorder_columns.php?table_name=<?php echo(rawurlencode(rawurldecode($_GET['table_name']))) ?>";
			for (var i in table.sortOrder)
				$url+="&"+encodeURIComponent(i)+"="+encodeURIComponent(table.sortOrder[i]);
			window.location.replace($url);
		}
		});
		$(".delete").on("click",function(){
			var res = $(event.target).attr('id').split("_");
			var id = Number(res[res.length-1]);
			$url="script_delete_field.php?table_name=<?php echo(rawurlencode(rawurldecode($_GET['table_name']))); ?>&field_name="+encodeURIComponent($("#p_original_"+id).html()).replace(/'/g, "%27");
			window.location.href=$url;
		});
		$("p.rename").on("click",function(){
			var res = $(event.target).attr('id').split("_");
			var id = Number(res[res.length-1]);
			$("#rename_"+id).hide();
			$("#done_"+id).show();
			$("#p_"+id).hide();
			$("#thinput_"+id).css("width",($("#th_"+id).width()+21)+"px");
			$("#thinput_"+id).css("height",($("#th_"+id).height()+21)+"px");
			$("#th_"+id).css("padding","0px");
			$("#thinput_"+id).show();
			$("#thinput_"+id).focus();
		});
		$("p.done").on("click",function(){
			var res = $(event.target).attr('id').split("_");
			var id = Number(res[res.length-1]);
			$("#done_"+id).hide();
			$("#rename_"+id).show();
			$("#thinput_"+id).finish();
			$("#thinput_"+id).hide();
			var name = $("#thinput_"+id).val();
			$("#p_"+id).html(name);
			$("#p_"+id).finish();
			$("#p_"+id).fadeIn(200);
			$("#th_"+id).css("padding","10px");
			$.ajax({
				url: 'script_rename_field.php?',
				data: {
					table_name: "<?php echo(rawurldecode($_GET['table_name'])); ?>",
					field_name: $("#p_original_"+id).html(),
					new_field_name: $("#thinput_"+id).val(),
				},
			});
			$("#p_original_"+id).html($("#thinput_"+id).val());
			
		});
		$("th").on("keypress",function(){
			if(event.type="keydown"){
				if(event.which!=13){
					return;
				}
				else{
					//enter key pressed
					//permit the content to be toggled
					var res = $(event.target).attr('id').split("_");
					var id = Number(res[res.length-1]);
					$("#done_"+id).hide();
					$("#rename_"+id).show();
					$("#thinput_"+id).finish();
					$("#thinput_"+id).hide();
					var name = $("#thinput_"+id).val();
					$("#p_"+id).html(name);
					$("#p_"+id).finish();
					$("#p_"+id).fadeIn(200);
					$("#th_"+id).css("padding","10px");
					$.ajax({
						url: 'script_rename_field.php?',
						data: {
							table_name: "<?php echo(rawurldecode($_GET['table_name'])); ?>",
							field_name: $("#p_original_"+id).html(),
							new_field_name: $("#thinput_"+id).val(),
						},
					});
					$("#p_original_"+id).html($("#thinput_"+id).val());
				}
			}
		});
	});
	</script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#table_name_input").on("keypress",function(){
				if(event.type="keydown"){
					if(event.which==13){
						//enter key pressed
						if($("#table_name_input").val()==="<?php echo(htmlentities(rawurldecode($_GET['table_name']))); ?>"){
							alert('To rename something you have to provide a new name. Maybe you should ask for help.');
						}
						else{
							if(confirm("Are you sure you want to rename <?php echo(htmlentities(rawurldecode($_GET['table_name']))); ?> to "+$("#table_name_input").val()+"?")){
								/*
								$.ajax({
									url:"script_rename_table.php",
									data: {
										table_name: "<?php echo(rawurldecode($_GET['table_name'])); ?>",
										new_table_name: $("#table_name_input").val()
									}
								});
								*/
								//$url="manage_table.php?table_name="+encodeURIComponent($("#table_name_input").val());
								$url="script_rename_table.php?table_name=<?php echo(rawurldecode($_GET['table_name'])); ?>&new_table_name="+$("#table_name_input").val();
								window.location.replace($url);
							}
						}
					}
					else{
						return;
					}
				}
				var res = $(event.target).attr('id').split("-");
				var id = Number(res[res.length-1]);
				$("#img-"+id).hide();
				$("#input-"+id).hide();
				var name = $("#input-"+id).val();
				if(name==''){
					name='Untitled Search';
				}
				$("#p-"+id).html(name);
				$("#p-"+id).show();
			});
			$("#rename_table_button").on("click",function(){
				if($("#table_name_input").val()==="<?php echo(htmlentities(rawurldecode($_GET['table_name']))); ?>"){
					alert('To rename something you have to provide a new name. Maybe you should ask for help.');
				}
				else{
					if(confirm("Are you sure you want to rename <?php echo(htmlentities(rawurldecode($_GET['table_name']))); ?> to "+$("#table_name_input").val()+"?")){
						/*
						$.ajax({
							url:"script_rename_table.php",
							data: {
								table_name: "<?php echo(rawurldecode($_GET['table_name'])); ?>",
								new_table_name: $("#table_name_input").val()
							}
						});
						*/
						//$url="manage_table.php?table_name="+encodeURIComponent($("#table_name_input").val());
						$url="script_rename_table.php?table_name=<?php echo(rawurldecode($_GET['table_name'])); ?>&new_table_name="+$("#table_name_input").val();
						window.location.href=$url;
					}
				}
			});
			$("#delete_table_button").on("click",function(){
				if(confirm("Are you sure you want to delete the table <?php echo(htmlentities(rawurldecode($_GET['table_name']))); ?>?")){
					/*
					$.ajax({
						url:"script_delete_table.php",
						data: {
							table_name: "<?php echo(rawurldecode($_GET['table_name'])); ?>"
						}
					});
					*/
					window.location.href="script_delete_table.php?table_name=<?php echo(rawurldecode($_GET['table_name'])); ?>";
				}
			});
			$("#add_field_button").on("click",function(){
				$(this).hide();
				$("#add_field_form").fadeIn(200);
			});
			$("#add_field_cancel").on("click",function(){
				$("#add_field_form").hide();
				$("#add_field_button").fadeIn(200);
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
					$decoded_table_name = rawurldecode($_GET['table_name']);
					$table_name = $decoded_table_name;
					$sanitized_table_name = mysql_real_escape_string($table_name);
					$encoded_table_name = rawurlencode($table_name);
					$html_table_name = htmlentities($table_name);
					$sanitized_uid = (int) $_SESSION['uid'];
					$sql="SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `uid`='$sanitized_uid' AND `name`='$sanitized_table_name' ORDER BY `tables`.`name` ASC";
					$row=mysql_fetch_array(mysql_query($sql));
					if($row['admin_access']==1){
						$table_id = $row['table_id'];
						$sanitized_table_id = (int) $table_id;
						//$sub_result = mysql_query("SELECT * FROM tables WHERE id='$sanitized_table_id'");
						//$sub_row = mysql_fetch_array($sub_result);
						$table_name = $row['name'];
						echo('<div id="table_name_'.$table_id.'" style="font-size:50px;display:inline-block;cursor:pointer;" onclick="window.location.href=\'view_table.php?table_name='.rawurlencode($table_name).'\'">'.$html_table_name.'</div><br /><br />');
						
						//rename table
						echo('<input type="text" style="margin-left:0px;" id="table_name_input" value="'.$html_table_name.'" placeholder="Rename Table" autocomplete="off" />');
						echo('<input type="button" id="rename_table_button" value="Rename" />');
						echo('<input type="button" id="delete_table_button" value="Delete" />');
						echo('<input type="button" id="add_field_button" value="Add Field" />');
						echo('<div id="add_field_form" style="display:none;"><form action="script_add_field.php" method="GET" /><input type="text" name="table_name" style="display:none;" value="'.$table_name.'" /><input type="text" id="add_field_input" name="field_name" style="margin-left:0px;" placeholder="Field Name" /><input type="submit" id="add_field" value="Add Field" /><input type="button" id="add_field_cancel" value="Cancel" /></form></div>');
						
						
						echo('<br /><br />');
						
						//Modify Columns
						
						$sanitized_table_name=mysql_real_escape_string($table_name);
						$html_table_name=htmlentities($table_name);
						$sql="SELECT * FROM `".$sanitized_table_name."`";
						$sql.=" LIMIT 5";
						
						$num_fields;
						$result;
						$result=mysql_query($sql);
						$num_fields=mysql_num_fields($result);
						
						echo('<table id="customPersistTable" class="draggable">');
						
						echo('<tr>');
						//echo('<td class="noselect plus" style="border-bottom-style:none;" id="add_column_button_0"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;"></div></a></td>');
						$id_index = 0;
						for($index=0;$index<$num_fields;$index++){
							echo('<th class="noselect draggable" id="th_'.$index.'" style="min-width:100px;"><p id="p_'.$index.'">'.htmlentities(mysql_fetch_field($result,$index)->name).'</p><textarea id="thinput_'.$index.'" class="th_edit"  style="margin:0px;background-color:white;" autocomplete="off">'.htmlentities(mysql_fetch_field($result,$index)->name).'</textarea></th>');
							if((mysql_fetch_field($result,$index)->name)=='id'){
								$id_index = $index;
							}
						}
						echo('</tr>');
						echo('<tr style="display:none;">');
						//echo('<td class="noselect plus" style="border-bottom-style:none;" id="add_column_button_0"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;"></div></a></td>');
						for($index=0;$index<$num_fields;$index++){
							echo('<td class="noselect draggable" id="th_original_'.$index.'" style="min-width:100px;"><p id="p_original_'.$index.'">'.htmlentities(mysql_fetch_field($result,$index)->name).'</p></th>');
						}
						echo('</tr>');
						$rowid=0;
						echo('<tr>');
						//echo('<td class="noselect plus draggable" style="border-style:none solid none solid;" id="add_column_button_1"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:0px 0px 0px 0px;"><div style="position:relative;left:41px;">+</div></div></a></td>');
						for($index=0;$index<$num_fields;$index++){
							$cell_id=$rowid.'_'.$index;
							
							if($index!=$id_index){
								echo('<td id="td_'.$cell_id.'" class="rename noselect"><p class="rename" id="rename_'.$index.'">Rename</p><p class="done" style="display:none;" id="done_'.$index.'">Done</p></td>');
							}
							else{
								echo('<td id="td_'.$cell_id.'" class="noselect" style="background-color:#808080;cursor:default;color:white;padding:10px;text-shadow:0px 0px 1px black;text-align:center;" title="Not allowed" onclick="alert(\'You cannot rename this field.\');"><p id="rename_'.$index.'">Rename</p><p class="done" style="display:none;" id="done_'.$index.'">Done</p></td>');
							}
							
						}
						echo('</tr>');
						while($row = mysql_fetch_array($result)){
							$rowid++;
							echo('<tr>');
							//echo('<td class="noselect plus draggable" style="border-top-style:none;" id="add_column_button_2"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;"></div></a></td>');
							for($index=0;$index<$num_fields;$index++){
								$cell_id=$rowid.'_'.$index;
								echo('<td id="td_'.$cell_id.'" class="data_cell noselect"><div class="data_cell_container">'.$row[mysql_fetch_field($result,$index)->name].'</div></td>');
							}
							echo('</tr>');
						}
						$rowid++;
						echo('<tr>');
						//echo('<td class="noselect plus draggable" style="border-top-style:none;" id="add_column_button_2"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;"></div></a></td>');
						for($index=0;$index<$num_fields;$index++){
							$cell_id=$rowid.'_'.$index;
							if($index!=$id_index){
								echo('<td id="td_'.$cell_id.'" class="delete noselect" style="padding:0px;"><div id="'.$cell_id.'" style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;">Delete</div></td>');
							}
							else{
								echo('<td id="td_'.$cell_id.'" class="noselect" style="background-color:#808080;cursor:default;color:white;padding:0px;text-shadow:0px 0px 1px black;text-align:center;" title="Not allowed" onclick="alert(\'You cannot delete this field.\');"><div id="'.$cell_id.'" style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;">Delete</div></td>');
							}
						}
						echo('</tr>');
						echo('</table>');
						echo('<br />');
						
					}
					else{
						$table_id = $row['table_id'];
						echo('<div id="table_name_'.$table_id.'" style="font-size:50px;display:inline-block;cursor:pointer;" onclick="window.location.href=\'view_table.php?table_name='.rawurlencode($table_name).'\'">'.$html_table_name.'</div><br />');
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