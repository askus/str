<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Question{
	public function __construct( $_question_title, $_order, $has_score, $has_comment ){
		$this->question_title = $_content;
		$this->order = $_order;

	}
	public $question_title;
	public $order;  
	public $has_score; 
	public $has_comment ;
}

class Section{
	public function __construct( $_section_title, $_order ){
		$this->section_title = $_section_title;
		$this->order = $_order; 
		$this->questions = array();
	} 
	public $questions;
	public $section_title; 
	public $order; 
}

class Template_model extends CI_Model
{
	public $template_table = "template";
	public $template_section_table = "template_section";
	public $section_table = "sections";
	public $template_question_table ="template_question";
	public $question_table = "questions";


	public $sections ;
	public $year;
	public $month; 
	public $user_id ;

	public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultTemplate(){

    }

    public function get($template_id ){
        $this->db->from($this->template_table)
                 ->join($this->template_section_table, "{$this->template_section_table}.template_id = {$this->template_table}.template_id")
                 ->join($this->section_table, "{$this->section_table}.section_id = {$this->template_section_table}.section_id")
                 ->join($this->template_question_table, "{$this->template_question_table}.template_id = {$this->template_table}.template_id")
				 ->join($this->question_table,"{$this->question_table}.template_id = {$this->template_table}.template_id")
                 ->where("{$this->template_table}", array( "template_id"=>$template_id ) )
                 ->order_by('section_order, question_order', 'asc');
        $raw_questions = $this->db->get()->result();
        
        $tmp_section_array = array();

        // need to conver to self sections;
        $tmp_template = $this->db->get_where($this->template_table, array( "template_id" => $template_id ) )->result();
        $retTemplateObj = new Template_model();
        $retTemplateObj->template_id = $tmp_template->template_id; 
        $retTemplateObj->year = $tmp_template->year; 
        $retTemplateObj->month = $tmp_template->month;
        $retTemplateObj->user_id = $tmp_template->user_id ;

        $retTemplateObj->sections = array();
        $previousSectionId= -1; 
        foreach( $raw_questions as $rq ){
            $question = new Question( $rq->question_title , $rq->order, $rq->has_score, $rq->has_comment );
            
            if( $rq->section_id )

            if(!array_key_exists($rq->section_id,$tmp_section_array)){
                $tmp_section_array[ $rq->section_id] = array();
            }
            $tmp_section_array[ $rq->section_id][] = $question ; 
        }


        foreach( $tmp_section_array as )


        return $users;


    }
    public function insert_one( $template_id ){

    }
    public function update_one( $template_id){
    	
    }
}