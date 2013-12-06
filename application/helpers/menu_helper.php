<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function year_menu( $years, $selected_year, $menu_name ,$menu_class, $menu_id){
    $html = "<select class='{$menu_class}' name='{$menu_name}' id= '{$menu_id}' >";
    $html .= "<option value='0'>-------</option>";

    foreach( $years as $year ){
        $is_selected = "";
        if( $year == $selected_year ){ $is_selected ="selected";}
        $html .= "<option value='{$year}' {$is_selected}>{$year}</option>";
    }
    $html .= "</select>";
    return $html ;
}

function user_menu( $users, $menu_name, $menu_class, $menu_id, $selected_id= NULL ){

    $html = "<select class='{$menu_class}' name='". $menu_name ."' id='${menu_id}'>"; 
    $html .= "<option value='-1'>-------</option>";
    foreach( $users as $user ){
        $is_selected = "";
        if( $user->user_id == $selected_id ) {$is_selected ="selected"; }        
        $html .= "<option value='{$user->user_id}' ". $is_selected . ">{$user->name}</option>"; 
    }
    $html .= "</select>";
    return $html ;
}

function cal_process( $template ){
    $tmp_records = array();
    $tmp_records[0] = 0;
    $tmp_records[1] = 0;
    $tmp_records[2] = 0;
    foreach( $template->questionnaires as $questionnaire ){
        $tmp_records[ $questionnaire->status ] += 1;
    }
    $total = count( $template->questionnaires );

    $tmp_records[0] /= $total;//未填寫
    $tmp_records[1] /= $total;//填寫中
    $tmp_records[2] /= $total;//已填寫

    $tmp_records[0] *= 100;
    $tmp_records[1] *= 100;
    $tmp_records[2] *= 100;
    return $tmp_records;
}

function show_template_completenness( $template ){
    $tmp_records=cal_process( $template );
    return sprintf("完成度:%.0f%%", $tmp_records[2] ); 
}

function show_template_process( $template){
    $tmp_records = cal_process( $template );
    return sprintf( "未填寫: %.0f %%, 填寫中: %.0f %%, 已填寫: %.0f %%", $tmp_records[0], $tmp_records[1], $tmp_records[2] );
}

/* 顯示選單列 */
function show_menu()
{
    // 建立評分表, 檢視評分表, 填寫評分表, 分析評分表
    $CI = &get_instance();
    $user_role = $CI->session->userdata('role_id');
    //$saved_menu = $CI->session->userdata('menu');
    $curr_url = $CI->uri->segment(1);

    $menu = array(
        array("title"=>"系統公告",   "url"=>"news","role"=>3),
        array("title"=>"帳號管理", "url"=>"user","role"=>1),
        array("title"=>"建立評分表", "url"=>"template/add", "role"=>1 ),
        array("title"=>"檢視評分表", "url"=>"template/index", "role"=>1),
        array("title"=>"填寫評分表", "url"=>"questionnaire/index", "role"=>3),
        array("title"=>"分析評分表", "url"=>"questionnaire/analyze", "role"=>1)
    );

    // 建構選單 HTML
    $html = '<ul class="nav nav-list">';

    foreach ($menu as $m) {
            if( $m['role'] & $user_role   ){
                $html .= ($curr_url == $m['url'] )
                    ? '<li class="active"><a href="'. base_url($m['url'] ).'">'. $m['title'] .'</a></li>'
                    : '<li><a href="'. base_url( $m['url'] ) .'">' . $m['title'] .'</a></li>';
            }
    }

    $html .= '</ul>';

    return $html;
}

function true_false_null( $select_name ,  $questionnaire_score, $row = null ){    
    // determine selected option 
    if( $questionnaire_score->is_null ){
        $selected_option = 0; // 0 = NULL
    }else if( $questionnaire_score->score == 1 ){
        $selected_option = 1; // 1 = O
    }else if( $questionnaire_score->score == 0 ){
        $selected_option = 2; // 2 = X
    }

    $option2values = array( array("-", -1), array("o",1), array( "x", 0 ));
    $html =  '<select data-row="'.$row.'" class="span1 true_false_null" name="'. $select_name .'">';

    for( $i = 0 ; $i < 3 ; $i++ ){
        if( $selected_option == $i ){ $is_selected = "selected";}else{ $is_selected = "";}
        $html .= ( '<option value="'.$option2values[$i][1].'" '.$is_selected.'>'.$option2values[$i][0].'</option>' );
    }
    $html .= '</select>';

    return $html;
} 

// radio array 
function null_true_false( $radio_name, $questionnaire_score, $row_id  ){
    $selected_option = 0;
    if( $questionnaire_score->is_null ){
        $selected_option = 0; // 0 = NULL
    }else if( $questionnaire_score->score == 1 ){
        $selected_option = 1; // 1 = O
    }else if( $questionnaire_score->score == 0 ){
        $selected_option = 2; // 2 = X
    }
    $option2values = array( array("<span class='muted'>無</span> ", -1), array("<span class='text-info'>Ｏ</span>",1), array( "<span class='text-error'>Ｘ </span>", 0 ));
    
    $html = "";
    for( $i =0 ; $i <3 ; $i++){
        if( $selected_option == $i ){ $is_selected = "checked";}else{ $is_selected = "";}
        $html .= sprintf('<label class="radio inline"><input type="radio" data-row="%d" class="true_false_null" name="%s[%d]" value="%d" %s>%s</label><br>', 
            $row_id,
            $radio_name, 
            $row_id, 
            $option2values[$i][1],
            $is_selected, 
            $option2values[$i][0] ); 
    }
    return $html;
}

function score2class( $questionnaire_score ){
    if( $questionnaire_score->is_null ){
        return "null";
    }else if($questionnaire_score->score == 1 ){
        return "success";
    }else{
        return "error";
    }
}

function score2symbol( $raw_score ){   
    $mapping= array( );
    $mapping[0] ='Ｘ';
    $mapping[1] = 'Ｏ';
    return $mapping[$raw_score]; 
}
function cml_section_pos( $section ){
    $pos_score =0;
    foreach( $section->questions as $question  ){
        if( !$question->score->is_null && $question->score->score == 1 ) $pos_score+=1;
    }
    return $pos_score; 
}
function cml_section_neg( $section ){
    $pos_score =0;
    foreach( $section->questions as $question  ){
        if( !$question->score->is_null && $question->score->score == 0 ) $pos_score+=1;
    }
    return $pos_score;
}
function cml_section_score( $section){
    $pos_score = cml_section_pos( $section );
    $neg_score = cml_section_neg( $section );
    if( $pos_score + $neg_score > 0 ){
        return $pos_score/ ($pos_score+ $neg_score);
    }
    return null;
}

function cml_questionnaire_score( $questionnaire ){
    $pos_score = 0;
    $neg_score = 0;
    foreach( $questionnaire->sections as $section ){
        $pos_score += cml_section_pos($section);
        $neg_score += cml_section_neg( $section );
    }
    if( $pos_score + $neg_score >0) return $pos_score/ ($pos_score+$neg_score );
    return null;
}

function status_menu( $status ){
    $string_map = array( 0=>'<p class="text-error">未填寫</p>', 1=>'<p class="text-warning">填寫中</p>', 2=>'<p class="text-success">已填寫</p>' );
    return $string_map[ $status ];
    //return $html ;
}

function department_menu( $departments, $menu_name , $menu_class , $menu_id=NULL , $selected_id =NULL ){
    if( is_null( $menu_id) ){
       $html = "<select class='{$menu_class}' name='". $menu_name ."'>"; 
    }else{
        $html ="<select class='{$menu_class}' id='{$menu_id}' name='". $menu_name ."'>";
    }

    $html .= "<option value='-1'>-------</option>";
    foreach( $departments as $department ){
        $is_selected = "";
        if( $department->department_id == $selected_id ) {$is_selected ="selected"; }        
        $html .= "<option value='{$department->department_id}' ". $is_selected . ">{$department->department_name}</option>"; 
    }
    $html .= "</select>";
    return $html ;
} 