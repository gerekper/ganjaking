<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;


use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

use GT3\PhotoVideoGalleryPro\Block\Ribbon as Gallery;


class Ribbon extends Basic {

	public function get_title(){
		return esc_html__('Ribbon', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-ribbon';
	}

	public function get_name(){
		return 'gt3pg-ribbon';
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
			'showTitle',
			array(
				'label'       => esc_html__('Show Title', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF title', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'showCaption',
			array(
				'label'       => esc_html__('Show Caption', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF captions', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'moduleHeight',
			array(
				'label'       => esc_html__('Module Height', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__('Set module height in px (pixels). Enter \'100%\' for full height mode.', 'gt3pg_pro'),
				'default'     => '600',
			)
		);

		/*$this->add_control(
			'itemsPadding',
			array(
				'label'       => esc_html__('Paddings around the images', 'gt3pg_pro'),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__('Please use this option to add paddings around the images. Recommended size in pixels 0-50. (Ex.: 15px)', 'gt3pg_pro'),
				'min'         => '0',
				'step'        => '5',
				'default'     => '30',
			)
		);*/

		$this->add_control(
			'controls',
			array(
				'label' => esc_html__('Controls', 'gt3pg_pro'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label' => esc_html__('Autoplay', 'gt3pg_pro'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'interval',
			array(
				'label'     => esc_html__('Slide Duration (s)', 'gt3pg_pro'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'step'      => 0.1,
				'default'   => 6,
				'condition' => array(
					'autoplay!' => '',
				),
			)
		);

		$this->add_control(
			'transitionTime',
			array(
				'label'     => esc_html__('Transition Interval (ms)', 'gt3pg_pro'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 100,
				'step'      => 100,
				'default'   => 600,
				'condition' => array(
					'autoplay!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__('Title', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__('Title Color', 'gt3pg_pro'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ribbon_title' => 'color: {{VALUE}};'
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ribbon_title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'descr_style_section',
			array(
				'label' => esc_html__('Caption', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'descr_color',
			array(
				'label'     => esc_html__('Caption Color', 'gt3pg_pro'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ribbon_descr' => 'color: {{VALUE}};'
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'descr_typography',
				'selector' => '{{WRAPPER}} .ribbon_descr',
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
		$gallery  = Gallery::instance();

		echo $gallery->render_block($settings);

	}

	// js
	protected function _content_template(){

	}
}

