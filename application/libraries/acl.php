<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Acl {

    private $CI;
    private $acl;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->CI->load->config('acl', true);
        $this->acl = $this->CI->config->item('permission', 'acl');
    }

    public function has_permission($controller, $required_permissions = array(), $own = null)
    {
        $user_id = $this->CI->session->userdata('user_id');
        $user_role = $this->CI->session->userdata('role_id');
        $dep_id = $this->CI->session->userdata('department_id');

        if (!$user_id || !$user_role) {
            return false;
        }

        if (!is_array($required_permissions)) {
            $required_permissions = explode(',', $required_permissions);
        }

        foreach ($this->acl[$controller] as $action => $roles) {
            if (in_array($action, $required_permissions) && ($roles & $user_role)) {
                if ($own) {
                    if ($own != null && $own != $dep_id)
                        return false;
                }

                return true;
            }
        }

        return false;
    }

    public function is_logged_in()
    {

        $logged_in = $this->CI->session->userdata('logged_in');

        if ($logged_in) {
            return true;
        }
        return false;
    }


}

/* End of application/libraries/acl.php */