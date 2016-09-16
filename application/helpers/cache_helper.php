<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function empty_cache(){
	$CI =& get_instance();
	
	foreach (glob(FCPATH.'temp'.DIRECTORY_SEPARATOR.'*') as $filename) {
		if (is_file($filename)) {
			if(!strstr($filename, 'README.txt')) unlink($filename);
		}
	}
		
	$CI->db->cache_delete_all();
}