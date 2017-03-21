<?php

// require '../config/settings.php';
//print_r($dbSettings);
//die();
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Central\CentralGeneral;
use Central\CentralPDOMySQL;

$data = (array) json_decode($_POST['data']);

if ($data['action']=='saveFormData')
{
	require_once('functions.php');
	
	$first_name = Functions::formatName(CentralPDOMySQL::escapeString($data['first_name']));
	$surname    = Functions::formatName(CentralPDOMySQL::escapeString($data['surname']));
	$email      = strtolower(CentralPDOMySQL::escapeString($data['email']));
	
	$criteria = array('first_name'=>$first_name,
					  'surname'   =>$surname,
					  'email'	  =>$email);
	
	$sql = $mysql->queryBuilder('users', 'user_id', $criteria, 'first_name asc', '', '');
	$data['checkSQL'] = $sql;
	$user = $mysql->singleRow($sql);
		
	$criteria['session_id'] = $sessionId;
	if ((isset($user['user_id'])) && ($user['user_id']!='')):
		// Record does already exist so we need to update it
		$updateSettings = array_merge($criteria, array('updated'=>$now));
		$sql = $mysql->updateQuery('users', $updateSettings, array('user_id'=>$user['user_id']));
		$mysql->query($sql);	
		$data['updateSQL'] = $sql;	
	else:
		// Record doesn't exist yet so we insert a new one 
		$insertSettings = array_merge($criteria, array('created'=>$now));
		$sql = $mysql->insertQuery('users', $insertSettings);
		$mysql->query($sql);
		$data['insertSQL'] = $sql;
	endif;
	$data['status'] = 'success';
}

echo json_encode($data);


?>