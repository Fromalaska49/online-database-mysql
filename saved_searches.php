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
	<style type="text/css">
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
			//Dynamically sets width of input.saved-search-item by accounting for the associated img and parentElement's width, padding, margin, and border
			$("input.saved-search-item").width($("input.saved-search-item").parent().width()-$("#img-0").outerWidth(true)-($("input.saved-search-item").outerWidth(true)-$("input.saved-search-item").width()));
			$("#sort-by").on("change",function(){
				//needs to submit new page load with active #sort-by option
				var url = "saved_searches.php?sort_by="+$("#sort-by").find(":selected").val();
				window.location.replace(url);
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
				<h3>
					Saved Searches
				</h3>
				<p>
				<?php
				$sort_by;
				if(isset($_GET['sort_by'])){
					$sort_by=(int) $_GET['sort_by'];
				}
				else{
					$sort_by=0;//default
				}
				$sanitized_sort_by;
				if($sort_by==0){
					$sanitized_sort_by=' ORDER BY `time` DESC';
				}
				else if($sort_by==1){
					$sanitized_sort_by='ORDER BY `time` ASC';
				}
				else if($sort_by==2){
					$sanitized_sort_by='ORDER BY `name` ASC';
				}
				else if($sort_by==3){
					$sanitized_sort_by='ORDER BY `name` DESC';
				}
				/*
				Smart sorting
				else if($sort_by==4){
					$sanitized_sort_by='ORDER BY `usage` ASC';
				}
				*/
				/*
				Age of save
				Utilization ratio
				Number of times accessed
				Amount of time accessed
				
				Decay algorithm
				*/
				else{
					$sort_by=0;
					$sanitized_sort_by=' ORDER BY `time` DESC';//unexpected error
				}
				?>
				<p>
					Sorting: 
				<select id="sort-by" name="sort-by">
					<option value="0"<?php if($sort_by==0){echo(' selected');} ?>>
						newest to oldest
					</option>
					<option value="1"<?php if($sort_by==1){echo(' selected');} ?>>
						oldest to newest
					</option>
					<option value="2"<?php if($sort_by==2){echo(' selected');} ?>>
						alphabetical
					</option>
					<option value="3"<?php if($sort_by==3){echo(' selected');} ?>>
						reverse alphabetical
					</option>
				</select>
				</p>
				</p>
				<?php
				$uid=$_SESSION['uid'];
				$sanitized_uid=(int)$uid;
				$sql="SELECT * FROM `saved_searches` WHERE `authuid`='$sanitized_uid'".$sanitized_sort_by;
				$result=mysql_query($sql);
				echo('<ul style="margin:0px;padding:1px;border-style:solid;border-width:1px;border-color:#555555;border-radius:3px;display:block;">');
				echo('<a href="advanced_search_table.php" class="saved-search-item"><li class="saved-search-item" title="Make a new search"><img src="images/icons/s/add-button.png" class="saved-search-item" /><p class="saved-search-item">New Search</p></li></a>');
				if(mysql_num_rows($result)>0){
					while($row=mysql_fetch_array($result)){
						echo('<a href="advanced_search_table_results.php'.$row['get_version'].'" class="saved-search-item"><li class="saved-search-item" title="'.htmlentities($row['english_version']).'"><p class="saved-search-item">'.htmlentities($row['name']).'</p></li></a>');
					}
				}
				else{
					echo('<li class="saved-search-item"><p class="saved-search-item">No saved searches</p></li>');
				}
				echo('</ul>');
				?>
				<br />
				<input type="button" value="Manage Saves" onclick="window.location='edit_saved_searches.php'" style="margin:0px;" />
				<br />
				<br />
			</div>
		</div>
	</center>
</body>
</html>