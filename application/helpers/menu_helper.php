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
        array("title"=>"建立評分表", "url"=>"template/create", "role"=>1 ),
        array("title"=>"檢視評分表", "url"=>"questionarrie/show", "role"=>3),
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