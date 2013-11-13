<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Question{
	/*
    public function __construct( $_question_id, $_question_title, $_question_order, $_has_score, $_has_comment ){
		$this->question_id = $_question_id;
        $this->question_title = $_question_title;
		$this->question_order = $_question_order;
        $this->has_score = $_has_score;
        $this->has_comment = $_has_comment;
	}
    public function __construct( $_question_title, $_question_order, $_has_score, $_has_comment ){
        $this->question_title = $_question_title;
        $this->question_order = $_question_order;
        $this->has_score = $_has_score;
        $this->has_comment = $_has_comment;   
    } */
    public $question_id ; 
	public $question_title ;
	public $question_order ;  
	public $has_score ; 
	public $has_comment ;
    public $template_id ;
    public $section_id;
}

class MY_Section{

	public function __construct( ){
        $this->questions = array();
    }
    /*
    public function __construct( $_section_id , $_section_title, $_section_order ){
		$this->section_id = $_section_id;
        $this->section_title = $_section_title;
		$this->section_order = $_section_order; 
		$this->questions = array();
	} */
    public $section_id ; 
	public $section_title; 
	public $section_order; 
    public $template_id ;
    public $questions;
    public $is_all_comment;
}

class MY_Template{
    public function __construct(){
        $this->sections = array();
        $this->assigned_ids = array();
        $this->labor_division = array();
        $this->questionnaires = array();
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
    public $questionnaires;
}

class Template_model extends CI_Model
{
	public $template_table = "templates";

	public $template_section_table = "template_section";
	public $section_table = "sections";
	public $default_section_table ="default_sections";
    public $template_question_table ="template_question";
	public $question_table = "questions";
    public $default_question_table = "default_questions"; 
    public $questionnaire_table = "questionnaires"; 
    public $questionnaire_score_table = "questionnaire_score" ; 

	public $sections ;
	public $year;
	public $month; 
	public $user_id ;

	public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->model('user_model');
        $this->load->model('department_model');

        $this->load->helper('form');
    }

    public function delete( $template_id ){
        // delete questionarry 
        $this->questionnaire_model->delete_by_template_id( $template_id );

        $this->db->trans_start();
        $this->db->delete( $this->template_table ,array( "template_id"=>$template_id) );
        $this->db->delete( $this->question_table, array( "template_id"=>$template_id) );
        $this->db->delete( $this->section_table, array( "template_id"=>$template_id) );
        $this->db->trans_complete();

        return true ;
    }


    public function form2template( $template_form , $sections_form, $questions_form, $labor_division_form ){
        
        // template 
        $template= new My_Template();
        $template->template_id = $template_form['template_id'];
        $template->title = $template_form['title'];
        $template->year = $template_form['year'];
        $template->month = $template_form['month'];
        $template->user_id = $template_form['user_id'];
        
        // sections 
        $tmp_section_order2section = array();
        for( $i =0 ; $i < count( $sections_form['section_id'] ) ; $i++){

            $section = new MY_Section(); 
            $section->section_id = $sections_form['section_id'][$i];
            $section->section_title = $sections_form['section_title'][$i];
            $section->section_order = $sections_form['section_order'][$i];  
            $section->is_all_comment = $sections_form['is_all_comment'][$i];
            $section->template_id = $template->template_id ;

            $template->sections[] = $section ;         

            $tmp_section_order2section[ $section->section_order ] = $section ;
        }

        // questions 
        for( $i = 0 ; $i < count( $questions_form['question_id'] ); $i++){
            $has_score_array = trans_checkbox_array( $questions_form['has_score']) ; 
            $has_comment_array = trans_checkbox_array( $questions_form['has_comment'] ) ;

            $question = new MY_Question();
            $question->question_id = $questions_form['question_id'][$i];
            $question->question_title = $questions_form['question_title'][$i];
            $question->question_order = $questions_form['question_order'][$i];
            $question->template_id = $template->template_id ; 
            $question->section_id = $questions_form['section_id'];
            $question->has_score = $has_score_array[$i];
            $question->has_comment = $has_comment_array[$i];

            $tmp_section_order2section[ $questions_form['section_order'][$i]]->questions[] = $question ;
        }

        // labor division
        for( $i= 0 ; $i< count( $labor_division_form['assigned_user_ids']) ; $i++){
            $assigned_user_id = $labor_division_form['assigned_user_ids'][$i];
            $target_department_id = $labor_division_form['target_department_ids'][$i];
            if( $target_department_id < 0 ){ continue; /* target_id < 0 means there does not exist such department */}
            $template->labor_division[] = array( "assigned_user_id"=>$assigned_user_id, "target_department_id"=>$target_department_id );
        }

        return $template; 
    }

    public function get_default_template(){
        $retTemplateObj = new My_Template();
        $retTemplateObj->title= '服務稽核評分表' ;
        $retTemplateObj->user_id = $this->session->userdata('user_id');
        $section_ids = range( 1, 8); 
        $section2question = array(1=>range(1,27 ), 2=>range( 28, 47), 3=>range( 48, 65), 4=> range( 66, 75), 5=> range( 76, 80), 6=> range( 82,82), 7=>range( 83, 83 ), 8=>range( 84,85) );
        $i =0;
        foreach( $section_ids as $section_id ){
            $section = $this->db->get_where( $this->default_section_table,array("section_id"=>$section_id ))->row();

            //$tmp = new MY_Section( $section->section_id , $section->section_title, $i );
            //$tmp = new MY_Section( NULL , $section->section_title, $i );
            $tmp = new MY_Section();
            $tmp->section_title = $section->section_title;
            $tmp->section_order= $i     ; 

            $question_order= 1;
            if( $section_id < 6){
                $question_has_comment= true;
                $question_has_score = true ;
                $tmp->is_all_comment = false;
            }else{
                $question_has_comment= true;
                $question_has_score = false ;
                $tmp->is_all_comment = true;                
            }
            $retTemplateObj->sections[] = $tmp;

            foreach( $section2question[ $section_id ] as $question_id ){
                $question = $this->db->get_where( $this->default_question_table,  array("question_id"=> $question_id))->row();  
                
                $tmp_Question = new MY_Question();
                $tmp_Question->question_title = $question->question_title ;
                $tmp_Question->question_order = $question_order ; 
                $tmp_Question->has_score = $question_has_score; 
                $tmp_Question->has_comment = $question_has_comment;
                $retTemplateObj->sections[ $i ]->questions[]=  $tmp_Question;
                $question_order +=1 ;
            }
            $i +=1;
        }

        return $retTemplateObj; 
    }

    public function get_years( ){
        $q = $this->db->distinct()->select('year')->from('templates')->order_by('year','desc');
        $tmp_results = $q->get()->result();
        $ret_years = array();
        foreach( $tmp_results as $tmp_result ){
            $ret_years[] = $tmp_result->year;
        }
        return $ret_years;
    }

    public function get_by_year( $year ){
        $query = $this->db->from( $this->template_table )->where('year', $year)->order_by('template_id','desc');
        $tmp_templates = $query->get()->result();
        $retTemplateArray= array();
        foreach( $tmp_templates as $tmp_template ){
            $retTemplateArray[] = $this->get( $tmp_template->template_id );
        }
        return $retTemplateArray ;
    }

    public function get_all($max_limit=  100){
        $query = $this->db->from( $this->template_table )->order_by('year desc, month desc')->limit( $max_limit);
        $tmp_templates  =  $query->get()->result();

        //print_r( $tmp_templates );
        $retTemplateArray = array();
        foreach( $tmp_templates as $tmp_template ){
            $retTemplateArray[] = $this->get( $tmp_template->template_id );
        }
        return $retTemplateArray;
    }

    public function get( $template_id ){
        //get template
        $ret_template = $this->db->get_where( "templates", array("template_id"=>$template_id))->row();
        $sections = $this->db->from("sections")->where("template_id", $template_id)->order_by("section_order", 'asc')->get()->result();
        $ret_template->sections = $sections ; 
        foreach( $ret_template->sections as $section ){
            $questions = $this->db->from('questions')->where('section_id', $section->section_id)->order_by("question_order",'asc')->get()->result();
            $section->questions = $questions ; 
        }
        $questionnaires = $this->questionnaire_model->get_by_template_id( $template_id);
        $ret_template->questionnaires = $questionnaires; 
        return $ret_template;
    }

    public function get_2($template_id ){
        /*
        $this->db->from($this->template_table)
                 ->join($this->template_section_table, "{$this->template_section_table}.template_id = {$this->template_table}.template_id")
                 ->join($this->section_table, "{$this->section_table}.section_id = {$this->template_section_table}.section_id")
                 ->join($this->template_question_table, "{$this->template_question_table}.template_id = {$this->template_table}.template_id")
				 ->join($this->question_table,"{$this->question_table}.question_id = {$this->template_question_table}.question_id")
                 ->where("{$this->template_table}.template_id", $template_id )
                 ->order_by('section_order, question_order', 'asc');
        */
        $this->db->from($this->template_table)
                 ->join($this->section_table, "{$this->section_table}.template_id = {$this->template_table}.template_id")
                 ->join($this->question_table,"{$this->question_table}.template_id = {$this->template_table}.template_id")
                 ->where("{$this->template_table}.template_id", $template_id )
                 ->order_by('section_order, question_order', 'asc');

        $raw_questions = $this->db->get()->result();
        
        $tmp_template = $this->db->get_where($this->template_table, array( "template_id" => $template_id ) )->row();
        //print_r( $tmp_template);
        $retTemplateObj = new My_Template();
        $retTemplateObj->template_id = $tmp_template->template_id; 
        $retTemplateObj->year = $tmp_template->year; 
        $retTemplateObj->month = $tmp_template->month;
        $retTemplateObj->user_id = $tmp_template->user_id ;
        $retTemplateObj->title = $tmp_template->title; 
        // sections
        $retTemplateObj->sections = array();
        $previous_section_id= -1; 
        $i = 0;
        foreach( $raw_questions as $rq ){
            // questions
            $question = new MY_Question();
            $question->question_title = $rq->question_title ;
            $question->question_order = $rq->question_order;
            $question->has_score = $rq->has_score ; 
            $question->has_comment = $rq->has_comment; 
            $question->section_id = $rq->section_id ; 
            $question->template_id = $rq->template_id ;

             //$rq->question_title , $rq->question_order, $rq->has_score, $rq->has_comment );
            
            if( $rq->section_id != $previous_section_id ){
                $tmp_section = new MY_Section();
                $tmp_section->section_id = $rq->section_id ;
                $tmp_section->section_title = $rq->section_title ;
                $tmp_section->section_order = $rq->section_order ;
                $tmp_section->is_all_comment = $rq->is_all_comment;
                $tmp_section->template_id = $rq->template_id ; 

                $retTemplateObj->sections[] = $tmp_section; 
                $i +=1;
                $previous_section_id = $rq->section_id; 
            }
            $retTemplateObj->sections[$i]->questions[] = $question; 
        }

        // questionnaire
        $questionnaires = $this->questionnaire_model->get_by_template_id( $template_id);
        $retTemplateObj->questionnaires = $questionnaires; 

        return $retTemplateObj; 
    }

    public function insert_template( $template ){
        $data = array( 
            "year" => $template->year,
            "month" => $template->month,
            "user_id" => $template->user_id,
            "title" =>  $template->title  
        );
        // insert template basic information 
        $this->db->trans_start();
        $this->db->insert( $this->template_table , $data );
        // set back template id 
        $template_id = $this->db->insert_id();
        $template->template_id = $template_id; 
        $this->db->trans_complete();

        // insert sections 
        foreach( $template->sections as $section  ){
            $section->template_id = $template->template_id ;
            $section_id = $this->insert_section( $section );
        }
        /*
        // connect template, section, question   
        foreach( $template->sections as $section ){
            $this->insert_template_section( $template, $section );
            foreach( $section->questions as $question ){
                $this->insert_template_question( $template, $section, $question ); 
            }
        }
        */

        // insert labor division 
        $this->questionnaire_model->add_from_template( $template );

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
            "section_title" => $section->section_title ,
            "section_order" => $section->section_order,
            "template_id" => $section->template_id,
            "is_all_comment" => $section->is_all_comment
        );
        // insert section bsaic information 
        $this->db->trans_start();
        $this->db->insert( $this->section_table , $data );
        // set back section id 
        $section_id = $this->db->insert_id();
        $section->section_id = $section_id ;
        $this->db->trans_complete();

        // insert question 
        foreach( $section->questions as $question ){
            $question->template_id = $section->template_id ; 
            $question->section_id = $section->section_id ;
            $question_id = $this->insert_question( $question );
        }
    }
    public function insert_question( $question ){
        $data = array(
            "question_title" => $question->question_title, 
            "has_score" => $question->has_score,
            "has_comment" => $question->has_comment,
            "section_id" => $question->section_id, 
            "question_order" => $question->question_order, 
            "template_id" => $question->template_id 
        );
        // insert question basic information 
        $this->db->trans_start();
        $this->db->insert( $this->question_table, $data );
        // set back question id
        $question_id = $this->db->insert_id();
        $question->question_id = $question_id ;
        $this->db->trans_complete();

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