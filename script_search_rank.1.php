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
		$maxlen=strlen($description);
		if($maxlen>122){
			//fit description into (128 character string) - 2*(elipse i.e. the "..." at the beginning and end)
			$maxlen=122;
		}
		$escaped_keyword=mysql_real_escape_string($description);
		$cropped_description=substr($description,0,$maxlen);
		$escaped_cropped_description = mysql_real_escape_string($cropped_description);
		$sql="INSERT INTO `search_rank` (`r`,`keyword`,`table_id`,`record`,`field`,`description`) VALUES ('".$escaped_r."','".$escaped_keyword."','".$escaped_table_id."','".$escaped_record_id."','".$index."','".$escaped_cropped_description."')";
		echo($sql.'<br />');
		mysql_query($sql);
		//end td
	}
	//end tr
}
//end table
?>


