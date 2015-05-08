<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/admin
	 *	- or -  
	 * 		http://example.com/index.php/admin/index
	 *	- or -
	**/
	 
	
	function Api(){
		parent::__construct();
	}

	
	public function count_free_rooms(){
		$this->load->model('booking_model');
		
		$this->output->cache(5); //Cache for 5 minutes
		
		$data['free_rooms'] = $this->booking_model->count_free_rooms(4); //$role_id "4" is undergrads
		$this->load->view('api/free_rooms', $data);
	}
	
	
}