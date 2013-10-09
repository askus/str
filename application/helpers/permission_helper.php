<?php

/* 檢查是否有登入 */
function check_login()
{
    $CI = &get_instance();

    if ( !$CI->acl->is_logged_in()) {
        redirect('login');
        exit;
    }
}

/* 檢查使用者權限 */
function check_permission($class, $action, $own = null)
{
    $CI = &get_instance();

    if ($CI->acl->has_permission($class, $action, $own) == false) {
        $uid = $CI->session->userdata('user_id');

        log_message('error', "[UID: {$uid}] [CLASS: {$class}] - 操作權限不足");
        show_error('使用者操作權限不足');
    }
}

function check_user($checkUserID)
{
    $CI = &get_instance();
    
    $my_uid = $CI->session->userdata('user_id');

    if ($checkUserID != $my_uid) {
        show_error('使用者操作權限不足');
    }
}