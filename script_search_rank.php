<?php
require('connect.php');
require('session_handler.php');
$decoded_table_name=rawurldecode($_GET['table_name']);
$escaped_table_name=mysql_real_escape_string($decoded_table_name);
$html_table_name=htmlentities($decoded_table_name);

$table_result = mysql_query("SELECT * FROM `".$escaped_table_name."`");
$num_fields=mysql_num_fields($table_result);
$table_id_result=mysql_query("SELECT * FROM `tables` WHERE name='$escaped_table_name'");
$table_id_row = mysql_fetch_array($table_id_result);
$escaped_table_id = (int) $table_id_row['id'];
//begin table


//th
/*
for($index=0;$index<$num_fields;$index++){
	echo('<th style="position:relative;">'.mysql_fetch_field($result,$index)->name.'</th>');
}
*/
//end th


$escaped_r=1;
while($row=mysql_fetch_array($table_result)){
	//tr
	$escaped_record_id=(int)$row['id'];
	for($index=0;$index<$num_fields;$index++){
		//td
		$description=$row[$index];
		$keyword=preg_split("/[ ]/",$row[$index]);
		$num_keywords=count($keyword);
		print_r($keyword);
		$escaped_index=(int)$index;
		for($i=0;$i<$num_keywords;$i++){
			$escaped_keyword=mysql_real_escape_string($keyword[$i]);
			$keyword_position=strpos($description,$keyword);
			$keyword_length=strlen($keyword);
			if($keyword_length>(128-6)){
				$keyword_length=122;
			}
			$maxlen=strlen($description);
			if($maxlen>122){
				//fit description into (128 character string) - 2*(elipse i.e. the "..." at the beginning and end)
				$maxlen=122;
			}
			//set the beginning of the description to one half of the maximum length before the middle of the keyword's position
			$description_position_initial=$keyword_position-($keyword_length-$maxlen)/2;
			if($description_position_initial<0){
				//it is not possible to have a negative index
				$description_position_intial=0;
			}
			$description_position_final=$description_position_initial+$maxlen;
			if($description_position_final>$maxlen){
				//if the end of the description exceeds the maximum length, set the end to the last possible index. Also, shift the beginning of the description by the smae amount.
				$description_position_initial-=($description_position_final-$maxlen);
				$description_position_final=$maxlen-1;
				if($description_position_initial<0){
					//If the beginning of the description has been shifted to a negative value, then it must be reset to 0 to prevent an invalid index
					$description_position_initial=0;
				}
			}
			$cropped_description = substr($description,$description_position_initial,$description_position_final);
			/*
			//split on spaces to eliminate partial words at the beginning and end of the description
			preg_split('/\s+/',$cropped_description);
			*/
			if(!($description_position_initial==0)){
				$cropped_description = '...'.$cropped_description;
			}
			if(!($description_position_final==$maxlen-1)){
				$cropped_description = $cropped_description.'...';
			}
			$escaped_cropped_description = mysql_real_escape_string($cropped_description);
			$sql="INSERT INTO `search_rank` (`r`,`keyword`,`table_id`,`record`,`field`,`description`) VALUES ('".$escaped_r."','".$escaped_keyword."','".$escaped_table_id."','".$escaped_record_id."','".$escaped_index."','".$escaped_cropped_description."')";
			echo($sql.'<br />');
			mysql_query($sql);
		}
		//end td
	}
	//end tr
}
//end table
?>


