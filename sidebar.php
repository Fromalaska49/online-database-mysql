<div class="sidebar">
	<a href="tables.php" class="sidebar-link">
		Tables
	</a>
	<div>
		<?php
			$sanitized_uid=(int)$_SESSION['uid'];
			$table_permissions_result=mysql_query("SELECT * FROM `table_permissions` INNER JOIN `tables` ON `table_permissions`.`table_id`=`tables`.`id` WHERE `uid`='$sanitized_uid' ORDER BY `name` ASC");
			while($table_permissions_row=mysql_fetch_array($table_permissions_result)){
				if($table_permissions_row['read_access']==1){
					$sanitized_table_id=(int)$table_permissions_row['table_id'];
					$table_name=$table_permissions_row['name'];
					$html_table_name=htmlentities($table_name);
					$rawurlencoded_table_name=rawurlencode($table_name);
					echo('<a href="view_table.php?table_name='.$rawurlencoded_table_name.'" class="sidebar-link"><div style="padding:0px 0px 0px 10px;">'.$html_table_name.'</div></a>');
				}
			}
			unset($table_permissions_result);
			unset($table_permissions_row);
			unset($sanitized_table_id);
			unset($table_name);
			unset($html_table_name);
			unset($rawurlencoded_table_name);
		?>
	</div>
	<a href="search_table.php" class="sidebar-link">
		Search
	</a>
	<br />
	<a href="advanced_search_table.php" class="sidebar-link">
		Advanced Search
	</a>
	<br />
	<a href="saved_searches.php" class="sidebar-link">
		Saved Searches
	</a>
	<?php
	$sanitized_uid=(int)$_SESSION['uid'];
	if(mysql_num_rows(mysql_query("SELECT * FROM `user_permissions` WHERE `uid`='$sanitized_uid' AND `table_permissions`>0"))>0){
		echo('<br /><a href="manage_tables.php" class="sidebar-link">Manage Table Access</a>');
	}
	if(mysql_num_rows(mysql_query("SELECT * FROM `user_permissions` WHERE `uid`='$sanitized_uid' AND `user_permissions`>0"))>0){
		echo('<br /><a href="manage_users.php" class="sidebar-link">Manage Users</a>');
	}
	?>
</div>