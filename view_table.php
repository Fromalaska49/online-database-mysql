<?php
require('connect.php');
require('session_handler.php');
$decoded_table_name=rawurldecode($_GET['table_name']);
$table_name=$decoded_table_name;
$sanitized_table_name=mysql_real_escape_string($table_name);
$html_table_name=htmlentities($table_name);
$sql="SELECT * FROM `".$sanitized_table_name."`";



//THIS IS WHERE PRIVILEGES ARE TO BE DETERMINED

$table_id_result=mysql_query("SELECT * FROM `tables` WHERE `name`='$sanitized_table_name'");
$table_id_row=mysql_fetch_array($table_id_result);
$table_id=$table_id_row['id'];
$sanitized_table_id=(int)$table_id;
$sanitized_uid=(int)$_SESSION['uid'];
$privileges_result=mysql_query("SELECT * FROM `table_permissions` WHERE `table_id`='$sanitized_table_id' AND `uid`='$sanitized_uid'");
$privileges_array=mysql_fetch_array($privileges_result);
$read_access=false;
$write_access=false;

$key_name=array_keys($_GET);
$num_keys=count($key_name);
$get_vars='?'.$key_name[0].'='.$_GET[$key_name[0]];
for($i=1;$i<$num_keys;$i++){
	$get_vars.='&'.$key_name[$i].'='.$_GET[$key_name[$i]];
}
if($privileges_array['write_access']==1){
	//header('Location: edit_table.php'.$get_vars);
	$write_access=true;
}
if($privileges_array['read_access']==1){
	$read_access=true;
}
/*
else{
	header('home.php');
}
*/



$table_page=0;
$table_size=30;
if(isset($_GET['result_page'])&&$_GET['result_page']>0){
	$table_page=(int)$_GET['result_page'];
}
if(isset($_GET['result_size'])&&$_GET['result_size']>0){
	$table_size=(int)$_GET['result_size'];
}
$offset = $table_page * $table_size;
$next_page = true;
$preceding_page = true;
if(!(@mysql_num_rows(mysql_query($sql.' LIMIT '.($offset-$table_size).', '.$table_size))>0)){
	$preceding_page = false;
}
if(!(@mysql_num_rows(mysql_query($sql.' LIMIT '.($offset+$table_size).', '.$table_size))>0)){
	$next_page = false;
}
if(!$preceding_page&&!$next_page){
	if($offset>0){
		$table_page = floor(mysql_num_rows(mysql_query($sql))/$table_size);
		$offset = $table_page*$table_size;
		$preceding_page = true;
	}
	else{
		$offset = 0;
	}
}
$sql.=" LIMIT ".$offset.", ".$table_size;
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
	<title>
		<?php include('site_title.php'); ?><?php if(isset($_GET['table_name'])){echo('/'.$html_table_name);} ?>
	</title>
	<?php
	$target_cell_id='';
	$target_record_id='';
	$target_field_id='';
	if(isset($_GET['target_cell_id'])){
		$target_cell_id=rawurldecode($_GET['target_cell_id']);
		$target_cell_array = explode('-',$target_cell_id);
		$target_record_id = $target_cell_array[0];
		$target_field_id = $target_cell_array[1];
		unset($target_cell_array);
	}
	?>
	<script type="text/javascript">
		function scrollToResult(){
			var options = {};
			var scrollLeft = $("#<?php echo($target_record_id); ?>-td-<?php echo($target_field_id); ?>").offset().left-(window.innerWidth+200-$("#<?php echo($target_record_id); ?>-td-<?php echo($target_field_id); ?>").width())/2;
			var scrollTop = $("#<?php echo($target_record_id); ?>-td-<?php echo($target_field_id); ?>").offset().top-(window.innerHeight+45-$("#<?php echo($target_record_id); ?>-td-<?php echo($target_field_id); ?>").height())/2;
			options["scrollTop"] = scrollTop;
			options["scrollLeft"] = scrollLeft;
			var distance=Math.pow((scrollTop*scrollTop+scrollLeft*scrollLeft),(1/2));
			duration=1000*(Math.log(distance+1097)-7);//1097 ~ e^7
			$('html, body').animate(options, duration);
		}
		$(document).ready(function(){
			<?php
			if(isset($_GET['target_cell_id'])){
				echo('scrollToResult();');
			}
			?>
			$("#page-number").on("mouseenter",function(){
				$("#page-number-display").hide();
				$("#page-number-input").show();
			});
			$("#page-number").on("mouseleave",function(){
				updatePageNumber();
				$("#page-number-input").hide();
				$("#page-number-display").show();
				$("#page-number-input").blur();
			});
			$("#page-number-container").on("mouseleave",function(){
				$("#go-page-button").hide();
				$("#next-page-button").show();
				$("#page-number-input").val("<?php echo(($table_page+1)); ?>");
				$("#page-number-display").html("<?php echo(($table_page+1)); ?>");
			});
			$("#page-number-input").on("focus",function(){
				$("#next-page-button").hide();
				$("#go-page-button").show();
			});
			$("#page-number-input").on("keypress",function(){
				if(event.type="keydown"){
					if(event.which==13){
						//enter key pressed
						updatePageNumber();
						$("#page-number-input").hide();
						$("#page-number-display").show();
						$("#page-number-input").blur();
						goToPage();
					}
				}
			});
			function updatePageNumber(){
				var pageNumber = Number($("#page-number-input").val());
				if(pageNumber==""||isNaN(pageNumber)||pageNumber<1){
					pageNumber="<?php echo(($table_page+1)); ?>";
					$("#page-number-input").val(pageNumber);
				}
				$("#page-number-display").html(pageNumber);
			}
		});
	</script>
	
	<script type="text/javascript">
		function previousResult(){
			var url = "edit_table.php?table_name=<?php echo($_GET['table_name']);if(isset($_GET['result_size'])){echo('&result_size='.$_GET['result_size']);} ?>&result_page=<?php echo(($table_page-1)); ?>";
			redirect(url);
		}
		function nextResult(){
			var url = "edit_table.php?table_name=<?php echo($_GET['table_name']);if(isset($_GET['result_size'])){echo('&result_size='.$_GET['result_size']);} ?>&result_page=<?php echo(($table_page+1)); ?>";
			redirect(url);
		}
		function goToPage(){
			var pageNumber = Number($("#page-number-input").val());
			if(pageNumber==""||isNaN(pageNumber)||pageNumber<1){
				pageNumber="<?php echo(($table_page+1)); ?>";
				$("#page-number-input").val(pageNumber);
			}
			pageNumber = pageNumber-1;
			var url = "edit_table.php?table_name=<?php echo($_GET['table_name']);if(isset($_GET['result_size'])){echo('&result_size='.$_GET['result_size']);} ?>&result_page="+pageNumber;
			redirect(url);
		}
	</script>
	<style type="text/css">
		.td-edit,.th-edit{
		display:none;
		width:100%;
		height:100%;
		margin:0px;
		border-style:none;
		border-radius:0px;
		padding:10px;
		resize:none;
		background-color:#CCCCCC;
		text-shadow:0px 1px 1px white;
		text-align:center;
		}
		tr{
			padding:0px;
			margin:0px;
		}
		td{
		position:relative;
		padding:0px;
		min-width:100px;
		}
		th{
		position:relative;
		padding:0px;
		min-width:100px;
		}
		textarea{
			margin:0px;
			padding:0px;
			border-style:none;
			display:inline-block;
		}
		table{
			cursor:text;
		}
	</style>
</head>
<body>
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<?php include('sidebar.php'); ?>
			<div class="content">
				<?php
				if(isset($_GET['target_cell_id'])){
					echo('<div style="background-color:black;color:white;opacity:0.7;border-radius:100px;padding:5px;border-style:solid;border-color:white;border-width:2px;box-shadow:0px 1px 5px black;display:inline-block;position:fixed;top:55px;right:10px;cursor:pointer;z-index:5;" onclick="scrollToResult();">Show Result</div>');
				}
				echo('<div style="font-size:50px;">'.htmlentities(rawurldecode($_GET['table_name'])).'</div>'); 
				$sanitized_uid=(int) $_SESSION['uid'];
				$table_permissions_row=mysql_fetch_array(mysql_query("SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `uid`='$sanitized_uid' AND `name`='$sanitized_table_name' ORDER BY `tables`.`name` ASC"));
				if($table_permissions_row['write_access']==1){
					echo('<div style="display:inline-block;padding:10px;"><a href="edit_table.php?table_name='.rawurlencode($decoded_table_name).'">Edit</a></div>');
				}
				if($table_permissions_row['admin_access']==1){
					echo('<div style="display:inline-block;padding:10px;"><a href="manage_table.php?table_name='.rawurlencode($decoded_table_name).'">Manage</a></div>');
				}
				echo('<div style="display:inline-block;padding:10px;"><a href="http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/script_download_csv.php?table_name='.rawurlencode(rawurldecode($_GET['table_name'])).'">Download</a></div>');
				echo('<br />');
				$hide_page_navigator=!$preceding_page&&!$next_page;
				if($hide_page_navigator){ echo('<!--'); }
				?>
				<div id="page-number-container" style="display:inline-block;border-style:solid;border-color:#999999;border-width:1px;border-radius:3px;">
					<?php
					if($preceding_page){
						echo('<input type="button" class="arrow-button" title="previous page" value=" &lt " id="previous-page-button" onclick="previousResult()" />');
					}
					else{
						echo('<div id="previous-page-button" style="width:10px;margin:0px;padding:0px;display:inline-block;"></div>');
					}
					?>
					<div id="page-number" style="display:inline-block;">Page <div id="page-number-display" style="display:inline-block;width:50px;"><?php echo($table_page+1); ?></div><input id="page-number-input" type="text" style="display:none;height:30px;width:50px;margin:0px;" value="<?php echo($table_page+1); ?>" autocomplete="off" /></div>
					<?php
					if($next_page){
						echo('<input type="button" title="next page" value=" &gt " id="next-page-button" onclick="nextResult()" />');
					}
					else{
						echo('<div id="next-page-button" style="width:10px;margin:0px;padding:0px;display:inline-block;"></div>');
					}
					?>
					<input type="button" title="Go to page" value="go" id="go-page-button" onclick="goToPage()" style="display:none;" />
				</div>
				<br />
				<br />
				<?php
				if($hide_page_navigator){ echo('-->'); } 
				?>
				<?php
				$num_fields;
				if(isset($_GET['table_name'])){
					$result;
					/*
					if(isset($_POST["column-0"])){
						$result=mysql_query($sql);
					}
					else{
						$result=mysql_query("SELECT * FROM `".$escaped_table_name."` ORDER BY `company` ASC LIMIT 100");
					}
					*/
					$result=mysql_query($sql);
					$num_fields=mysql_num_fields($result);
				}
				else{
					echo("\$_GET[\"table_name\"] is not set<br />");
				}
				?>
				<div id="query-selector">
				</div>
				<?php
				if($read_access){
					echo('<table id="thetable">');
					echo('<tr>');
					for($index=0;$index<$num_fields;$index++){
						echo('<th class="noselect" id="th-'.$index.'"><p id="p-'.$index.'" style="padding:10px;">'.htmlentities(mysql_fetch_field($result,$index)->name).'</p></th>');
					}
					echo('</tr>');
					//$record_id=$offset;
					while($row=mysql_fetch_array($result)){
						echo('<tr>');
						$record_id=$row['id'];
						for($field_id=0;$field_id<$num_fields;$field_id++){
							$style='';
							if($field_id==$target_field_id&&$record_id==$target_record_id){
								$style='box-shadow:0px 1px 20px 0px black;z-index:2;';
							}
							echo('<td id="'.$record_id.'-td-'.$field_id.'" class="noselect" style="'.$style.'"><p id="'.$record_id.'-p-'.$field_id.'" style="padding:10px;">'.$row[$field_id].'</p></td>');
						}
						echo('</td>');
					}
					echo('</table>');
				}
				else{
					//no read access
					echo('<p>You do not have permission to access this data.</p>');
					echo('<p>Contact the system administrator for more information.</p>');
				}
				?>
				<br />
			</div>
		</div>
	</center>
</body>
</html>