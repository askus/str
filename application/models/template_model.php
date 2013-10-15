<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Question{
	public function __construct( $_question_id, $_question_title, $_question_order, $_has_score, $_has_comment ){
		$this->question_id = $_question_id;
        $this->question_title = $_question_title;
		$this->question_order = $_question_order;
        $this->has_score = $_has_score;
        $this->has_comment = $_has_comment;
	}
    public $question_id ; 
	public $question_title;
	public $question_order;  
	public $has_score; 
	public $has_comment ;
}

class MY_Section{
	public function __construct( $_section_id , $_section_title, $_section_order ){
		$this->section_id = $_section_id;
        $this->section_title = $_section_title;
		$this->section_order = $_section_order; 
		$this->questions = array();
	} 
    public $section_id ; 
	public $questions;
	public $section_title; 
	public $section_order; 
}

class MY_Template{
    public function __construct(){
        $this->sections = array();
        $this->assigned_ids = array();
        $this->labor_division = array();
        //$this->sections[] = 1; 
        //print_r( $this->sections );
    }
    public $sections ;
    public $year;
    public $month; 
    public $user_id ;
    public $title;
    public $template_id;
    public $assigned_ids;
    public $labor_division; 
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
        $this->load->model('questionarry_model');
    }


    public function trans_checkbox_array( $checkbox_array ){
        $ret = array();
        $to_add = false;
        for( $i= 0 ; $i< count( $checkbox_array); $i++){
            if( $checkbox_array[$i] == 1){
                $to_add = true;
            }else{
                $ret[] = $to_add;
                $to_add = false;
            }
        }
        return $ret ; 
    }

    public function form2template( $template_form , $sections_form, $questions_form, $labor_division_form ){
        
        // template 
        $template= new My_Template();
        $template->template_id = $template_form['template_id'];
        $template->title = $template_form['title'];
        $template->year = $template_form['year'];
        $template->month = $template_form['month'];

        // template user 
        array_merge( $template->assigned_ids , $template_form['assigned_ids'] ) ; 

        // sections 
        $tmp_section_order2section = array();
        for( $i =0 ; $i < count( $sections_form['section_id'] ) ; $i++){

            $section = new MY_Section(
                $sections_form['section_id'][$i],
                $sections_form['section_title'][$i],
                $sections_form['section_order'][$i] 
            );

            $template->sections[] = $section ;         

            $tmp_section_order2section[ $section->section_order ] = $section ;
        }

        // questions 
        for( $i = 0 ; $i < count( $questions_form['question_id'] ); $i++){
            $has_score_array = $this->trans_checkbox_array( $questions_form['has_score']) ; 
            $has_comment_array = $this->trans_checkbox_array( $questions_form['has_comment'] ) ;

            $question = new MY_Question(
                $questions_form['question_id'][$i],
                $questions_form['question_title'][$i],
                $questions_form['question_order'][$i],
                $has_score_array[$i],
                $has_comment_array[$i]
            );
            $tmp_section_order2section[ $questions_form['section_order'][$i]]->questions[] = $question ;
        }

        // labor division
        for( $i= 0 ; $i< count( $labor_division_form['assigned_user_ids']) ; $i++){
            $assigned_user_id = $labor_division_form['assigned_user_ids'][$i];
            $target_department_id = $labor_division_form['target_department_ids'][$i];
            $this->labor_division[] = array( $assigned_user_id, $target_department_id );
        }

        return $template; 
    }

    public function get_default_template(){
        $retTemplateObj = new My_Template();
        $retTemplateObj->title= "服務稽核評分表";
        $section_ids = range( 1, 8); 
        $section2question = array(1=>range(1,27 ), 2=>range( 28, 47), 3=>range( 48, 65), 4=> range( 66, 75), 5=> range( 76, 80), 6=> range( 82,82), 7=>range( 83, 83 ), 8=>range( 84,85) );
        $i =0;
        foreach( $section_ids as $section_id ){
            $section = $this->db->get_where( $this->section_table,array("section_id"=>$section_id ))->row();
            //print_r( $section );

            //$tmp = new MY_Section( $section->section_id , $section->section_title, $i );
            $tmp = new MY_Section( NULL , $section->section_title, $i );
            $retTemplateObj->sections[] = $tmp;
            $question_order= 1;
            if( $section_id < 6){
                $question_has_comment= true;
                $question_has_score = true ;
            }else{
                $question_has_comment= true;
                $question_has_score = false ;                
            }
            foreach( $section2question[ $section_id ] as $question_id ){
                $question = $this->db->get_where( $this->question_table,  array("question_id"=> $question_id))->row();  
                $retTemplateObj->sections[ $i ]->questions[]= new MY_Question( NULL, $question->question_title, $question_order, $question_has_score, $question_has_comment ) ;
                $question_order +=1 ;
            }
            $i +=1;
        }

        return $retTemplateObj; 
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
        
        $tmp_template = $this->db->get_where($this->template_table, array( "template_id" => $template_id ) )->result();
        $retTemplateObj = new My_Template();
        $retTemplateObj->template_id = $tmp_template->template_id; 
        $retTemplateObj->year = $tmp_template->year; 
        $retTemplateObj->month = $tmp_template->month;
        $retTemplateObj->user_id = $tmp_template->user_id ;

        $retTemplateObj->sections = array();
        $previous_section_id= -1; 
        $i = 0;
        foreach( $raw_questions as $rq ){
            $question = new MY_Question( $rq->question_title , $rq->question_order, $rq->has_score, $rq->has_comment );
            
            if( $rq->section_id != $previous_section_id ){
                $retTemplateObj->sections[] = new MY_Section( $rq->section_id,  $rq->section_title, $rq->section_order );
                $i +=1;
                $previous_section_id = $rq->section_id; 
            }
            $retTemplateObj->sections[$i]->questions[] = $question; 

        }
        return $retTemplateObj; 
    }

    public function insert_template( $template ){
        $data = array( 
            "year" => $template->year,
            "month" => $template->month,
            "user_id" => $template->user_id,
            "title" => $this->db->escape( $template->title  ),
        );
        // insert template basic information 
        $this->db->trans_start();
        $this->db->insert( $this->template_table , $data );
        $this->db->trans_complete();
        // set back template id 
        $template_id = $this->db->insert_id();
        $template->template_id = $template_id; 
        // insert sections 
        foreach( $template->sections as $section  ){
            $section_id = insert_section( $section );
        }
        // connect template, section, question   
        foreach( $template->sections as $section ){
            insert_template_section( $template, $section );
            foreach( $section->questions as $question ){
                insert_template_question( $template, $section, $question ); 
            }
        }
    }
    private function insert_template_section( $template, $section ){
        $data = array( 
            "template_id" => $template->template_id,
            "section_id" => $section->section_id ,
            "section_order" => $section->section_order 
        );
        $this->db->trans_start();
        $this->db->insert( $this->template_section_table , $data );
        $this->db->trans_complete();
    }
    private function insert_template_question( $template, $section, $question ){
        $data = array( 
            "template_id" => $template->template_id ,
            "section_id" => $section->section_id ,
            "question_id" => $question->question_id,
            "question_order" => $question->question_order 
        );
        $this->db->trans_start();
        $this->db->insert( $this->template_question_table , $data );
        $this->db->trans_complete();
    }

    public function insert_section( $section ){
        $data = array( 
            "section_title" => $this->db->escape( $section->section_title) , 
        );
        // insert section bsaic information 
        $this->db->trans_start();
        $this->db->insert( $this->section_table , $data );
        $this->db->trans_complete();
        // set back section id 
        $section_id = $this->db->insert_id();
        $section->section_id = $section_id ;
        // insert question 
        foreach( $sections->questions as $question ){
            $question_id = insert_question( $question );
        }
    }
    public function insert_question( $question ){
        $data = array(
            "question_title" => $this->db->escape( $question->question_title ) , 
            "question_order" => $question->question_order, 
            "has_score" => $question->has_score,
            "has_comment" => $question->has_comment 
        );
        // insert question basic information 
        $this->db->trans_start();
        $this->db->insert( $this->question_table, $data );
        $this->db->trans_complete();
        // set back question id
        $question_id = $this->db->insert_id();
        $question->question_id = $question_id ;
        return $question->question_id ; 
    }

    public function update_template( $template ){


    }
    public function update_section( $section ){

    } 
    public function update_question( $question ){

    }


    public function insert_one( $template_id ){

    }
    public function update_one( $template_id){
    	
    }
}