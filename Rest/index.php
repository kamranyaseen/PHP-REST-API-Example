<?php

function deliver_response($format, $api_response){

	$http_response_code = array(
		200 => 'OK',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found'
	);
	header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);

	
	if( strcasecmp($format,'json') == 0 ){
     	header('Content-Type: application/json; charset=utf-8');
		$json_response = json_encode($api_response);
		echo $json_response;

	}elseif( strcasecmp($format,'xml') == 0 ){
		header('Content-Type: application/xml; charset=utf-8');
		$xml_response = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
			'<response>'."\n".
			"\t".'<code>'.$api_response['code'].'</code>'."\n".
			"\t".'<data>'.$api_response['data'].'</data>'."\n".
			'</response>';
		echo $xml_response;

	}else{
		header('Content-Type: text/html; charset=utf-8');
		echo $api_response['data'];

	}
	exit;

}
$HTTPS_required = FALSE;
$authentication_required = FALSE;
$api_response_code = array(
	0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
	1 => array('HTTP Response' => 200, 'Message' => 'Success'),
	2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
	3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
	4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
	5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
	6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
);
$response['code'] = 0;
$response['status'] = 404;
$response['data'] = NULL;
if( $HTTPS_required && $_SERVER['HTTPS'] != 'on' ){
	$response['code'] = 2;
	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
	$response['data'] = $api_response_code[ $response['code'] ]['Message'];
	deliver_response($_GET['format'], $response);
}
if( $authentication_required ){

	if( empty($_POST['username']) || empty($_POST['password']) ){
		$response['code'] = 3;
		$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
		$response['data'] = $api_response_code[ $response['code'] ]['Message'];
		deliver_response($_GET['format'], $response);

	}
	elseif( $_POST['username'] != 'root' && $_POST['password'] != 'root' ){
		$response['code'] = 4;
		$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
		$response['data'] = $api_response_code[ $response['code'] ]['Message'];
		deliver_response($_GET['format'], $response);

	}

}
if( strcasecmp($_GET['method'],'hello') == 0){
	$response['code'] = 1;
	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
	$response['data'] = 'Hello World';
}
deliver_response($_GET['format'], $response);

?>
    