<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
    private $controller;

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->controller = strtolower(__CLASS__);
        $this->load->model('user_model');
    }

    /* 帳號管理首頁 */
    public function index()
    {
        check_permission($this->controller, 'view');

        $data['users'] = $this->user_model->get_all();

        $this->layout->view('user_list', $data);
    }

    /* 新增帳號表單 */
    public function add()
    {
        check_permission($this->controller, 'add');

        $data['roles'] = $this->user_model->get_role_list(); 
        $data['departments'] = $this->user_model->get_department_list();
        $data['action'] = '新增';

        $this->layout->view('user_add_form', $data);
    }

    /* 新增使用者 */
    public function ajax_add_user()
    {
        check_permission($this->controller, 'add');

        $user = $this->input->post('user', true);

        if (empty($user['user_id'])) { // 新增
            if ($this->user_model->isExist($user['account'])) {
                echo 'exist';
            }
            else {
                $result = $this->user_model->add($user);
                echo ($result == true) ? 'ok' : 'error';
            }
        }
        else { // 編輯
            $result = $this->user_model->update($user);
            echo ($result == true) ? 'ok' : 'error';
        }
    }

    /* 編輯使用者 */
    public function edit($user_id)
    {
        check_permission($this->controller, 'edit');
    
        $data['roles'] = $this->user_model->get_role_list(); 
        $data['departments'] = $this->user_model->get_department_list();
        $data['user'] = $this->user_model->get($user_id);
        $data['action'] = '修改';

        $this->layout->view('user_add_form', $data);
    }

    /* 刪除使用者 */
    public function ajax_delete_user()
    {
        check_permission($this->controller, 'delete');

        $user_id = $this->input->post('userID', true);
        $result = $this->user_model->delete($user_id);
        echo ($result == true) ? 'ok' : 'error';
    }

    /* 更改密碼表單 */
    public function profile()
    {
        check_permission($this->controller, 'chpw');

        $user_id = $this->session->userdata('user_id');
        $data['roles'] = $this->user_model->get_role_list(); 
        $data['departments'] = $this->user_model->get_department_list();
        $data['user'] = $this->user_model->get_user_profile($user_id);

        $this->layout->view('user_change_pw', $data);
    }

    public function ajax_change_password()
    {
        check_permission($this->controller, 'chpw');

        $user = $this->input->post('user', true);
        check_user($user['user_id']);
        $user['role_id'] = $this->session->userdata('role_id');

        $result = $this->user_model->update($user);
        echo ($result == true) ? 'ok' : 'error';
    }
}
