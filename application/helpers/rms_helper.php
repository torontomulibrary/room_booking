<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function is_access_center($username){
	$response = array();
	
	
	// Create a stream
	$opts = array(
	  'http'=>array(
		'method'=>	"GET",
		'User-agent' => "LibraryBooking/1.0",
		'header'=> 	"Authorization: Basic ".base64_encode(RMS_USERNAME.":".RMS_PASSWORD)."\r\n",
		'content'=>	'',
		'timeout'=>	60
	  )
	);

	$context = stream_context_create($opts);

	// Block the user using the HTTP headers set above
	$url = str_replace('{username}', urlencode($username), RMS_SERVICE);
	
	$file = @file_get_contents($url, false, $context);
	
	//Make sure the server gave a 200 response, else return the user an error
	if($file !== FALSE && $http_response_header[0] === "HTTP/1.1 200 OK"){
		$data = json_decode($file);
		
		if($data->hasOwnerResource == true){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return FALSE;
	}
}