<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

function layerslider_builder_convert_numbers(&$item, $key) {
	if(is_numeric($item)) {
		$item = (float) $item;
	}
}

function ls_ordinal_number($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    $mod100 = $number % 100;
    return $number . ($mod100 >= 11 && $mod100 <= 13 ? 'th' :  $ends[$number % 10]);
}


function layerslider_check_unit($str, $key = '') {

	if(strstr($str, 'px') == false && strstr($str, '%') == false) {
		if( $key !== 'z-index' && $key !== 'font-weight' && $key !== 'opacity') {
			return $str.'px';
		}
	}

	return $str;
}
