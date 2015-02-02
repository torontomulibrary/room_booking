<?php

class hours_Model  extends CI_Model  {
	
	function getHours($ext_id, $date){
	
	}
	
	function getAllHours($date){
		//Check to see if a cache file already exists
		if(file_exists('temp/'. date('Ymd', $date).'.hours')){
			$jsonData = @file_get_contents('temp/'. date('Ymd', $date).'.hours');
		}
		else{
			
			//Prepare the date into Coldfusion's horrible timestamp
			$timestamp = "{ts '". date('Y-m-d', $date) . " 00:00:00'}";
			
			$opts = array(
			  'http'=>array(
				'method'=>"GET",
				'header'=>"User-Agent: Library Room Booking\r\n" 
			  ),
			);

			$context = stream_context_create($opts);

			$url = HOURS_URL . '?dt='.urlencode($timestamp).'&l=all';
			
			
			
			$jsonData = @file_get_contents($url, false, $context);
			
			if($jsonData === FALSE){ 
				return FALSE; 
			}
			
			//Write it to a file
			file_put_contents('temp/'. date('Ymd', $date).'.hours', $jsonData);
		}
		
		$hours_json = json_decode($jsonData);
		
		//Make sure it is valid JSON
		if($hours_json === null) return FALSE;
		
		$output = array();
		
		$min = 2; //In coldfusion, 1 is midnight of the next day. Use 2 (instead of 1 in this case to cover hours such as 1am)
		$max = -1; //In coldfusion, 0 is midnight
		
		//Load all of the external ID's
		$this->load->model('room_model');
		$rooms = $this->room_model->list_rooms(true);
		
		$buildings = array();
		foreach($rooms->result() as $room){
			if(!in_array($room->external_id, $buildings)){
				$buildings[] = $room->external_id;
			}
		}

		//Match the external ID's with those in the JSON result
		foreach($hours_json as $location){
			$output[$location->LOCATION_ID] = $location->DATA;
			
			if(in_array($location->LOCATION_ID, $buildings)){
				//Is the building closed?
				if($location->DATA->STARTTIME == $location->DATA->ENDTIME || $location->DATA->ISOPEN == false || $location->DATA->HASCLOSURE == true){
					//Delete the cache file, as the user may be looking too far into the future where the hours have not yet been entered
					@unlink('temp/'. date('Ymd', $date).'.hours');					
					continue;
				}
				
				//Should we factor in the "ISOPEN" property here?
				if($location->DATA->STARTTIME < $min) $min = $location->DATA->STARTTIME;
				if($location->DATA->ENDTIME > $max) $max = $location->DATA->ENDTIME;
			}
		}
		
		$output['min'] = $min;
		$output['max'] = $max;
		
		return $output;

	}
}