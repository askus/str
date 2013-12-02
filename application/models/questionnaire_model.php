<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire_model extends CI_Model
{
/*
	status: 0-> incomplete, 1-> processed, 2-> complete
*/
	public $STATUS_INCOMPLETE= 0;
	public $STATUS_PROCESSED = 1;
	public $STATUS_COMPLETE = 2; 

	public function __construct()
    {
		parent::__construct();
		$this->load->model( "user_model");
		$this->load->model( "department_model");
		$this->load->helper( 'form');

	}


	public function delete_by_template_id( $template_id ){
		$questionnaires = $this->get_by_template_id( $template_id );

		//delete questionnaire_score
		foreach( $questionnaires as $questionnaire ){
			$this->delete( $questionnaire->questionnaire_id );
			//$this->db->delete("questionnaire_score", array( "questionnaire_id"=> $questionnaire->questionnaire_id  ));
		}
		//$this->db->trans_start();
		//$this->db->delete( "questionnaires", array( "template_id"=> $template_id));
	    //$this->db->trans_complete();
	}
	public function form2questionnaire( $questionnaire_form, $questionnaire_score_form ){
		/**!!!!!!!**/
		//basic information
		$questionnaire = $this->get_with_question_score( $questionnaire_form['questionnaire_id']);

		$questionnaire->template_id = $questionnaire_form['template_id'];
		//$questionnaire->date = $questionnaire_form['date'];
		$questionnaire->from_date = $questionnaire_form['from_date'];
		$questionnaire->to_date = $questionnaire_form['to_date'];
		$questionnaire->target_department_id = $questionnaire_form['target_department_id'];
		$questionnaire->last_modified_user_id = $questionnaire_form['last_modified_user_id'];
		$questionnaire->executor = $questionnaire_form['executor'];

		// build tmp mapping from question_id2question 
		$question_id2question = array();
		foreach( $questionnaire->sections  as $section ){
			foreach( $section->questions as $question ){
				$question_id2question[ $question->question_id ] = $question ;
			}
		}

		//$questionnaire_score_is_not_null_array = trans_checkbox_array( $questionnaire_score_form['is_not_null']);

		// update questionnaire_score 
		for( $i=0; $i < count( $questionnaire_score_form['true_false_null'] ); $i++ ){
			$true_false_null = $questionnaire_score_form['true_false_null'][$i];
			if( $true_false_null <0 ){
				$is_null= true;
				$score = null;
			}else if( $true_false_null == 0 ){
				$is_null= false; 
				$score = 0;
			}else if( $true_false_null == 1 ){
				$is_null = false;
				$score = 1;
			}
			//$is_null = !$questionnaire_score_is_not_null_array[$i];
			$question_id = $questionnaire_score_form['question_id'][$i];
			//$score = $questionnaire_score_form['score'][$i];
			$comment = $questionnaire_score_form['comment'][$i];

			$question = $question_id2question[ $question_id ] ;
			$question->score->is_null = $is_null;
			$question->score->score = $score; 
			$question->score->comment = $comment ;  
		} 

		return $questionnaire; 
	}

	// create new questionnaire by 
	public function add_by_labor_division( $template_id, $assigned_user_id, $target_department_id ){
		$questionnaire_id = $this->add( 0, $template_id, $assigned_user_id, $target_department_id, date("Y-m-d H:i:s", now()),date("Y-m-d H:i:s", now()) ,"", date("Y-m-d H:i:s", now()), null);
		
		$this->load->model("template_model");
		$template = $this->template_model->get( $template_id );

		foreach( $template->sections as $section ){
			//print_r( $section);
			//echo "<br><br>";
			foreach( $section->questions as $question ){

				if( !$section->is_all_comment ){
					$this->add_questionnaire_score( $questionnaire_id, $question->question_id );
				}else{
					$this->add_questionnaire_score( $questionnaire_id, $question->question_id, null, "", false);
				}
			}
		} 
	}

	//create by new template 
	public function add_from_template( $template ){
		foreach( $template->labor_division as $labor_division ){
			$assined_user_id = $labor_division['assigned_user_id'];
			$target_department_id = $labor_division['target_department_id'];
			
			$this->add_by_labor_division( $template->template_id, $assined_user_id, $target_department_id );
/*
			$questionnaire_id = $this->add( 0 , $template->template_id, $assined_user_id, $target_department_id, date("Y-m-d H:i:s") ,"", date("Y-m-d H:i:s", time()), null);
		
			//initialize questionnaire_score for this 
			foreach( $template->sections as $section  ){
				foreach( $section->questions as $question ){
					if( !$section->is_all_comment ){
						$this->add_questionnaire_score( $questionnaire_id, $question->question_id );
					}else{
						$this->add_questionnaire_score( $questionnaire_id, $question->question_id, null, "", false);
					}
				}
			}
*/
		}
	}


	public function update_questionnaire_score( $questionnaire_score ){


		$data = array( 'is_null' => $questionnaire_score->is_null,
						'comment' => $questionnaire_score->comment,
						'score' => $questionnaire_score->score  );

		//update 
		$this->db->update('questionnaire_score', $data , 
			array( 'questionnaire_id'=> $questionnaire_score->questionnaire_id,
			 "question_id"=> $questionnaire_score->question_id )
		);

	}

	// update questionnaire 
	public function update( $questionnaire ){
		$data = array( 
					'template_id'=> $questionnaire->template_id,
					'from_date' => $questionnaire->from_date,
					'to_date' => $questionnaire->to_date, 
					'target_department_id' => $questionnaire->target_department_id,
					'executor' => $questionnaire->executor , 
					'assigned_user_id' => $questionnaire->assigned_user_id,
					'status' => $questionnaire->status,
					'last_modified_datetime' => $questionnaire->last_modified_datetime,
					'last_modified_user_id' => $questionnaire->last_modified_user_id 
				);
		$this->db->update('questionnaires', $data, array('questionnaire_id' => $questionnaire->questionnaire_id)  );

		// update questionnaire score
		foreach( $questionnaire->sections as $section ){
			foreach( $section->questions as $question ){

				$this->update_questionnaire_score( $question->score );
			}
		}
	}

	// initialize questionnaire_score 
	public function add_questionnaire_score( $questionnaire_id, $question_id , $score=null, $comment="",$is_null= true ){
		$data = array( "questionnaire_id" => $questionnaire_id,
						 "question_id" => $question_id,
						 "score" => $score,
						 "comment" => $comment ,
						 "is_null" => $is_null );
		$this->db->insert("questionnaire_score", $data );
	}

	//public function add_score( $question) 

	public function add( $status,  $template_id, $assined_user_id, $target_department_id, $from_date, $to_date, $executor, $last_modified_datetime, $last_modified_user_id ){
		$data = array(
			'status' => $status, 
			'template_id' => $template_id,
			'assigned_user_id' => $assined_user_id,
			'target_department_id' => $target_department_id,
			'from_date' => $from_date,
			'to_date' => $to_date, 
			'executor' => $executor ,
			'last_modified_datetime' => $last_modified_datetime,
			'last_modified_user_id' => $last_modified_user_id
		);
		$this->db->insert( 'questionnaires', $data ); 
		$questionnaire_id = $this->db->insert_id();
		return $questionnaire_id;
	}
	public function get_by_template_id( $template_id ){
		$query = $this->db->get_where( "questionnaires", array( "template_id"=>$template_id ) );
		$tmp_questionnaires = $query->result();
		
		$ret_questionnaires = array();
		foreach( $tmp_questionnaires as $tmp_questionnaire ){
			$ret_questionnaires[] = $this->get( $tmp_questionnaire->questionnaire_id );
		}
		return $ret_questionnaires;  
	}
	public function get( $questionnaire_id ){
		$query = $this->db->get_where( "questionnaires", array("questionnaire_id"=> $questionnaire_id ));
		$ret = $query->row();
		$ret->assigned_user = $this->user_model->get( $ret->assigned_user_id );
		if( !is_null( $ret->last_modified_user_id)  ){
			$ret->last_modified_user = $this->user_model->get( $ret->last_modified_user_id );
		}else{
			$ret->last_modified_user = null;
		}	
		$ret->target_department = $this->department_model->get( $ret->target_department_id );
		// get template year, month, title 
		$query = $this->db->get_where( "templates" , array("template_id"=> $ret->template_id ));
		$tmp_template = $query->row();
		$ret->month = $tmp_template->month;
		$ret->year = $tmp_template->year;
		$ret->title = $tmp_template->title;

		return $ret;
	}
	public function get_with_question_score( $questionnaire_id){
		$ret_questionnaire = $this->get( $questionnaire_id);


		$sections = $this->db->from( "sections")
							->where( "template_id" , $ret_questionnaire->template_id  )
							->order_by("sections.section_order")
							->get()->result();
		$ret_questionnaire->sections = $sections ; 
		// add questions
		foreach( $ret_questionnaire->sections as $section ){
			$questions = $this->db->from( "questions" )
									->where( "template_id",  $ret_questionnaire->template_id )
									->where( "section_id", $section->section_id )
									->order_by("questions.question_order")
									->get()->result();
			$section->questions = $questions;
			foreach( $section->questions as $question ){
				$question_score = $this->db->get_where( "questionnaire_score", array( "questionnaire_id"=> $questionnaire_id, "question_id"=> $question->question_id  ))->row();
				$question->score = $question_score ;
			}
		}
		return $ret_questionnaire;
	}
	public function get_by_user_id( $user_id ){
		$tmp_questionnaires = $this->db->get_where("questionnaires", array("assigned_user_id"=>$user_id))->result(); 
		$ret_questionnaires = array();
		foreach( $tmp_questionnaires as $tmp_questionnaire ){
			$ret_questionnaires[] = $this->get_with_question_score( $tmp_questionnaire->questionnaire_id );
		}
		return $ret_questionnaires;
	}

	public function delete( $questionnaire_id){
		
		$this->db->delete('questionnaires', array('questionnaire_id' => $questionnaire_id ));
        $this->db->delete('questionnaire_score', array('questionnaire_id'=>$questionnaire_id));
	}
}