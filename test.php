<?php
$name ='giapnh434';
if (!preg_match("/^[a-zA-Z ][a-zA-Z0-9]{6,30}$/",$name))
  {
  $nameErr = "Only letters and white space allowed"; 
  }else{
	echo "OK";
  }

?>