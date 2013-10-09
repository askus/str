<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $user_table = 'users';
    private $dep_table = 'departments';
    private $role_table = 'roles';

    public function __construct()
    {
        parent::__construct();
    }

    /* 取得所有使用者 */
    public function get_all()
    {
        $this->db->from($this->user_table)
                 ->join($this->dep_table, "{$this->dep_table}.department_id = {$this->user_table}.department_id")
                 ->join($this->role_table, "{$this->role_table}.role_id = {$this->user_table}.role_id")
                 ->order_by('user_id', 'asc');
        $users = $this->db->get()->result();
        return $users;
    }

    public function get_role_list(){
        $roles = $this->db->from($this->role_table)->order_by('role_id', 'desc')->get()->result();
        return $roles; 
    }

    /* 取得單位列表 */
    public function get_department_list()
    {
        $departments = $this->db->get($this->dep_table)->result();

        return $departments;
    }

    /* 取得一位使用者 */
    public function get($user_id)
    {
        $data = $this->db->get_where($this->user_table, array('user_id' => $user_id))->row();

        return $data;
    }

    /* 新增使用者 */
    public function add($inputData)
    {
        $data = array(
            'name'     => $inputData['name'],
            'account'  => $inputData['account'],
            'password' => hash( "sha256", $inputData['password']),
            'email'    => $inputData['email'],
            'department_id'   => $inputData['department_id'],
            'role_id'    => $inputData['role_id'] 
        );
        /*(isset($inputData['role_id'])) ? 1 : 2*/
        $query = $this->db->insert($this->user_table, $data);

        return $query;
    }

    /* 更新使用者 */
    public function update($user)
    {
        $data = array(
            'name'   => $user['name'],
            'email'  => $user['email'],
            'department_id' => $user['department_id'],
            'role_id'    => $user['role_id'] 
        );
        if (!empty($user['password'])) {
            $data['password'] = hash( "sha256",  $user['password']);
        }
        $query = $this->db->update($this->user_table, $data, array('user_id' => $user['user_id']));

        return $query;
    }

    /* 刪除使用者 */
    public function delete($user_id)
    {
        $query = $this->db->delete($this->user_table, array('user_id' => $user_id));

        return $query;
    }

    /* 檢查使用者是否存在 */
    public function isExist($account)
    {
        $data = $this->db->get_where($this->user_table, array('account' => $account))->row();

        return (count($data) > 0) ? true : false;
    }

    /* 取得使用者資料 */
    public function get_user_profile($user_id)
    {
        $this->db->from('users')
                 ->join('departments', 'departments.department_id = users.department_id')
                 ->where('user_id', $user_id);
        $user = $this->db->get()->row();

        return $user;
    }
}