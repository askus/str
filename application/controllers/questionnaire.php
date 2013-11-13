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

    public function index(){
        check_permission( $this->controller, 'edit');
        $user_id = $this->session->userdata('user_id');
        $questionnaires= $this->questionnaire_model->get_by_user_id( $user_id );
        $data = array("questionnaires" => $questionnaires);
        $this->layout->view('index_questionnaire', $data );
    }
    public function ajax_add(){
        check_permission( $this->controller, 'add');
        $template_id = $this->input->post("template_id", true);
        $assigned_user_id = $this->input->post("assigned_user_id", true);
        $target_department_id = $this->input->post("target_department_id", true);

        $this->questionnaire_model->add_by_labor_division(  $template_id, $assigned_user_id, $target_department_id );
        
        echo '{ "status":"ok","errMsg":[] }';
    }

    public function ajax_delete( ){
        check_permission( $this->controller, 'delete');

        $questionnaire_id = $this->input->post('questionnaire_id', true);
        $this->questionnaire_model->delete(  $questionnaire_id);

        echo' {"status":"ok", "errMsg":[] } ';
    }

    public function check_user_id_for_edit_permission( $questionnaire_user_id ){
        
        $login_user_role_id = $this->session->userdata('role_id');
        $login_user_id = $this->session->userdata('user_id');
        
        if( $questionnaire_user_id == $login_user_id ){
            return true ;
        }else if( $login_user_role_id == 1 ){
            return true ; 
        }else{
            return false ; 
        }
    }

    public function complete(){
        check_permission( $this->controller, 'edit'); 

        $questionnaire_form = $this->input->post('questionnaire', true);
        $questionnaire_score_form = $this->input->post('questionnaire_score', true);
    
        $questionnaire = $this->questionnaire_model->form2questionnaire( $questionnaire_form, $questionnaire_score_form);
        // set now date 
        $questionnaire->last_modified_datetime = date("Y-m-d H:i:s");
        //set the status into complete
        $questionnaire->status = $this->questionnaire_model->STATUS_COMPLETE;
        $this->questionnaire_model->update( $questionnaire );
        
        $login_user_role_id = $this->session->userdata('role_id');
        if( $login_user_role_id == 2 ){
            redirect( base_url("questionnaire"));
        }else if( $login_user_role_id == 1 ){
            redirect( base_url('template'));
        }
    }

    public function temp_save(){
        check_permission( $this->controller, 'edit'); 

        $questionnaire_form = $this->input->post('questionnaire', true);
        $questionnaire_score_form = $this->input->post('questionnaire_score', true);
    
        $questionnaire = $this->questionnaire_model->form2questionnaire( $questionnaire_form, $questionnaire_score_form);
        // set now date 
        $questionnaire->last_modified_datetime = date("Y-m-d H:i:s");

        //set the status into processed
        $questionnaire->status = $this->questionnaire_model->STATUS_PROCESSED;

        $this->questionnaire_model->update( $questionnaire );
        $is_ok = true; 
        if ( $is_ok ) {
            echo '{ "status":"ok", "errMsg":[] }';
        }else{
            echo '{"status":"fault", "errMsg":[] }';
        }
    }


    public function view( $questionnaire_id ){
        check_permission( $this->controller, 'view');
        $questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );
        
        $data['questionnaire'] = $questionnaire ;
        $this->layout->view('view_questionnaire', $data );

    }


    public function edit( $questionnaire_id ){
    	check_permission($this->controller, 'edit');
        $data['css']= array('bootstrap-datetimepicker.min.css');

    	// check is the assinged user 
    	$questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );


		// if login_user == administrator or the assigned user   
    	if( $this->check_user_id_for_edit_permission( $questionnaire->assigned_user->user_id ) ){
    		$data['questionnaire'] = $questionnaire;
    		$data['last_modified_user_id'] = $this->session->userdata('user_id');
            $this->layout->view( 'edit_questionnaire', $data );
    	}else{
    		redirect( base_url("questionnaire") );
    	}
    }

}