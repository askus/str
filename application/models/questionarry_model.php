<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Questionarry_model extends CI_Model{
	public function add( $template_id, $assined_user_id, $target_department_id, $date, $excutor ){
		$data = array(
			'template_id' => $template_id,
			'user_id' => $assined_user_id,
			'target_id' => $target_department_id,
			'date' => $date, 
			'excutor' => $excutor 
		);
		$this->db->insert( 'questionarries', $data ); 
	}

}