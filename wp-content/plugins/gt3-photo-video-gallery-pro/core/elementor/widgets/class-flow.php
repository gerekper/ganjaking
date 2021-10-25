<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Flow as Gallery;

if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Flow extends Basic {

	public function get_title(){
		return esc_html__('Flow', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-flow';
	}

	public function get_name(){
		return 'gt3pg-flow';
	}

	//////////////////////////

	protected function _register_controls(){
		$this->start_controls_section(
			'basic_section',
			array(
				'label' => 'Basic',
			)
		);

		$this->imagesControls();

		$this->add_control(
			'imageRatio',
			array(
				'label'   => esc_html__('Image Size Ratio', 'gt3pg_pro'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'4x3'     => esc_html__('4x3', 'gt3pg_pro'),
					'16x9'    => esc_html__('16x9', 'gt3pg_pro'),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'showTitle',
			array(
				'label'       => esc_html__('Show Title', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF titles on slide', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'       => esc_html__('Autoplay', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF slider autoplay', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'interval',
			array(
				'label'       => esc_html__('Slide Duration', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__('Sets time before change the slide in seconds', 'gt3pg_pro'),
				'default'     => 4,
				'condition'   => array(
					'autoplay!' => '',
				),
			)
		);

		$this->add_control(
			'transitionTime',
			array(
				'label'       => esc_html__('Transition Time', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__('Sets Transition animation time', 'gt3pg_pro'),
				'default'     => 600,
				'condition'   => array(
					'autoplay!' => '',
				),
			)
		);

		$this->add_control(
			'moduleHeight',
			array(
				'label'       => esc_html__('Module Height', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '100%',
				'description' => esc_html__('Set module height in px (pixels) . Enter \'100%\' for full height mode', 'gt3pg_pro'),
			)
		);

		$this->end_controls_section();
	}

	// php
	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();
		$settings['is_custom'] = '1';

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();
		echo $gallery->render_block($settings);

	}

	// js
	protected function _content_template(){

	}

}

