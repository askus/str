<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
        array("title"=>"我的評分表", "url"=>"questionnaire/index", "role"=>3),
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
function score2symbol( $raw_score ){   
    $mapping= array( );
    $mapping[0] ='x';
    $mapping[1] = 'o';
    return $mapping[$raw_score]; 
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