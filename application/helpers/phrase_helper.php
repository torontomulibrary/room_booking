<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function phrase($saying){
	$CI =& get_instance();
	$CI->load->model('role_model');
	
	$template = str_replace("_template", "", $CI->role_model->get_theme());

	//Look up phrase in the template folder
	
	//Create path to template phrases file
	$path = FCPATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "template". DIRECTORY_SEPARATOR . $template. DIRECTORY_SEPARATOR ."phrases.php";

	//Verify file exists
	if(!file_exists($path)) return $saying;

	//Load file
	include($path);

	if(array_key_exists($saying, $phrases)){
		return $phrases[$saying];
	}
	else{
		return $saying;
	}
}