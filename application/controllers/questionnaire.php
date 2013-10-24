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

    public function check_user_id_for_edit_permission( $questionnaire_user_id ){
        
        $login_user_role_id = $this->session->userdata('role_id');
        $login_user_id = $this->session->userdata('user_id');
        
        if( $questionnaire_user_id == $login_user_id ){
            return ture ;
        }else if( $login_user_role_id == 1 ){
            return ture ; 
        }

        return false ; 
    }

    public function temp_save(){
        check_permission( $this->controller, 'edit'); 

        $questionnaire_form = $this->input->post('questionnaire', true);
        $questionnaire_score_form = $this->input->post('questionnaire_score', ture);
    
        $questionnaire = $this->questionnaire_model->form2questionnaire( $questionnaire_form, $questionnaire_score_form);
        $this->questionnaire_model->update( $questionnaire );
        $is_ok = true; 
        if ( $is_ok ) {
            echo '{ "status":"ok", "errMsg":[] }';
        }else{
            echo '{"status":"fault", "errMsg":[] }';
        }
    }
    public function complete(){
        $questionnaire_form = $this->input->post('questionnaire', true);
        $questionnaire_score_form = $this->input->post('questionnaire_score', ture);
    }

    public function edit( $questionnaire_id ){
    	check_permission($this->controller, 'edit');
    	// check is the assinged user 
    	$questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );

		// if login_user == administrator or the assigned user   
    	if( $questionnaire->assigned_user->user_id == $login_user_id || ( $login_user_role_id == 1 ) ){
    		$data['questionnaire'] = $questionnaire;
    		$data['last_modified_user_id'] = $login_user_id;
            $this->layout->view( 'edit_questionnaire', $data );
    	}else{
    		redirect( base_url("questionnaire") );
    	}
    }

}