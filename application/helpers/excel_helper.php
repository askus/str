<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


function num2col( $number ){
	$ret = "";
	while( $number > 0 ){
		$number -= 1 ;
		$chr = chr( ($number % 26) + ord('A')  ); 
		$ret = $chr . $ret ;
		$number -= ($number % 26);
		$number /= 26;  
	}
	return $ret ;
}

function num2chiNum( $number){
	$dict = array( "零", "一", "二", "三", "四", "五", "六", "七","八","九","十");
	if( $number > 10 ) return $number;
	return $dict[$number ];

}
