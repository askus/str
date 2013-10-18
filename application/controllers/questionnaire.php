<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire extends CI_Controller
{
	private $controller;

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->controller = strtolower(__CLASS__);
    	$this->load->model('questionnaire_model');
    	$this->load->model('template_model');
    }
    public function edit( $questionnaire_id ){
    	check_permission($this->controller, 'edit');
    	// check is the assinged user 
    	$questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );
    	$login_user_role_id = $this->session->userdata('role_id');
    	$login_user_id = $this->session->userdata('user_id');
		// if login_user == administrator or the assigned user   
    	if( $questionnaire->assigned_user->user_id == $login_user_id || ( $login_user_role_id == 1 ) ){
    		$data['questionnaire'] = $questionnaire;
    		$this->layout->view( 'edit_questionnaire', $data );
    	}else{
    		redirect( base_url("questionnaire") );
    	}
    }

}