<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller
{
    private $controller;
    private $dep_id;

    public function __construct()
    {
        parent::__construct();
        check_login();

        //echo "PASS LOGIN";

        $this->controller = strtolower(__CLASS__);
        $this->dep_id = $this->session->userdata('department_id');
        $this->load->model('news_model');
    }

    /* 公告首頁 */
    public function index()
    {
        check_permission($this->controller, 'view');

        $data['news'] = $this->news_model->get_all();
        //$data['claim'] = $this->news_model->claim_message($this->dep_id);
        $data['is_addable'] = false;
        if( $this->session->userdata('role_id') ==1 ){ $data['is_addable']= true; }

        $this->layout->view('news', $data);
    }

    /* 新增、編輯公告 */
    public function ajax_add_news()
    {
        check_permission($this->controller, 'add');

        $news_id = $this->input->post('newsID', true);
        $input_content = $this->input->post('inputContent', true);

        if ($news_id == false) { // 新增
            $result = $this->news_model->add($input_content);
            echo ($result == true) ? 'ok' : 'error';
        }
        else { // 編輯
            $result = $this->news_model->update($news_id, $input_content);
            echo ($result == true) ? 'ok' : 'error';
        }
    }

    /* 刪除公告 */
    public function ajax_delete_news()
    {
         check_permission($this->controller, 'delete');

        $news_id = $this->input->post('newsID', true);
        $result = $this->news_model->delete($news_id);
        echo ($result == true) ? 'ok' : 'error';
    }

    /* 取得指定的公告 */
    public function ajax_get_news()
    {
        check_permission($this->controller, 'view');

        $news_id = $this->input->post('newsID', true);
        $news = $this->news_model->get($news_id);

        $data = array();
        if (count($news) > 0) {
            $data['status']  = 'ok';
            $data['nid']     = $news->news_id;
            $data['content'] = $news->content;
        }
        else {
            $data['status']  = 'error';
        }

        echo json_encode($data);
    }
}
