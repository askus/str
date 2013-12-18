<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template extends CI_Controller
{
	private $controller;

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->controller = strtolower(__CLASS__);
        $this->load->model('template_model');
        $this->load->model('user_model');
        $this->load->model('department_model');
        $this->load->helper('menu_helper');
    }
    public function index( $selected_year = 0,  $selected_template_id = 0 ){
        check_permission($this->controller, 'view');
        $data = array();
        $users = $this->user_model->get_users_by_role_id(2);
        $departments = $this->user_model->get_department_list();

        //$selected_template_id= $this->input->get("tid");


        if( $selected_year== 0){
            $data['templates'] = $this->template_model->get_all();
        }else{
            $data['templates'] = $this->template_model->get_by_year( $selected_year);
        }
        $data['selected_year'] = $selected_year ;
        $data['selected_template_id']=$selected_template_id;
       // $data['selected_template_id'] = $selected_template_id;
        $data['years'] = $this->template_model->get_years();
        $data['users'] = $users;
        $data['departments'] = $departments;
        $this->layout->view( 'index_template', $data );
    }
    public function add(){
        check_permission($this->controller, 'add');

    	$data = array();
    	$data['action'] = '建立';
    	$data['users'] = $this->user_model->get_users_by_role_id( 2 );
        $data['template'] = $this->template_model->get_default_template();
        $data['departments'] = $this->department_model->get_all_department() ;
    	$this->layout->view('add_template_form', $data);
    }
    public function ajax_delete( ) {

        check_permission( $this->controller,'delete');

        $template_id = $this->input->post("template_id", true );
        if( $this->template_model->delete( $template_id )) {
            echo '{ "status":"ok" , "errMsg":""}';
        }else{
            echo '{ "status":"fault" , "errMsg":""}';
        }
        //redirect( base_url('template'));
    }

    public function view_blank( $template_id ){
        check_permission( $this->controller,'view_blank');
        $template = $this->template_model->get( $template_id );
        $data = array();
        $data['template'] = $template ;
        $data['css']= array('view-questionnaire.css');
        $data['print_css'] = array('view-questionnaire.css');
        $this->load->view( 'view_blank', $data );
    }

    public function ajax_add(){
        check_permission($this->controller, 'add');

        $data = array(); 
        
        $template_form = $this->input->post('template', true);
        $sections_form = $this->input->post('sections', true);
        $questions_form = $this->input->post('questions', true );
        $labor_division_form = $this->input->post('labor_division', true);
        // converting to object 
        $template = $this->template_model->form2template( $template_form, $sections_form, $questions_form, $labor_division_form );
        

        if( $template->template_id == false ){
            $this->template_model->insert_template( $template );
        }else{
            $this->template_model->update_template( $template );
        }

        echo '{ "status": "ok", "errMsg": "" }';

        //
        /*
        if ($news_id == false) { // 新增
            $result = $this->news_model->add($input_content);
            echo ($result == true) ? 'ok' : 'error';
        }
        else { // 編輯
            $result = $this->news_model->update($news_id, $input_content);
            echo ($result == true) ? 'ok' : 'error';
        }
        */
    }

}
