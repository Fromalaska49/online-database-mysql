<?php
require('connect.php');
require('session_handler.php');
$decoded_table_name=rawurldecode($_GET['table_name']);
$escaped_table_name=mysql_real_escape_string($decoded_table_name);
$html_table_name=htmlentities($decoded_table_name);

$sql="SELECT * FROM `".$escaped_table_name."`";
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
	<title>
		<?php include('site_title.php'); ?><?php if(isset($_GET['table_name'])){echo('/'.$html_table_name);} ?>
	</title>
	<style type="text/css">
	</style>
	<script type="text/javascript">
		window.n=0;
		function add_query_selector(){
			var n=window.n;
			var selectAndOr = document.createElement("select");
			var optionAnd = document.createElement("option");
			var textAnd = document.createTextNode("and");
			var optionOr = document.createElement("option");
			var textOr = document.createTextNode("or");
			optionAnd.appendChild(textAnd);
			optionAnd.setAttribute("value","1");
			selectAndOr.appendChild(optionAnd);
			optionOr.appendChild(textOr);
			optionOr.setAttribute("value","2");
			selectAndOr.appendChild(optionOr);
			var selectColumn = document.createElement("select");
			function appendSelectColumn(text, value){
				var option1 = document.createElement("option");
				var text1 = document.createTextNode(text);
				option1.appendChild(text1);
				selectColumn.appendChild(option1);
				option1.setAttribute("value",value);
			}
			<?php
			if(isset($_GET['table_name'])){
				$result=mysql_query('SELECT * FROM `'.$escaped_table_name.'` LIMIT 1');
				$num_fields=mysql_num_fields($result);
			}
			for($index=0;$index<$num_fields;$index++){
				$field_name=mysql_fetch_field($result,$index)->name;
				echo('appendSelectColumn("'.htmlentities($field_name).'","'.rawurlencode($field_name).'");');
			}
			?>
			var selectSelector = document.createElement("select");
			function appendSelect(text,value){
				var option1 = document.createElement("option");
				var text1 = document.createTextNode(text);
				option1.appendChild(text1);
				selectSelector.appendChild(option1);
				option1.setAttribute("value",value);
			}
			appendSelect("is equal to","1");
			appendSelect("is not equal to","2");
			appendSelect("is less than","3");
			appendSelect("is greater than","4");
			appendSelect("is less than or equal to","5");
			appendSelect("is greater than or equal to","6");
			appendSelect("contains","7");
			appendSelect("does not contain","8");
			appendSelect("is blank","9");
			appendSelect("is not blank","10");
			appendSelect("begins with","11");
			appendSelect("does not begin with","12");
			appendSelect("ends with","13");
			appendSelect("does not end with","14");
			
			var inputText = document.createElement("input");
			inputText.setAttribute("type","text");
			inputText.setAttribute("name","text-"+n);
			selectAndOr.setAttribute("name","andor-"+n);
			selectColumn.setAttribute("name","column-"+n);
			selectSelector.setAttribute("name","selector-"+n);
			
			var querySelector = document.getElementById('query-selector-form');
			if(document.getElementById('query-selector-form').childNodes.length>14){
				querySelector.insertBefore(selectAndOr, querySelector.childNodes[querySelector.childNodes.length-11]);
				querySelector.insertBefore(document.createElement("br"), querySelector.childNodes[querySelector.childNodes.length-11]);
			}
			querySelector.insertBefore(selectColumn, querySelector.childNodes[querySelector.childNodes.length-11]);
			querySelector.insertBefore(selectSelector, querySelector.childNodes[querySelector.childNodes.length-11]);
			querySelector.insertBefore(inputText, querySelector.childNodes[querySelector.childNodes.length-11]);
			querySelector.insertBefore(document.createElement("br"), querySelector.childNodes[querySelector.childNodes.length-11]);
			
			window.n=n+1;
		}
	</script>
	<script>
		$(document).ready(function(){
			$("#table_name_selector").on("change",function(){
				$url="advanced_search_table.php?table_name="+$(this).val();
				window.location=$url;
			});
		});
	</script>
</head>
<body onload="add_query_selector()">
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<?php include('sidebar.php'); ?>
			<div class="content">
				<?php
				if(0<mysql_num_rows(mysql_query("SELECT * FROM `table_permissions` WHERE `uid`='$sanitized_uid' AND (`read_access`='1' OR `write_access`='1' OR `admin_access`='1')"))){
					echo('<div id="query-selector">');
					echo("\n");
					echo('<form id="query-selector-form" action="advanced_search_table_results.php" method="get">');
					echo("\n");
					echo('<select name="table_name" style="display:block;" id="table_name_selector">');
					echo('<option value="">Select a Table</option>');
					echo("\n");
					$sanitized_uid = (int) $_SESSION['uid'];
					echo("SELECT `name` FROM `tables` INNER JOIN `table_permissions` ON `tables`.`id`=`table_permissions`.`table_id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND `table_permissions`.`read_access`='1'");
					$tables_result=mysql_query("SELECT `name` FROM `tables` INNER JOIN `table_permissions` ON `tables`.`id`=`table_permissions`.`table_id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND `table_permissions`.`read_access`='1'");
					while($tables_row=mysql_fetch_array($tables_result)){
						if($tables_row['name']===$decoded_table_name){
							echo('<option value="'.$tables_row['name'].'" selected>'.$tables_row['name'].'</option>');
						}
						else{
							echo('<option value="'.$tables_row['name'].'">'.$tables_row['name'].'</option>');
						}
					}
					echo('</select>');
					echo("\n");
					if(isset($_GET['table_name'])&&$_GET['table_name']!=''){
						echo('<br />');
						echo("\n");
						echo('<p style="padding:0px;padding-left:10px;margin:0px;">Sort by:<select name="order_by">');
						$result=mysql_query('SELECT * FROM `'.$escaped_table_name.'` LIMIT 1');
						$num_fields=mysql_num_fields($result);
						for($index=0;$index<$num_fields;$index++){
							$field_name=mysql_fetch_field($result,$index)->name;
							echo('<option value="'.$field_name.'">'.htmlentities($field_name).'</option>');
						}
						echo('</select></p>');
						echo('<br />');
						echo("\n");
						echo('<input type="button" value="add filter" id="add-query-selector" class="button button-inactive" onclick="add_query_selector()" />');
						echo("\n");
						echo('<br />');
						echo("\n");
						echo('<input type="submit" value="Search" class="button button-inactive" />');
					}
					echo("\n");
					echo('</form>');
					echo('</div>');
				}
				else{
					echo('You do not yet have permission to access any tables.');
				}
				/*
				echo('<table>');
				echo('<tr>');
				for($index=0;$index<$num_fields;$index++){
					echo('<th style="position:relative;">'.mysql_fetch_field($result,$index)->name.'</th>');//$row[$index]);
				}
				echo('</tr>');
				while($row=mysql_fetch_array($result)){
					echo('<tr>');
					for($index=0;$index<$num_fields;$index++){
						echo('<td>'.$row[$index].'</td>');//;
					}
					echo('</tr>');
				}
				echo('</table>');
				*/
				?>
			</div>
		</div>
	</center>
</body>
</html>