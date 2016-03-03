<?php
require('session_handler.php');
require('connect.php');
$table_name=$_GET['table_name'];
$sanitized_table_name=mysql_real_escape_string($table_name);
$sanitized_uid = (int) $_SESSION['uid'];
$log='At '.time().': ';
$size_of_GET=count($_GET);




function fix(&$target, $source, $keep = false) {                        
    if (!$source) {                                                            
        return;                                                                
    }                                                                          
    $keys = array();                                                           

    $source = preg_replace_callback(                                           
        '/                                                                     
        # Match at start of string or &                                        
        (?:^|(?<=&))                                                           
        # Exclude cases where the period is in brackets, e.g. foo[bar.blarg]
        [^=&\[]*                                                               
        # Affected cases: periods and spaces                                   
        (?:\.|%20)                                                             
        # Keep matching until assignment, next variable, end of string or   
        # start of an array                                                    
        [^=&\[]*                                                               
        /x',                                                                   
        function ($key) use (&$keys) {                                         
            $keys[] = $key = base64_encode(urldecode($key[0]));                
            return urlencode($key);                                            
        },                                                                     
    $source                                                                    
    );                                                                         

    if (!$keep) {                                                              
        $target = array();                                                     
    }                                                                          

    parse_str($source, $data);                                                 
    foreach ($data as $key => $val) {                                          
        // Only unprocess encoded keys                                      
        if (!in_array($key, $keys)) {                                          
            $target[$key] = $val;                                              
            continue;                                                          
        }                                                                      

        $key = base64_decode($key);                                            
        $target[$key] = $val;                                                  

        if ($keep) {                                                           
            // Keep a copy in the underscore key version                       
            $key = preg_replace('/(\.| )/', '_', $key);                        
            $target[$key] = $val;                                              
        }                                                                      
    }                                                                          
}      



fix($_GET, $_SERVER['QUERY_STRING']);


$field_name_array = array();
$i=0;
foreach ($_GET as $key){
	$field_name_array[$i]=$key;
	$i++;
}

$field_name_array=array_keys($_GET);
$sql="ALTER TABLE `$sanitized_table_name`";

$sanitized_field_name=mysql_real_escape_string($field_name_array[2]);
$sanitized_previous_field_name=mysql_real_escape_string($field_name_array[2-1]);
$row=mysql_fetch_array(mysql_query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = '$sanitized_table_name' AND COLUMN_NAME = '$sanitized_field_name'"));
$sanitized_definition=mysql_real_escape_string($row['COLUMN_TYPE']);
$sql.=" MODIFY `$sanitized_field_name` $sanitized_definition AFTER `$sanitized_previous_field_name`";
for($i=3;$i<$size_of_GET;$i++){
	$field_name = rawurldecode($field_name_array[$i]);
	$sanitized_field_name=mysql_real_escape_string($field_name);
	$previous_field_name = $field_name_array[$i-1];
	$sanitized_previous_field_name=mysql_real_escape_string($previous_field_name);
	$definition=mysql_fetch_object(mysql_query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = '$sanitized_table_name' AND COLUMN_NAME = '$sanitized_field_name'"))->COLUMN_TYPE;
	$sanitized_definition=mysql_real_escape_string($definition);
	if(strlen($definition)>0){
		//echo(htmlentities($definition).'<br />');
	}
	else{
		//echo("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = '$sanitized_table_name' AND COLUMN_NAME = '$sanitized_field_name'".'<br />');
	}
	$sql.=", MODIFY `$field_name` $definition AFTER `$previous_field_name`";//using unsanitized values due to mysql conflicts
	unset($row);
}
$name_row = mysql_fetch_array(mysql_query("SELECT `fname`,`lname` FROM `users` WHERE `uid`='$sanitized_uid'"));
$user_name = $name_row['fname'] . ' ' . $name_row['lname'];
unset($name_row);
$sanitized_description = mysql_real_escape_string($user_name.' reordered the columns in "'.$table_name.'"');
unset($user_name);
$sanitized_target_table_id = (int) mysql_fetch_object(mysql_query("SELECT `id` FROM `tables` WHERE `name`='$sanitized_table_name'"))->id;
$sanitized_time = (int) time();
$sanitized_action_id = 2;
mysql_query("INSERT INTO `table_edit_log` (`time`,`uid`,`action_id`,`target_table_id`,`description`) VALUES ('$sanitized_time','$sanitized_uid','$sanitized_action_id','$sanitized_target_table_id','$sanitized_description')");
if(mysql_query($sql)){
	echo('<script type="text/javascript">window.location.replace("manage_table.php?table_name='.rawurlencode($table_name).'");</script>');
}
else{
	die(mysql_error().'<br /><br />'.$sql);
}
/*
$sanitized_entry=mysql_real_escape_string($log);
$sql="INSERT INTO `log` (`entry`) VALUES ('".$sanitized_entry."')";

mysql_query($sql);
*/
?>