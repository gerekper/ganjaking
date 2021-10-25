<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;


use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Stripe as Gallery;

class Stripe extends Basic {

	public function get_title(){
		return esc_html__('Stripe Gallery', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'eicon-slider-vertical';
	}

	public function get_name(){
		return 'gt3pg-stripe';
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
			'images',
			array(
				'label' => esc_html__('Images', 'gt3pg_pro'),
			)
		);

		$this->imagesControls();

		$this->end_controls_section();

		$this->start_controls_section(
			'settings',
			array(
				'label' => esc_html__('Basic', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'moduleHeight',
			array(
				'label'       => esc_html__('Module Height', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '100%',
				'description' => esc_html__('Set module height in px (pixels). Enter \'100%\' for full height mode', 'gt3pg_pro'),
			)
		);

		$this->end_controls_section();

	}

	// php
	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery  = Gallery::instance();
		$settings = array_merge($settings, array(
			'_uid'       => $this->get_id(),
			'_blockName' => 'stripe',
			'className'  => '',

			'blockAlignment' => '',
			'fromElementor' => true,
		));

		echo $gallery->render_block($settings);
	}

	// js
	protected function _content_template(){

	}

}

