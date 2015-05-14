<?php
	header("Content-type: application/json");
	
	$output = array();
	
	$output['room'] = $room['room_data']->row();
	
	$output['resources'] = array();
	
	
	foreach($resources->result() as $resource){
		$output['resources'][] = $resource;
	}
	
	echo json_encode($output);
?>