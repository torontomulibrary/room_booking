<?php

class resource_Model  extends CI_Model  {

	function __construct() {
		parent::__construct();
	}

	function load_resources($resources){
		if(!is_array($resources)) return FALSE;
		
		if(count($resources) > 0){
			$sql = "SELECT * FROM resources WHERE resource_id IN (". implode($resources,',').")";
		}
		else{
			$sql = "SELECT * FROM resources WHERE 0=1"; 	//It's just easier this way. Always a query result with the empty set
		}
		return $this->db->query($sql);
	}
	
	function list_resources($params = array()){
		$this->db->select('*');
		$this->db->from('resources');
			
		if(array_key_exists("where", $params)){
				foreach($params['where'] as $key => $val){
						$this->db->where($key, $val);
				}
		}

		if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
			$result = $this->db->count_all_results();
		}else{
			if(array_key_exists("resource_id", $params)){
				$this->db->where('resource_id', $params['resource_id']);
				$query = $this->db->get();
				$result = $query->row_array();
			}else{
				$this->db->order_by('name', 'asc');
				if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
						$this->db->limit($params['limit'],$params['start']);
				}elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
						$this->db->limit($params['limit']);
				}
				
				$query = $this->db->get();
				$result = ($query->num_rows() > 0)?$query->result_array():FALSE;
			}
		}
		
		// $this->db->order_by('name', 'asc');
		return $result;
	}
	
	function list_room_resources($room_id){
		if(!is_numeric($room_id)) return false;
		
		$sql = "SELECT 
					r.resource_id, r.name, r.desc, r.can_filter, r.image
				FROM
					resources r,
					room_resource rr
				WHERE
					r.resource_id = rr.resource_id
					AND rr.room_id = $room_id";
		
		return $this->db->query($sql);
	}	
	
	function get_resource($resource_id){
		$this->db->where('resource_id', $resource_id);
		return $this->db->get('resources');
	}
	
	// function edit_resource($id, $name, $desc, $filter){
	public function update($data, $id){
		if(!empty($data) && !empty($id)){
			$data['can_filter'] = $data['can_filter'] === 'on';

			// Update member data
			$update = $this->db->update(
				'resources',
				$data,
				array('resource_id' => $id)
			);
		
			// Return the status
			$this->db->cache_delete_all();
			return $update?true:false;
		}

		return false;
	}
	
	public function add_room_resource($data = array()){
		if(!empty($data)){
			$data['can_filter'] = $data['can_filter'] === 'on';
			
			// Insert member data
			$this->db->cache_delete_all();
			$insert = $this->db->insert('resources', $data);
				
			// Return the status
			return $insert?$this->db->insert_id():false;
		}
		return false;

	}
	
	function delete_resource($id){
		$this->db->cache_delete_all();

		$result = $this->db->delete('resources', array('resource_id' => $id));

		return $result?true:false;
	}

}
