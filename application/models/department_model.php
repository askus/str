<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Department_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_department(){
    	$departments = $this->db->get("departments")->result();
        return $departments;
    }
    public function get( $department_id ){
    	return $this->db->get_where('departments', array("department_id"=> $department_id) )->row();
    }
}