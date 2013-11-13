<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 date_default_timezone_set("Asia/Taipei");

/* 西元日期轉民國日期 */
function date_to_chinese($date)
{
	// filter out time information 
    $date_text = explode( ' ', $date ); 
    $s_date = explode('-', $date_text[0]);
    $year  = $s_date[0] - 1911;
    $month = $s_date[1];
    $day   = $s_date[2];

    return "{$year}-{$month}-{$day}";
}

/* 西元年轉民國年 */
function year_to_chinese($year)
{
    return $year-1911;
}

function next_k_year( $k ){
	$next_k_year_arr = array();
	$this_year =  date('Y') ;
	for( $i = 0 ; $i <$k; $i++){
		$next_k_year_arr[] = year_to_chinese( $this_year+ $i);
	}
	return $next_k_year_arr; 
}
function months(){
	$month_array= array();
	for( $i = 1; $i <= 12 ; $i++){
		$month_array[] = $i;
	}
	return $month_array;
}

function trim_sec( $datetime ){
	$tmp_datetime_array = explode( ":", $datetime );
	unset( $tmp_datetime_array[ count($tmp_datetime_array)-1 ]);
	return implode( ":", $tmp_datetime_array);
}