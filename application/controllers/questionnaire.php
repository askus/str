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
        $this->load->model('department_model');
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

    public function check_department_id( $department_id ){
        $login_user_department_id = $this->session->userdata('department_id');
        return $department_id == $login_user_department_id; 
    }

    public function is_author( $questionnaire_user_id ){
        $login_user_id = $this->session->userdata('user_id');
        return $questionnaire_user_id == $login_user_id;
    }
    public function is_admin( ){
        $login_user_role_id = $this->session->userdata('role_id');
        return $login_user_role_id == 1 ;
    }
/*
    public function check_author_id_or_admin_id( $questionnaire_user_id ){
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
*/
    /*
    public function check_user_id_for_edit_permission( $questionnaire_user_id ){
        return check_author_id_or_admin_id( $questionnaire_user_id );
    }
*/
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

    public function view_encrypted( $questionnaire_id ){
        check_permission( $this->controller, 'view_encrypted');
        $questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );
        
        if(  $this->is_admin() || $this->check_department_id( $questionnaire->target_department_id ) ){
            $data['questionnaire'] = $questionnaire ;
            $data['is_encrypted'] = true; 
            $data['css']= array('view-questionnaire.css');
            $data['print_css'] = array('view-questionnaire.css');
            $this->layout->view('view_questionnaire', $data );
        }else{
            redirect( base_url("questionnaire") );
        }
    }

    public function view( $questionnaire_id ){
        check_permission( $this->controller, 'view');
        $questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );
        
        if( $this->is_author( $questionnaire->assigned_user->user_id ) || $this->is_admin() ){
            $data['questionnaire'] = $questionnaire ;
            $data['is_encrypted'] = false; 
            $data['css']= array('view-questionnaire.css');
            $data['print_css'] = array('view-questionnaire.css');
            $this->layout->view('view_questionnaire', $data );
        }else{
            redirect( base_url("questionnaire") );
        }
    }


    public function edit( $questionnaire_id ){
    	check_permission($this->controller, 'edit');
        $data['css']= array('bootstrap-datetimepicker.min.css');

    	// check is the assinged user 
    	$questionnaire = $this->questionnaire_model->get_with_question_score( $questionnaire_id );


		// if login_user == administrator or the assigned user   
    	if( $this->is_author( $questionnaire->assigned_user->user_id ) || $this->is_admin() ){
    		$data['questionnaire'] = $questionnaire;
    		$data['last_modified_user_id'] = $this->session->userdata('user_id');
            $this->layout->view( 'edit_questionnaire', $data );
    	}else{
    		redirect( base_url("questionnaire") );
    	}
    }
    public function my_department( ){
        check_permission( $this->controller, 'my_department');

        $my_department_id = $this->session->userdata('department_id');
        $department = $this->department_model->get( $my_department_id);
        $data = array();
        $data['questionnaires'] = $this->questionnaire_model->get_complete_by_target_department_id( $my_department_id );
        $data['department'] = $department ; 
        $this->layout->view('my_department', $data );        

    }

}