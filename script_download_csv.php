<?php
require('connect.php');
require('session_handler.php');
$decoded_table_name=rawurldecode($_GET['table_name']);
$escaped_table_name=mysql_real_escape_string($decoded_table_name);
$html_table_name=htmlentities($decoded_table_name);

$sql="SELECT * FROM `".$escaped_table_name."`";
if(isset($_GET["column-0"])){
	$sql.=" WHERE";
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
		if($i>0){
			$andor=(int) $_GET["andor-".$i];
		}
		
		$text=$_GET["text-".$i];
		$decoded_column_i=rawurldecode($_GET["column-".$i]);
		$escaped_field=mysql_real_escape_string($decoded_column_i);
		unset($decoded_column_i);
		$selector=(int) $_GET["selector-".$i];
		$text_i=$_GET["text-".$i];
		$escaped_text=mysql_real_escape_string($text_i);
		if($andor==1){
			$escaped_andor=" AND";
		}
		else if($andor==2){
			$escaped_andor=" OR";
		}
		else{
			$escaped_andor="";
		}
		if($selector==1){
			$escaped_selection="='".$escaped_text."'";
		}
		elseif($selector==2){
			$escaped_selection="<>'".$escaped_text."'";
		}
		elseif($selector==3){
			$escaped_selection="<'".$escaped_text."'";
		}
		elseif($selector==4){
			$escaped_selection=">'".$escaped_text."'";
		}
		elseif($selector==5){
			$escaped_selection="<='".$escaped_text."'";
		}
		elseif($selector==6){
			$escaped_selection=">='".$escaped_text."'";
		}
		elseif($selector==7){
			$escaped_selection=" LIKE '%".$escaped_text."%'";
		}
		elseif($selector==8){
			$escaped_selection=" NOT LIKE '%".$escaped_text."%'";
		}
		elseif($selector==9){
			$escaped_selection="=''";
		}
		elseif($selector==10){
			$escaped_selection="<>''";
		}
		elseif($selector==11){
			$escaped_selection="='".$escaped_text."%'";
		}
		elseif($selector==12){
			$escaped_selection="<>'".$escaped_text."%'";
		}
		elseif($selector==13){
			$escaped_selection="='%".$escaped_text."'";
		}
		elseif($selector==14){
			$escaped_selection="<>'%".$escaped_text."'";
		}
		else{
			//die();
		}
		unset($text_i);
		unset($selector);
		unset($escaped_text);
		$sql.=$escaped_andor." `".$escaped_field."`".$escaped_selection;
		unset($escaped_field);
		unset($escaped_selection);
	}
}
else{
	//no filters in search

}
if(isset($_GET['order_by'])&&$_GET['order_by']!=''){
	$sql.=" ORDER BY `".mysql_real_escape_string(rawurldecode($_GET['order_by']))."`";
}


$result = mysql_query($sql);
$num_fields = mysql_num_fields($result);
$csv_output="";
$field_name = mysql_fetch_field($result,0)->name;
if(strpos($field_name,'"')===false && strpos($field_name,',')===false){
	$csv_output .= $field_name.",";
}
else{
	$csv_output .= "\"".str_replace('"','""',$field_name)."\",";
}
for($index = 1; $index < $num_fields - 2; $index++){
	$field_name = mysql_fetch_field($result,$index)->name;
	if(strpos($field_name,'"')===false && strpos($field_name,',')===false){
		$csv_output .= $field_name.",";
	}
	else{
		$csv_output .= "\"".str_replace('"','""',$field_name)."\",";
	}
}
$field_name = mysql_fetch_field($result,$num_fields - 1)->name;
if(strpos($field_name,'"')===false && strpos($field_name,',')===false){
	$csv_output .= $field_name;
}
else{
	$csv_output .= "\"".str_replace('"','""',$field_name)."\"";
}
$csv_output .= "\n";
while($row = mysql_fetch_array($result)){
	$value = $row[0];
	if(strpos($value,'"')===false && strpos($value,',')===false){
		$csv_output .= $value.",";
	}
	else{
		$csv_output .= "\"".str_replace('"','""',$value)."\",";
	}
	for($index = 1; $index < $num_fields - 2; $index++){
		$value = $row[$index];
		if(strpos($value,'"')===false && strpos($value,',')===false){
			$csv_output .= $value.",";
		}
		else{
			$csv_output .= "\"".str_replace('"','""',$value)."\",";
		}
	}
	$value = $row[$num_fields-1];
	if(strpos($value,'"')===false && strpos($value,',')===false){
		$csv_output .= $value;
	}
	else{
		$csv_output .= "\"".str_replace('"','""',$value)."\"";
	}
	$csv_output .= "\n";
}
$filename = date("n.j.y",time())." ".rawurldecode($_GET['table_name']);
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
//header("Content-disposition: csv" . date("Y-m-d") . ".csv");
//header("Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;
?>