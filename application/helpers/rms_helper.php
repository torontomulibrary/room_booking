<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function is_access_center($username){
	$response = array();
	
	
	// Create a stream
	$opts = array(
	  'http'=>array(
		'method'=>	"POST",
		'header'=> 	"Authorization: Basic ".base64_encode(RMS_USERNAME.":".RMS_PASSWORD)."\r\n",
		'content'=>	'',
		'timeout'=>	60
	  )
	);

	$context = stream_context_create($opts);

	// Block the user using the HTTP headers set above
	$url = str_replace('{username}', urlencode($username), RMS_SERVICE);
	
	$file = file_get_contents($url, false, $context);
	echo "Authorization: Basic ".base64_encode(RMS_USERNAME.":".RMS_PASSWORD)."\r\n";
	var_dump($http_response_header); die;
	
	//Make sure the server gave a 200 response, else return the user an error
	if($http_response_header[0] === "HTTP/1.1 200 OK"){
		return TRUE;
	}
	else{
		return FALSE;
	}
}