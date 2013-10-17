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
    }
    public function index(){
        check_permission($this->controller, 'view');
        $data = array();
        $data['templates'] = $this->template_model->get_all();
       // print_r( $data );
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
    public function delete($template_id) {

        check_permission( $this->controller,'delete');
        $this->template_model->delete( $template_id );

        redirect( base_url('template'));
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
