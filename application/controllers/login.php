<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function index()
    {
        if (!$this->acl->is_logged_in()) {
            $this->load->view('login');
        }
        else {
            redirect('/');
        }
    }

    public function ajax_check_login()
    {
        $inUsername = trim($this->input->post('inputUsername', true));
        $inPassword = trim($this->input->post('inputPassword', true));

        $status = '';
        $message = '';

        if (empty($inUsername) || empty($inPassword)) {
            $status  = 'error';
            $message = '帳號或密碼不可空白';
        }
        else {
            $query = $this->db->join('departments', 'departments.department_id = users.department_id')
                              ->get_where('users', array('account' => $inUsername, 'password' =>  hash( "sha256", $inPassword)));

            // 註冊 session
            if ($query->num_rows() > 0) {
                $user = $query->row();
                $data = array(
                    'user_id'   => $user->user_id,
                    'user_name' => $user->name,
                    'department_id'    => $user->department_id,
                    'department_name'  => $user->department_name,
                    'email'     => $user->email,
                    'role_id'      => $user->role_id,
                    'logged_in' => true
                );
                $this->session->set_userdata($data);

                $status = 'ok';
            }
            else {
                $status  = 'error';
                $message = '帳號或密碼不正確';
            }
        }
        //print_r( $this->session->userdata('logged_in') );

        echo json_encode(array('status' => $status, 'message' => $message));
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }
}
