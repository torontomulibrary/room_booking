<?php

class settings_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

   	function get_all_settings_array(){
		$settings = array();
		
		$results = $this->db->get('settings');
		
		foreach($results->result() as $setting){
			$settings[$setting->property] = $setting->value;
		}
		
		return $settings;
	}
	
	function get_property($property){
		$this->db->where('property', $property);
		
		$result = $this->db->get('settings');
		return $result->row()->value;
	}
	
	
	function get_site_title(){
		$this->db->where('property', 'site_title');
		return $this->db->get('settings');
	}
	
	function set_site_title($title){
		$data = array(
			'value' => $title,
		);
		
		$this->db->where('property', 'site_title'); 
		$this->db->update('settings', $data); 
		
		$this->db->cache_delete_all();
		
		return TRUE;


	}
	
	function check_debug(){
		$this->db->where('property', 'debug_mode');
		$result = $this->db->get('settings');
		
		if($result->row()->value == 1) return true;
		else return false;
	}
	
	function save_settings($settings_array){
		foreach ($settings_array as $setting=>$value){
			$input = array(
				'value' => $value
			);
			
			$this->db->where('property', $setting);
			$this->db->update('settings', $input);

		}
		
		$this->db->cache_delete_all();
	}
	


}
