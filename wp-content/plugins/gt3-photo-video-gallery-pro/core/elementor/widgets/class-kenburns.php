<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Kenburns as Gallery;


class Kenburns extends Basic {

	public function get_title(){
		return esc_html__('Kenburns', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-kenburns';
	}

	public function get_name(){
		return 'gt3pg-kenburns';
	}

	public function get_script_depends(){
		return array();
	}

	public function __construct(array $data = [], $args = null){
		parent::__construct($data, $args);
		$this->actions();
	}

	private function actions(){
		add_action('elementor/widgets/widgets_registered', function($widgets_manager){
			/* @var \Elementor\Widgets_Manager $widgets_manager */
			$widgets_manager->register_widget_type($this);
		});
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
			'moduleHeight',
			array(
				'label'       => esc_html__('Module Height', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '100%',
				'description' => esc_html__('Set module height in px (pixels). Enter \'100%\' for full height mode', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'interval',
			array(
				'label'       => esc_html__('Slide Duration', 'gt3pg_pro'),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'step'        => 0.1,
				'default'     => 4,
				'description' => esc_html__('Set the timing of single slides in seconds', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'transitionTime',
			array(
				'label'       => esc_html__('Transition Time', 'gt3pg_pro'),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 100,
				'step'        => 50,
				'default'     => 1000,
				'description' => esc_html__('Sets Transition animation time, (ms)', 'gt3pg_pro'),
			)
		);

	/*	$this->add_control(
			'overlayState',
			array(
				'label'       => esc_html__('Overlay', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF slides color overlay.', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'overlayBg',
			array(
				'label'       => esc_html__('Overlay Background Color', 'gt3pg_pro'),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__('Select overlay color.', 'gt3pg_pro'),
				'condition'   => array(
					'overlayState!' => '',
				)
			)
		);*/

		$this->end_controls_section();
	}

	// php
	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery  = Gallery::instance();

		echo $gallery->render_block($settings);
	}

	// js
	protected function _content_template(){

	}

}

