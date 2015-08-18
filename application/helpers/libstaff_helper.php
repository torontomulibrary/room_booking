<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function is_libstaff($username){
	// Create a stream
	$opts = array(
	  'http'=>array(
		'method'=>	"GET",
		'User-agent' => "LibraryBooking/1.0",
		'content'=>	'',
		'timeout'=>	60
	  )
	);

	$context = stream_context_create($opts);

	// Block the user using the HTTP headers set above
	$url = LIBSTAFF_URL;
	
	$file = @file_get_contents($url, false, $context);
	
	//Make sure the server gave a 200 response, else return the user an error
	if($file !== FALSE && $http_response_header[0] === "HTTP/1.1 200 OK"){
		$data = json_decode($file);
		
		$found = false;
		
		foreach ($data as $matrix){
			if($username == $matrix) return TRUE;
		}
		
		if($found == false) return FALSE;
	}
	else{
		return FALSE;
	}
}