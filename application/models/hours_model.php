<?php

class hours_Model  extends CI_Model  {
	
	function getHours($ext_id, $date){
	
	}
	
	function getAllHours($date){
		//Prepare the date into Coldfusion's horrible timestamp
		$timestamp = "{ts '". date('Y-m-d', $date) . " 00:00:00'}";
		
		$opts = array(
		  'http'=>array(
			'method'=>"GET",
			'header'=>"User-Agent: Library Room Booking\r\n" 
		  ),
		);

		$context = stream_context_create($opts);

		//We need to find the MIN and MAX time for the current users roles
		$min = 2; //In coldfusion, 1 is midnight of the next day. Use 2 (instead of 1 in this case to cover hours such as 1am)
		$max = -1; //In coldfusion, 0 is midnight
		
		//First, find the external ID'd for all buildings the user can book
		$this->load->model('room_model');
		$rooms = $this->room_model->list_rooms(true);
		
		$buildings = array();
		foreach($rooms->result() as $room){
			if(!in_array($room->external_id, $buildings)){
				$buildings[] = $room->external_id;
			}
		}
		
		$url = HOURS_URL . '?dt='.urlencode($timestamp).'&l=all';
		
		
		
		$jsonData = @file_get_contents($url, false, $context);
		
		if($jsonData !== FALSE){
			//var_dump(json_decode($jsonData)); die;
			
			$hours_json = json_decode($jsonData);
			
			$output = array();
			
			foreach($hours_json as $location){
				$output[$location->LOCATION_ID] = $location->DATA;
				
				if(in_array($location->LOCATION_ID, $buildings)){
					if($location->DATA->STARTTIME == $location->DATA->ENDTIME || $location->DATA->ISOPEN == false || $location->DATA->HASCLOSURE == true){
						//Assume the building is closed in this situation
						continue;
					}
					
					//Should we factor in the "ISOPEN" property here?
					if($location->DATA->STARTTIME < $min) $min = $location->DATA->STARTTIME;
					if($location->DATA->ENDTIME > $max) $max = $location->DATA->ENDTIME;
				}
			}
			
			$output['min'] = $min;
			$output['max'] = $max;
			
		//	var_dump($output);
			
			return $output;
		}
		
		return FALSE;		
	}
}