<?php
require('connect.php');
require('session_handler.php');
$decoded_table_name=rawurldecode($_GET['table_name']);
$escaped_table_name=mysql_real_escape_string($decoded_table_name);
$html_table_name=htmlentities($decoded_table_name);

$sql="SELECT * FROM `".$escaped_table_name."`";
$english_sql="Results from <i>".$decoded_table_name."</i>";
/*







$html_sql;
creates #query-selector
for displaying the original search input
in the <div id="query-selector">childNodes[]</div>






*/
/////////
$search_get_vars="?table_name=".rawurlencode($decoded_table_name);
if(isset($_GET["column-0"])){
	$sql.=" WHERE";
	$english_sql.=" where";
	$num_params=0;
	for($i=0;$i<1000;$i++){
		if(isset($_GET['column-'.$i])){
			$num_params++;
		}
	}
	for($i=0;$i<$num_params;$i++){
		while(!isset($_GET['column-'.$i])&&$i<1000){
			$num_params++;
			$i++;
		}
		$andor="";
		$get_var_andor;
		if($i>0){
			$andor=(int) $_GET["andor-".$i];
			$get_var_andor='&andor-'.$i.'='.$_GET['andor-'.$i];
		}
		else{
			$get_var_andor='';
		}
		$search_get_vars.=$get_var_andor.'&column-'.$i.'='.$_GET["column-".$i].'&selector-'.$i.'='.$_GET["selector-".$i].'&text-'.$i.'='.$_GET['text-'.$i];
		unset($get_var_andor);
		
		$text=$_GET["text-".$i];
		$decoded_column_i=rawurldecode($_GET["column-".$i]);
		$escaped_field=mysql_real_escape_string($decoded_column_i);
		$english_field=" ".htmlentities($decoded_column_i);
		unset($decoded_column_i);
		$selector=(int) $_GET["selector-".$i];
		$text_i=$_GET["text-".$i];
		$escaped_text=mysql_real_escape_string($text_i);
		$english_andor;
		$english_selection;
		if($andor==1){
			$escaped_andor=" AND";
			$english_andor=" and";
		}
		else if($andor==2){
			$escaped_andor=" OR";
			$english_andor=" or";
		}
		else{
			$escaped_andor="";
			$english_andor="";
		}
		if($selector==1){
			$escaped_selection="='".$escaped_text."'";
			$english_selection=" is \"".htmlentities($text_i)."\"";
		}
		elseif($selector==2){
			$escaped_selection="<>'".$escaped_text."'";
			$english_selection=" is not \"".htmlentities($text_i)."\"";
		}
		elseif($selector==3){
			$escaped_selection="<'".$escaped_text."'";
			$english_selection=" is less than \"".htmlentities($text_i)."\"";
		}
		elseif($selector==4){
			$escaped_selection=">'".$escaped_text."'";
			$english_selection=" is greater than \"".htmlentities($text_i)."\"";
		}
		elseif($selector==5){
			$escaped_selection="<='".$escaped_text."'";
			$english_selection=" is less than or equal to \"".htmlentities($text_i)."\"";
		}
		elseif($selector==6){
			$escaped_selection=">='".$escaped_text."'";
			$english_selection=" is greater than or equal to \"".htmlentities($text_i)."\"";
		}
		elseif($selector==7){
			$escaped_selection=" LIKE '%".$escaped_text."%'";
			$english_selection=" contains \"".htmlentities($text_i)."\"";
		}
		elseif($selector==8){
			$escaped_selection=" NOT LIKE '%".$escaped_text."%'";
			$english_selection=" does not contain \"".htmlentities($text_i)."\"";
		}
		elseif($selector==9){
			$escaped_selection="=''";
			$english_selection=" is blank";
		}
		elseif($selector==10){
			$escaped_selection="<>''";
			$english_selection=" is not blank";
		}
		elseif($selector==11){
			$escaped_selection="='".$escaped_text."%'";
			$english_selection=" begins with \"".htmlentities($text_i)."\"";
		}
		elseif($selector==12){
			$escaped_selection="<>'".$escaped_text."%'";
			$english_selection=" does not begin with \"".htmlentities($text_i)."\"";
		}
		elseif($selector==13){
			$escaped_selection="='%".$escaped_text."'";
			$english_selection=" ends with \"".htmlentities($text_i)."\"";
		}
		elseif($selector==14){
			$escaped_selection="<>'%".$escaped_text."'";
			$english_selection=" does not end with \"".htmlentities($text_i)."\"";
		}
		else{
			//die();
		}
		unset($text_i);
		unset($selector);
		unset($escaped_text);
		$english_sql.="<br />".$english_andor.$english_field.$english_selection;
		$sql.=$escaped_andor." `".$escaped_field."`".$escaped_selection;
		unset($english_andor);
		unset($english_field);
		unset($english_selection);
		unset($escaped_field);
		unset($escaped_selection);
	}
}
else{
	//no filters in search

}
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
if(isset($_GET['order_by'])&&$_GET['order_by']!=''){
	$search_get_vars.='&order_by='.rawurlencode(rawurldecode($_GET['order_by']));
	$sql.=" ORDER BY `".mysql_real_escape_string(rawurldecode($_GET['order_by']))."`";
	$english_sql.=" sorted by ".rawurldecode($_GET['order_by']);
}
$sql.=" LIMIT ".$offset.", ".$table_size;
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
	<title>
		<?php include('site_title.php'); ?><?php if(isset($_GET['table_name'])){echo('/'.$html_table_name);} ?>
	</title>
	<script type="text/javascript">
		function saveSearch(){
			redirect='script_save_search.php<?php echo($search_get_vars); ?>';
			redirect+='&search-title='+encodeURIComponent($('#search-title').val());
			window.location=redirect;
		}
	</script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#save-as-button").on("click",function(){
				$(this).hide();
				$("#share-search-link").hide();
				$("#save-search-form").show();
			});
			$("#share-button").on("click",function(){
				$(this).hide();
				$("#save-search-form").hide();
				$("#share-search-link").show();
			});
			$("#download-button").on("click",function(){
				window.open("<?php echo('http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/script_download_csv.php'.$search_get_vars); ?>");
			});
			$("#search-title").on("keypress",function(){
				if(event.which==13){
					//enter key pressed
					saveSearch();
				}
				else{
					return;
				}
			});
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
	<?php
	$adjacent_page_url = 'advanced_search_table_results.php?table_name='.$_GET['table_name'].'&'.$search_get_vars;
	if(isset($_GET['result_size'])){
		$adjacent_page_url .= '&result_size='.$_GET['result_size'];
	}
	$adjacent_page_url .= "&result_page=";
	?>
	<script type="text/javascript">
		function previousResult(){
			var url = "<?php echo($adjacent_page_url.($table_page-1)); ?>";

			redirect(url);
		}
		function nextResult(){
			var url = "<?php echo($adjacent_page_url.($table_page+1)); ?>";
			redirect(url);
		}
		function goToPage(){
			var pageNumber = Number($("#page-number-input").val());
			if(pageNumber==""||isNaN(pageNumber)||pageNumber<1){
				pageNumber="<?php echo(($table_page+1)); ?>";
				$("#page-number-input").val(pageNumber);
			}
			pageNumber -= 1;
			var url = "<?php echo($adjacent_page_url); ?>"+pageNumber;
			redirect(url);
		}
	</script>
</head>
<body>
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<?php include('sidebar.php'); ?>
			<div class="content">
				<?php
				echo($english_sql."<br />");
				unset($english_sql);
				?>
				<div>
					<input type="button" value="New Search" class="button button-inactive noselect" onclick="window.location='advanced_search_table.php?table_name=<?php echo($_GET['table_name']); ?>'" />
					<input type="button" id="save-as-button" value="Save As..." class="button button-inactive noselect" onclick="this.style.display='none';document.getElementByID('search-title').style.display='inline';document.getElementByID('save-button').style.display='inline';" />
					<input type="button" id="share-button" class="button button-inactive noselect" value="Share" />
					<input type="button" id="download-button" class="button button-inactive noselect" value="Download" />
					<div id="save-search-form" style="display:none">
						<input type="text" id="search-title" name="title" placeholder="Untitled Search" />
						<input type="button" id="save-button" value="Save" class="button button-inactive noselect" onclick="saveSearch()" />
					</div>
					<div id="share-search-link" style="display:none">
						<p>
							The following link connects to these results. Copy and paste to share the link.
						</p>
						<p>
							<?php
							echo('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);//'advanced_search_table_results.php'.$search_get_vars);
							?>
						</p>
					</div>
					<!--
					<div id="query-selector">
						<form id="query-selector-form" action="advanced_search_table_results.php" method="get">
							<input type="text" value="<?php echo($_GET['table_name']); ?>" name="table_name" style="display:none;" />
							<input type="button" value="add filter" id="add-query-selector" class="button button-inactive" onclick="add_query_selector()" />
							<br />
							<input type="submit" value="Search" class="button button-inactive" />
						</form>
					</div>
					-->
				</div>
				<br />
				<?php
					$hide_page_navigator=!($preceding_page||$next_page);
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
				echo('<table>');
				echo('<tr>');
				
				$result=mysql_query($sql);
				$num_fields=mysql_num_fields($result);
				for($index=0;$index<$num_fields;$index++){
					echo('<th style="position:relative;">'.mysql_fetch_field($result,$index)->name.'</th>');
				}
				echo('</tr>');
				while($row=mysql_fetch_array($result)){
					echo('<tr>');
					for($index=0;$index<$num_fields;$index++){
						echo('<td>'.$row[$index].'</td>');
					}
					echo('</tr>');
				}
				echo('</table>');
				?>
				<br />
			</div>
		</div>
	</center>
</body>
</html>