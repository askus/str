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
    }
    public function create(){
    	$data = array();
    	$data['action'] = '建立';
    	$data['users'] = $this->user_model->get_all();

    
    	$this->layout->view('add_template_form', $data);
    }

}
