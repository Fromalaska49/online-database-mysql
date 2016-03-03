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
					$decoded_table_name = rawurldecode($_GET['table_name']);
					$table_name = $decoded_table_name;
					$sanitized_table_name=mysql_real_escape_string($table_name);
					$sanitized_uid=(int) $_SESSION['uid'];
					$table_permissions_row=mysql_fetch_array(mysql_query("SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND `tables`.`name`='$sanitized_table_name'"));
					if($table_permissions_row['admin_access'] == 1){
						//User has admin permissions for this table
						echo('<p>Change column order</p>');
						
						$sanitized_table_name=mysql_real_escape_string($table_name);
						$html_table_name=htmlentities($table_name);
						$sql="SELECT * FROM `".$sanitized_table_name."`";
						$sql.=" LIMIT 1";
						
						$num_fields;
						$result;
						$result=mysql_query($sql);
						$num_fields=mysql_num_fields($result);
						
						echo('<table id="customPersistTable" class="draggable">');
						
						echo('<tr>');
						echo('<td class="noselect plus" style="border-bottom-style:none;" id="add_column_button_0"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;"></div></a></td>');
						for($index=0;$index<$num_fields;$index++){
							echo('<th class="noselect draggable" id="th_'.$index.'" style="min-width:100px;"><p id="p_'.$index.'">'.htmlentities(mysql_fetch_field($result,$index)->name).'</p><textarea id="thinput_'.$index.'" class="th_edit"  style="margin:0px;background-color:white;" autocomplete="off">'.htmlentities(mysql_fetch_field($result,$index)->name).'</textarea></th>');
						}
						echo('</tr>');
						$rowid=0;
						echo('<tr>');
						echo('<td class="noselect plus draggable" style="border-style:none solid none solid;" id="add_column_button_1"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:0px 0px 0px 0px;"><div style="position:relative;left:41px;">+</div></div></a></td>');
						for($index=0;$index<$num_fields;$index++){
							$cell_id=$rowid.'_'.$index;
							echo('<td id="td_'.$cell_id.'" class="rename noselect"><p class="rename" id="rename_'.$index.'">Rename</p><p class="done" style="display:none;" id="done_'.$index.'">Done</p></td>');
						}
						echo('</tr>');
						$rowid++;
						echo('<tr>');
						echo('<td class="noselect plus draggable" style="border-top-style:none;" id="add_column_button_2"><a href="add_field.php?table_name='.rawurlencode($table_name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;"></div></a></td>');
						for($index=0;$index<$num_fields;$index++){
							$cell_id=$rowid.'_'.$index;
							echo('<td id="td_'.$cell_id.'" class="delete noselect" style="padding:0px;"><a href="script_delete_field.php?table_name='.rawurlencode($table_name).'&field_name='.rawurlencode(mysql_fetch_field($result,$index)->name).'" style="color:white;text-decoration:none;"><div style="width:100%;height:100%;margin:0px;padding:10px 0px 10px 0px;">Delete</div></a></td>');
						}
						echo('</tr>');
						echo('</table>');
						echo('<br />');
					}
				?>
			</div>
		</div>
	</center>
</body>
</html>