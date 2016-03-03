<?php
require('session_handler.php');
require('connect.php');
$decoded_table_name=rawurldecode($_GET['table_name']);
$sanitized_table_name=mysql_real_escape_string($decoded_table_name);
$html_table_name=htmlentities($decoded_table_name);

$decoded_search_title=rawurldecode($_GET['search-title']);
$escaped_search_title=mysql_real_escape_string($decoded_search_title);

$sql="SELECT * FROM `".$sanitized_table_name."`";
$english_sql="results from ".$decoded_table_name."";
$search_get_vars="?table_name=".rawurlencode($decoded_table_name);
if(isset($_GET["column-0"])){
	$sql.=" WHERE";
	$english_sql.=" where";
	for($i=0;isset($_GET["column-".$i]);$i++){
		$andor="";
		$get_var_andor;
		if($i>0){
			$andor=(int) $_GET["andor-".$i];
			$get_var_andor='&andor-'.$i.'='.$_GET['andor-'.$i];
		}
		else{
			$get_var_andor='';
		}
		$search_get_vars.=$get_var_andor.'&column-'.$i.'='.rawurlencode(rawurldecode($_GET["column-".$i])).'&selector-'.$i.'='.rawurlencode(rawurldecode($_GET["selector-".$i])).'&text-'.$i.'='.rawurlencode(rawurldecode($_GET['text-'.$i]));
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
			$english_selection=" is '".htmlentities($text_i)."'";
		}
		elseif($selector==2){
			$escaped_selection="<>'".$escaped_text."'";
			$english_selection=" is not '".htmlentities($text_i)."'";
		}
		elseif($selector==3){
			$escaped_selection="<'".$escaped_text."'";
			$english_selection=" is less than '".htmlentities($text_i)."'";
		}
		elseif($selector==4){
			$escaped_selection=">'".$escaped_text."'";
			$english_selection=" is greater than '".htmlentities($text_i)."'";
		}
		elseif($selector==5){
			$escaped_selection="<='".$escaped_text."'";
			$english_selection=" is less than or equal to '".htmlentities($text_i)."'";
		}
		elseif($selector==6){
			$escaped_selection=">='".$escaped_text."'";
			$english_selection=" is greater than or equal to '".htmlentities($text_i)."'";
		}
		elseif($selector==7){
			$escaped_selection=" LIKE '%".$escaped_text."%'";
			$english_selection=" contains '".htmlentities($text_i)."'";
		}
		elseif($selector==8){
			$escaped_selection=" NOT LIKE '%".$escaped_text."%'";
			$english_selection=" does not contain '".htmlentities($text_i)."'";
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
			$english_selection=" begins with '".htmlentities($text_i)."'";
		}
		elseif($selector==12){
			$escaped_selection="<>'".$escaped_text."%'";
			$english_selection=" does not begin with '".htmlentities($text_i)."'";
		}
		elseif($selector==13){
			$escaped_selection="='%".$escaped_text."'";
			$english_selection=" ends with '".htmlentities($text_i)."'";
		}
		elseif($selector==14){
			$escaped_selection="<>'%".$escaped_text."'";
			$english_selection=" does not end with '".htmlentities($text_i)."'";
		}
		else{
			die();
		}
		unset($text_i);
		unset($selector);
		unset($escaped_text);
		$english_sql.=" ".$english_andor.$english_field.$english_selection;
		$sql.=$escaped_andor." `".$escaped_field."`".$escaped_selection;
		
		unset($english_andor);
		unset($english_field);
		unset($english_selection);
		unset($escaped_field);
		unset($escaped_selection);
	}
	$search_title;
	if(isset($_GET['search-title'])){
		if($_GET['search-title']!=''){
			$search_title=rawurldecode($_GET['search-title']);
		}
		else{
			$search_title='Untitled Search';
		}
	}
	else{
		$search_title='Untitled Search';
	}
	$authid=$_SESSION['uid'];
	$table_id_row = mysql_fetch_array(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name' LIMIT 1"));
	$sanitized_table_id = (int) $table_id_row['id'];
	unset($table_id_row);
	if(isset($_GET['order_by'])&&$_GET['order_by']!=''){
		$order_by = rawurldecode($_GET['order_by']);
		$sql.=" ORDER BY `".mysql_real_escape_string($order_by)."`";
		$english_sql.=" sort by ".$order_by;
		$search_get_vars.="&order_by=".$order_by;
	}
	$sanitized_sql=mysql_real_escape_string($sql);
	$sanitized_english=mysql_real_escape_string($english_sql);
	$sanitized_get=mysql_real_escape_string($search_get_vars);
	$sanitized_name=mysql_real_escape_string($search_title);
	$sanitized_authid=(int) $authid;
	$sanitized_time=(int) time();
	$sql_query="INSERT INTO `saved_searches` (`table_id`,`sql_version`,`english_version`,`get_version`,`name`,`authuid`,`time`) VALUES ('$sanitized_table_id','$sanitized_sql','$sanitized_english','$sanitized_get','$sanitized_name','$sanitized_authid','$sanitized_time')";
	if(mysql_query($sql_query)){
		//I know this looks stupid, but it is necessary to traverse the $_GET array twice because the search_id from the result of the first is a required part of the next query, performed here.
		$search_id_row = mysql_fetch_array(mysql_query("SELECT `id` FROM `saved_searches` WHERE `authuid`='$sanitized_authid' ORDER BY `id` DESC LIMIT 1"));
		$sanitized_search_id = (int) $search_id_row['id'];
		unset($search_id_row);
		$sanitized_column=mysql_real_escape_string($_GET['column-0']);
		$sanitized_selector=(int)$_GET['selector-0'];
		$sanitized_text=mysql_real_escape_string($_GET['text-0']);
		$saved_search_terms_sql = "INSERT INTO `saved_search_terms` (`search_id`,`n`,`field_name`,`selector`,`andor`,`text`) VALUES ('$sanitized_search_id','0','$sanitized_column','$sanitized_selector','0','$sanitized_text')";
		for($i=1;isset($_GET["column-".$i]);$i++){
			$sanitized_andor=0;
			if(isset($_GET['andor-'.$i])&&$_GET['andor-'.$i]!=''){
				$sanitized_andor=(int)$_GET["andor-".$i];
			}
			$sanitized_column=mysql_real_escape_string($_GET["column-".$i]);
			$sanitized_selector=(int)$_GET["selector-".$i];
			$sanitized_text=mysql_real_escape_string($_GET["text-".$i]);
			$sanitized_i=(int)$i;
			$saved_search_terms_sql.=", ('$sanitized_search_id','$sanitized_i','$sanitized_column','$sanitized_selector','$sanitized_andor','$sanitized_text')";
		}
		
		if(mysql_query($saved_search_terms_sql)){
			header('Location: saved_searches.php');
		}
		else{
			header('Location: unexpected_error.php');
		}
	}
	else{
		header('Location: unexpected_error.php');
	}
}
else{
	//no filters in search
}
?>