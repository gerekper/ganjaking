<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	if($button_icon_style=='font_awesome'){
		$icons=$button_icon;
	}else if($button_icon_style=='icon_mind'){
		$icons=$button_icons_mind;
	}else{
		$icons='';
	}
	$button_content='';
	$icons_before=$icons_after='';
	if($before_after=='before' && !empty($icons)){
		$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
	}
	if($before_after=='after' && !empty($icons)){
	   $icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
	}
	
	if($button_style=='style-8'){
		$button_content =$icons_before . $button_text . $icons_after;
	}
	
	if($button_style=='style-7'){
		$button_content =$button_text.'<span class="btn-arrow"></span>';
	}
	if($button_style=='style-9'){
		$button_content =$button_text.'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
	}
	return $button_content;
?>