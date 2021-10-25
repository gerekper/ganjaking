<?php

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists( 'RevSlider' )) {
	return;
}

use Elementor\Controls_Manager;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_RevolutionSlider $widget */

$gt3_themes_core_slider = new RevSlider();
$arrSliders_gt3_core = $gt3_themes_core_slider->getArrSliders();

$gt3_rs_core = array();
if ( $arrSliders_gt3_core ) {
	$gt3_rs_core['none'] = esc_html__( 'Select slider', 'gt3_themes_core' );
	foreach ( $arrSliders_gt3_core as $gt3_themes_core_slider ) {
		/** @var $slider RevSlider */
		$gt3_rs_core[ $gt3_themes_core_slider->getID() ] = $gt3_themes_core_slider->getTitle();
	}
} else {
	$gt3_rs_core['none'] = esc_html__( 'No sliders found', 'gt3_themes_core' );
}

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('General', 'gt3_themes_core')
	)
);

$widget->add_control(
	'gt3_rs_slider_core',
	array(
		'label'   => esc_html__('Revolution Slider','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => $gt3_rs_core,
		'default' => 'none'
	)
);

$widget->end_controls_section();