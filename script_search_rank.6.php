<?php
require('connect.php');
require('session_handler.php');
mysql_query("TRUNCATE TABLE `search_rank`");
mysql_query("TRUNCATE TABLE `keywords`");
$tables_row_result=mysql_query("SELECT * FROM `tables`");
while($tables_row=mysql_fetch_array($tables_row_result)){
	$table_name=$tables_row['name'];
	$escaped_table_name=mysql_real_escape_string($table_name);
	$table_result = mysql_query("SELECT * FROM `".$escaped_table_name."`");
	$num_fields=mysql_num_fields($table_result);
	$table_id_result=mysql_query("SELECT * FROM `tables` WHERE `name`='$escaped_table_name'");
	$table_id_row = mysql_fetch_array($table_id_result);
	$escaped_table_id = (int) $table_id_row['id'];
	//begin table
	
	
	//th
	$field_name=array();
	$field_name[]=$num_fields;
	$escaped_field_name=array();
	$escaped_field_name[]=$num_fields;
	for($index=0;$index<$num_fields;$index++){
		$field_name[$index] = mysql_fetch_field($table_result,$index)->name;
		$escaped_field_name[$index] = mysql_real_escape_string($field_name[$index]);
	}
	
	//end th
	
	
	while($row=mysql_fetch_array($table_result)){
		//tr
		//$company_name=$row['Company'];
		//echo('$company_name='.$company_name.'<br />');
		//$escaped_company_name=mysql_real_escape_string($company_name);
		$escaped_record_id=(int)$row['id'];
		for($index=0;$index<$num_fields;$index++){
			//td
			if(strlen($row[$index])>6){
				$description=$row[$index];
				$description=strtolower($description);
				$maxlen=strlen($description);
				if($maxlen>122){
					//fit description into (128 character string) - 2*(elipse i.e. the "..." at the beginning and end)
					$maxlen=122;
				}
				$cropped_description=substr($description,0,$maxlen);
				$escaped_cropped_description = mysql_real_escape_string($cropped_description);
				$r = 2*log(strlen($description)/10);
				$r = floor(100*$r);
				$escaped_r = (int) $r;
				$sql="INSERT INTO `search_rank` (`r`,`table_id`,`record`,`field`,`field_name`,`table_name`,`description`) VALUES ('".$escaped_r."','".$escaped_table_id."','".$escaped_record_id."','".$index."','".$escaped_field_name[$index]."','".$escaped_table_name."','".$escaped_cropped_description."')";
				mysql_query($sql);
				$result_id_row = mysql_fetch_array(mysql_query("SELECT `result_id` FROM `search_rank` WHERE `table_id`='$escaped_table_id' AND `record`='$escaped_record_id' AND `field`='$index'"));
				$sanitized_result_id = (int)$result_id_row['result_id'];
				
				$keyword = preg_split("/[ ]/",$description);
				$num_keywords = count($keyword);
				$sql="INSERT INTO `keywords` (`result_id`,`keyword`) VALUES";
				for($derivative=0;$derivative<$num_keywords&&$derivative<7;$derivative++){
					for($i=0;$i<$num_keywords-$derivative;$i++){
						$keyword_string=$keyword[$i];
						for($delta_i=1;$delta_i<=$derivative;$delta_i++){
							$keyword_string.=' '.$keyword[$i+$delta_i];
						}
						$sanitized_keyword=mysql_real_escape_string($keyword_string);
						if($derivative!=0||$i!=0){
							$sql.=", ('$sanitized_result_id','$sanitized_keyword')";
						}
						else{
							$sql.=" ('$sanitized_result_id','$sanitized_keyword')";
						}
					}
				}
				mysql_query($sql);
			}
			//end td
		}
		//end tr
	}
	//end table
}
?>


