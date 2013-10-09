<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class News_model extends CI_Model
{
    private $news_table = 'news';

    public function __construct()
    {
        parent::__construct();
    }

    /* 取得所有公告 */
    public function get_all()
    {
        $news = $this->db->order_by('news_id', 'desc')->get($this->news_table)->result();

        return $news;
    }

    /* 取得一筆公告 */
    public function get($news_id)
    {
        $data = $this->db->get_where($this->news_table, array('news_id' => $news_id))->row();

        return $data;
    }

    /* 新增公告 */
    public function add($content)
    {
        $data = array(
            'date' => date("Y-m-d"),
            'user_id'   => $this->session->userdata('user_id'),
            'content'   => $content
        );
        $query = $this->db->insert($this->news_table, $data);

        return $query;
    }

    /* 更新公告 */
    public function update($news_id, $content)
    {
        $data = array(
            'user_id'   => $this->session->userdata('user_id'),
            'content'   => $content
        );
        $query = $this->db->update($this->news_table, $data, array('news_id' => $news_id));

        return $query;
    }

    /* 刪除公告 */
    public function delete($news_id)
    {
        $query = $this->db->delete($this->news_table, array('news_id' => $news_id));

        return $query;
    }

    /* 稽催訊息 */
    public function claim_message($dep_id)
    {
        $this->db->from('reports')
                 ->join('report_ym', 'report_ym.ym_id = reports.ym_id')
                 ->where('dep_id', $dep_id)
                 ->where('status <>', 3)
                 ->where('year*100+month <', date('Ym'), false);
        $data_num = $this->db->get()->num_rows();

        return ($data_num > 0) ? true : false;
    }
}