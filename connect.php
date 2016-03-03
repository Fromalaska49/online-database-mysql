<?php
if(mysql_connect('localhost:8080','root','bitnami')){
	//echo('He moved!<br />');
}
else{
	//echo('He dead<br />');
	//die(mysql_error());
}
if(mysql_select_db('mainall')){
	//echo('He survived!<br />');
}
else{
	//echo('Aw, he dead.');
	//die(mysql_error());
}
?>