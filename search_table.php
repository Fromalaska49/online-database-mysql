<?php
require('connect.php');
require('session_handler.php');

$table_page=0;
$table_size=30;

$next_page = true;
$preceding_page = true;

if(isset($_GET['q'])){
	$query=strtolower(rawurldecode($_GET['q']));
	$sanitized_uid = (int) $_SESSION['uid'];
	$tmp_keyword = preg_split("/[ ]/",$query);
	$keyword = array();
	$index = 0;
	for($i=0;$i<count($tmp_keyword);$i++){
		if(strlen($tmp_keyword[$i])>1){
			$keyword[$index] = $tmp_keyword[$i];
			$index++;
		}
	}
	$num_keywords = count($keyword);
	for($derivative=$num_keywords;$derivative>0;$derivative--){
		$sql="SELECT DISTINCT * FROM `search_rank` INNER JOIN `keywords` ON `search_rank`.`result_id`=`keywords`.`result_id` INNER JOIN `table_permissions` ON `search_rank`.`table_id`=`table_permissions`.`table_id` WHERE `table_permissions`.`uid`='$sanitized_uid' AND `table_permissions`.`read_access`>0 AND (";
		for($i=0;$i<=$num_keywords-$derivative;$i++){
			$keyword_string=$keyword[$i];
			for($delta_i=1;$delta_i<$derivative;$delta_i++){
				$keyword_string.=' '.$keyword[$i+$delta_i];
			}
			$sanitized_keyword=mysql_real_escape_string($keyword_string);
			if($i!=0){
				$sql.=" OR `keywords`.`keyword`='$sanitized_keyword'";
			}
			else{
				$sql.=" `keywords`.`keyword`='$sanitized_keyword'";
			}
		}
		$sql.=")";
		//echo('$sql = '.$sql.'<br />');
		if(mysql_num_rows(mysql_query($sql))>0){
			//echo('$sql = '.$sql.'<br />');
			$derivative=-1;
		}
	}
	//$sql="SELECT DISTINCT * FROM `search_rank` INNER JOIN `keywords` ON `search_rank`.`result_id`=`keywords`.`result_id` WHERE `keywords`.`keyword` LIKE '".$escaped_keyword."'";
	/*
	for($i=1;$i<$num_keywords;$i++){
		$sql.=" AND keyword LIKE '%".mysql_real_escape_string($keyword[$i])."%'";
	}
	*/
	$sql.=" ORDER BY `r` DESC";
	//echo("\$sql = ".$sql."\n<br />");
	$total_results=mysql_num_rows(mysql_query($sql));
	if(isset($_GET['result_page'])&&$_GET['result_page']>0){
		$table_page=(int)$_GET['result_page'];
	}
	if(isset($_GET['result_size'])&&$_GET['result_size']>0){
		$table_size=(int)$_GET['result_size'];
	}
	$offset = $table_page * $table_size;
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
}
else{
	$sql="SELECT * FROM `search_rank`";
}
/*
echo("sql=".$sql."<br />");
echo(htmlentities('$_GET[\'q\']='.$_GET['q']));
echo(isset($_GET['q']));
*/
?>
<html>
<head>
	<?php include('head-includes.php'); ?>
	<title>
		<?php include('site_title.php'); ?><?php if(isset($_GET['table_name'])){echo('/'.htmlentities(rawurldecode($_GET['table_name'])));} ?>
	</title>
	<style type="text/css">
	.search_result{
		padding:5px;
		margin:10px 0px 10px 0px;
		border-radius:3px;
		cursor:text;
	}
	.search_results{
		width:800px;
	}
	.search_result_description{
		width:500px;
	}
	.search_result_title{
		text-decoration:none;
		color:blue;
	}
	.search_result_company{
		display:inline-block;
		cursor:pointer;
	}
	.search_result_company:hover{
		text-decoration:underline;
	}
	.search_result_field{
		display:inline-block;
		cursor:pointer;
	}
	.search_result_field:hover{
		text-decoration:underline;
	}
	</style>
	<script type="text/javascript">
		function previousResult(){
			var url = "search_table.php?q=<?php echo(rawurlencode(rawurldecode($_GET['q'])));if(isset($_GET['result_size'])){echo('&result_size='.$_GET['result_size']);} ?>&result_page=<?php echo(($table_page-1)); ?>";
			redirect(url);
		}
		function nextResult(){
			var url = "search_table.php?q=<?php echo(rawurlencode(rawurldecode($_GET['q'])));if(isset($_GET['result_size'])){echo('&result_size='.$_GET['result_size']);} ?>&result_page=<?php echo(($table_page+1)); ?>";
			redirect(url);
		}
		function goToPage(){
			var pageNumber = Number($("#page-number-input").val());
			if(pageNumber==""||isNaN(pageNumber)||pageNumber<1){
				pageNumber="<?php echo(($table_page+1)); ?>";
				$("#page-number-input").val(pageNumber);
			}
			pageNumber = pageNumber-1;
			var url = "search_table.php?q=<?php echo(rawurlencode(rawurldecode($_GET['q'])));if(isset($_GET['result_size'])){echo('&result_size='.$_GET['result_size']);} ?>&result_page="+pageNumber;
			redirect(url);
		}
		$(document).ready(function(){
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
</head>
<body>
	<?php include('head.php'); ?>
	<center>
		<div class="body">
			<?php include('sidebar.php'); ?>
			<div class="content">
				<div>
					<form action="search_table.php" method="GET">
						<input type="text" name="q" autocomplete="off" value="<?php if(isset($_GET['q'])){echo(htmlentities(rawurldecode($_GET['q'])));} ?>" />
						<input type="submit" value="Search" />
					</form>
				</div>
				<div id="search-results">
					<?php
					if(isset($_GET['q'])&&strlen($_GET['q'])>0){
					?>
						<div id="num_results" style="color:#888888;">
							<?php
							$first_result=$offset+1;
							$last_result=$offset+$table_size+1;
							$total_results=@mysql_num_rows(mysql_query($sql));
							if($last_result>$total_results){
								$last_result=$total_results;
							}
							if($total_results/$table_size>1){
								echo('Showing '.number_format($first_result,0,'.',',').' through '.number_format($last_result,0,'.',',').' of '.number_format($total_results,0,'.',',').' results');
							}
							else{
								if($total_results==0){
									echo('No results');
								}
								else if($total_results==1){
									echo('Only 1 result');
								}
								else if($total_results<4){
									echo('Just '.number_format($total_results,0,'.',',').' results');
								}
								else{
									echo(number_format($total_results,0,'.',',').' results');
								}
							}
							?>
							<br />
						</div>
						<?php
						$sql.=" LIMIT ".$offset.", ".$table_size;
						//$sql.=" LIMIT 100";
						
						$result = mysql_query($sql);
						echo('<div class="search_results">');
						while($row=@mysql_fetch_array($result)){
							//need to get table name
							$escaped_table_name = (int)$row['table_id'];
							$sql_table_name = "SELECT * FROM tables WHERE id='".$escaped_table_name."' LIMIT 1";
							$result_table_name = mysql_query($sql_table_name);
							$row_table_name=mysql_fetch_array($result_table_name);
							$table_name=$row_table_name['name'];
							unset($escaped_table_name);
							unset($sql_table_name);
							unset($result_table_name);
							unset($row_table_name);
							$encoded_table_name=rawurlencode($table_name);
							
							$result_table_page=0;
							$result_table_size=30;
							//$offset = $table_page * $table_size;
							$record_id = (int) $row['record_id'];
							$result_table_page = ceil($record_id / $result_table_size) - 1;
							echo('<div class="search_result">');
							$id=$row['record_id'].'-'.$row['field_id'];
							$edit = '';
							if($row['write_access']>0&&$row['field_name']!='id'){
								$edit = ' | <a href="edit_table.php?table_name='.$encoded_table_name.'&result_page='.$result_table_page.'&target_cell_id='.$id.'#td-'.$id.'"><div class="search_result_field">Edit</div></a>';
							}
							echo('<div class="search_result_title"><a href="view_table.php?table_name='.$encoded_table_name.'"><div class="search_result_company">'.htmlentities($row['table_name']).'</div></a> | <a href="view_table.php?table_name='.$encoded_table_name.'&result_page='.$result_table_page.'&target_cell_id='.$id.'#td-'.$id.'"><div class="search_result_field">'.htmlentities($row['field_name']).'</div></a>'.$edit.'</div>');
							$description = $row['description'];
							$max_description_len = 160;
							$description_len = strlen($description);
							if($description_len > $max_description_len){
								$substr_index = $description_len;
								for($i = 0; $i < $num_keywords; $i++){
									if(strpos($description, $keyword[$i])<$substr_index){
										$substr_index = strpos($description, $keyword[$i]);
									}
								}
								$substr_index -= $max_description_len/2;
								if($substr_index<0){
									$substr_index = 0;
									$description = substr($description, $substr_index, $max_description_len) . '...';
								}
								else if($substr_index+160 > $description_len){
									$substr_index = $description_len - $max_description_len;
									$description = '...' . substr($description, $substr_index, $max_description_len);
								}
								else{
									$description = '...' . substr($description, $substr_index, $max_description_len) . '...';
								}
								unset($substr_index);
							}
							unset($max_description_len);
							unset($description_len);
							$html_description = htmlentities($description);
							for($i = 0; $i < $num_keywords; $i++){
								$bold_keyword = '<b>'.$keyword[$i].'</b>';
								$html_description = str_replace($keyword[$i], $bold_keyword, $html_description);
							}
							unset($bold_keyword);
							echo('<div class="search_result_description">'.$html_description.'</div>');
							echo('</div>');
						}
						echo('</div>');
						
					}
					?>
					<?php 
					$hide_page_navigator=(!isset($_GET['q'])||$total_results/$table_size<1);
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
					<?php if($hide_page_navigator){ echo('-->'); } ?>
				
					<br />
					<br />
				</div>
			</div>
		</div>
	</center>
</body>
</html>