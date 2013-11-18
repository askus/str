<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration for ACL permissions
 *
 * 1: Admin
 * 2: User
 */

$config['permission'] = array(
    'news' => array(
        'view'   => 3,
        'add'    => 1,
        'edit'   => 1,
        'delete' => 1
    ),
    'monthly' => array(
        'view'   => 3,
        'add'    => 1,
        'edit'   => 3,
        'delete' => 1
    ),
    'quarterly' => array(
        'view'   => 3,
        'add'    => 1,
        'edit'   => 3,
        'delete' => 1
    ),
    'semiannual' => array(
        'view'   => 3,
        'add'    => 1,
        'edit'   => 3,
        'delete' => 1
    ),
    'query' => array(
        'view' => 3
    ),
    'report_manage' => array(
        'view'   => 1,
        'add'    => 1,
        'delete' => 1
    ),
    'user' => array(
        'view'   => 1,
        'add'    => 1,
        'edit'   => 1,
        'delete' => 1,
        'chpw'   => 3
    ),
    'items' => array(
        'view'   => 1,
        'add'    => 1,
        'edit'   => 1,
        'delete' => 1
    ),
    'template' => array( 
        'view'   => 1,
        'add'    => 1,
        'edit'   => 1,
        'delete' => 1
    ),
    'questionnaire' => array(
        'view'   => 3,
        'add'    => 1,
        'edit'   => 3,
        'delete' => 1
    ),
    'analysis' => array(
        'view'  => 1,
        'add'   => 1,
        'edit'  => 1,
        'delete' => 1
    )

);
