<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire_model extends CI_Model
{

	public function __construct()
    {
		parent::__construct();
		$this->load->model( "user_model");
		$this->load->model( "department_model");
	}
	public function delete_by_template_id( $template_id ){
		$this->db->trans_start();
		$this->db->delete( "questionnaires", array( "template_id"=> $template_id));
	    $this->db->trans_complete();
	}

	public function add_from_template( $template ){
		foreach( $template->labor_division as $labor_division ){
			$assined_user_id = $labor_division['assigned_user_id'];
			$target_department_id = $labor_division['target_department_id'];
			$this->add( $template->template_id, $assined_user_id, $target_department_id, date("Y-m-d H:i:s") ,"");
		}
	}
	public function add( $template_id, $assined_user_id, $target_department_id, $date, $executor ){
		$data = array(
			'template_id' => $template_id,
			'assigned_user_id' => $assined_user_id,
			'target_department_id' => $target_department_id,
			'date' => $date, 
			'executor' => $executor 
		);
		$this->db->insert( 'questionnaires', $data ); 
	}
	public function get_by_template_id( $template_id ){
		$query = $this->db->get_where( "questionnaires", array( "template_id"=>$template_id ) );
		$tmp_questionnaires = $query->result();
		
		$ret_questionnaires = array();
		foreach( $tmp_questionnaires as $tmp_questionnaire ){
			$ret_questionnaires[] = $this->get( $tmp_questionnaire->questionnaire_id );
		}
		return $ret_questionnaires;  
	}
	public function get( $questionnaire_id ){
		$query = $this->db->get_where( "questionnaires", array("questionnaire_id"=> $questionnaire_id ));
		$ret = $query->row();
		$ret->assigned_user = $this->user_model->get( $ret->assigned_user_id );
		$ret->target_department = $this->department_model->get( $ret->target_department_id );
		return $ret;
	}
}