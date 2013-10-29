<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    function trans_checkbox_array( $checkbox_array ){
        $ret = array();
        $to_add = false;
        for( $i= 0 ; $i< count( $checkbox_array); $i++){
            if( $checkbox_array[$i] == 1){
                $to_add = true;
            }else if( $checkbox_array[$i] < 0 ){
                $ret[] = $to_add;
                $to_add = false;
            }
        }
        return $ret ; 
    }