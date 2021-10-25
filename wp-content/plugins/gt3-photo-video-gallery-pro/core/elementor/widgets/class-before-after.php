<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use Elementor\Utils;
use GT3\PhotoVideoGalleryPro\Block\Before_After as Gallery;

class Before_After extends Basic {

	public function get_title(){
		return esc_html__('Before/After', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-beforeafter';
	}

	public function get_name(){
		return 'gt3pg-before-after';
	}

	//////////////////////////

	protected function _register_controls(){
		$this->start_controls_section(
			'basic_section',
			array(
				'label' => 'Basic',
			)
		);

		$this->add_control(
			'image_before',
			array(
				'label' => __( 'Image Before', 'gt3pg_pro' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'image_after',
			array(
				'label' => __( 'Image After', 'gt3pg_pro' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->end_controls_section();
	}

	// php
	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();
		echo $gallery->render_block($settings);

	}

	// js
	protected function _content_template(){

	}

}

