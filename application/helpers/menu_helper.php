<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
        array("title"=>"檢視評分表", "url"=>"template/index", "role"=>3),
        array("title"=>"填寫評分表", "url"=>"questionarrie/update", "role"=>3),
        array("title"=>"分析評分表", "url"=>"questionarrie/analyze", "role"=>1)
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

function department_menu( $departments, $menu_name , $menu_class , $selected_id =NULL ){
    $html = "<select class='{$menu_class}' name='". $menu_name ."'>"; 
    $html .= "<option value='-1'>-------</option>";
    foreach( $departments as $department ){
        $is_selected = "";
        if( $department->department_id == $selected_id ) {$is_selected ="selected"; }        
        $html .= "<option value='{$department->department_id}' ". $is_selected . ">{$department->department_name}</option>"; 
    }
    $html .= "</select>";
    return $html ;
} 