<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$animation_effects = $settings["animation_effects"];
$animation_delay = isset($settings["animation_delay"]["size"]) ? $settings["animation_delay"]["size"] : 50;
if($animation_effects == 'no-animation'){
    $animated_class='';
    $animation_attr='';
}else{
    $animate_offset = theplus_scroll_animation();
    $animated_class = 'animate-general';
    $animation_attr = ' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay).'"';
    $animation_attr .= ' data-animate-offset="'.esc_attr($animate_offset).'"';
    if(!empty($Plus_Listing_block) && $Plus_Listing_block == "Plus_Listing_block"){
        $animation_stagger = isset($settings["animation_stagger"]["size"]) ? $settings["animation_stagger"]["size"] : 150;	
        if( $settings["animated_column_list"] == 'stagger' ){
            $animated_columns = 'animated-columns';
            $animation_attr .=' data-animate-columns="stagger"';
            $animation_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
        }else if( $settings["animated_column_list"] == 'columns' ){
            $animated_columns = 'animated-columns';
            $animation_attr .=' data-animate-columns="columns"';
        }
    }
    if($settings["animation_duration_default"] == 'yes'){
        $animate_duration = $settings["animate_duration"]["size"];
        $animation_attr .= ' data-animate-duration="'.esc_attr($animate_duration).'"';
    }
    if(!empty($settings["animation_out_effects"]) && $settings["animation_out_effects"] != 'no-animation'){
        $animation_attr .= ' data-animate-out-type="'.esc_attr($settings["animation_out_effects"]).'" data-animate-out-delay="'.esc_attr($settings["animation_out_delay"]["size"]).'"';					
        if($settings["animation_out_duration_default"] == 'yes'){						
            $animation_attr .= ' data-animate-out-duration="'.esc_attr($settings["animation_out_duration"]["size"]).'"';
        }
    }
}