<?php

// ini_set("session.gc_maxlifetime", "18000");  //set session expire to 5 hours
ini_set("session.gc_maxlifetime", "604800"); //set session expire to 7 days

date_default_timezone_set("Europe/London");

//start sessions
if(!headers_sent())
{ 
	if (isset($return_session_id))
	{
		session_start($return_session_id);
		// echo session_id();
	}
	else session_start();
}
$sessionId = session_id();

$now = date('Y-m-d H:i:s');

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Central\CentralGeneral;
use Central\CentralPDOMySQL;

$dbHost = str_replace("http://", "", str_replace("www.", "", $_SERVER['HTTP_HOST']));

$dataLink = new CentralPDOMySQL('{dataHost>', '{dataUsername}', '{dataPassword}', '{dataDatabase}');
CentralGeneral::recordVisitorData($dbHost, // Record in the remote database activity for this particular domain
								  '{dataTable}', 
								  '{dataTableHeading}', 
								  $sessionId, 
								  $dataLink, 
								  '', 
								  array('inc_location'=>false));

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Examples</title>
</head>

<body>

	<div class="formfield first_name"><input id="first_name" name="first_name" placeholder="First Name"></div>
    <div class="formfield surname"><input id="surname" name="surname" placeholder="Surname"></div>
    <div class="formfield email"><input id="email" name="email" placeholder="Email"></div>
	<div class="form_button"><input id="submit_button" name="submit_button" value="Submit"></div>       
        
	<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="../Javascript/jquery.ajax_handler.js"></script>
	<script src="../Javascript/main.js"></script>

</body>
</html>