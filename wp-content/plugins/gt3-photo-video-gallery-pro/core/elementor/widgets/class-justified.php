<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Justified as Gallery;


class Justified extends Basic {

	public function get_title(){
		return esc_html__('Justified', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-justified';
	}

	public function get_name(){
		return 'gt3pg-justified';
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

		$this->imagesControls(array(
			'withCustomVideoLink' => true,
			'withCategories'      => true,
		));

		$this->end_controls_section();

		$this->start_controls_section(
			'settings',
			array(
				'label' => esc_html__('Basic', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'lightbox',
			array(
				'label'       => esc_html__('Lightbox', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('Enabled', 'gt3pg_pro'),
					'0'       => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'loader',
			array(
				'label'   => esc_html__('Show Images', 'gt3pg_pro'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'   => esc_html__('Default', 'gt3pg_pro'),
//					'asIs'      => esc_html__('Image Ready', 'gt3pg_pro'),
					'fromFirst' => esc_html__('From First To Last', 'gt3pg_pro'),
					'random'    => esc_html__('Random', 'gt3pg_pro'),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'gap',
			array(
				'label'   => esc_html__('Grid Gap', 'gt3pg_pro'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'0'       => '0',
					'1px'     => '1px',
					'2px'     => '2px',
					'3px'     => '3px',
					'4px'     => '4px',
					'5px'     => '5px',
					'10px'    => '10px',
					'15px'    => '15px',
					'20px'    => '20px',
					'25px'    => '25px',
					'30px'    => '30px',
					'35px'    => '35px',
				),
				'default' => 'default',
				/*'selectors' => array(
					'{{WRAPPER}} .isotope_wrapper'     => 'margin-right:-{{VALUE}}; margin-bottom:-{{VALUE}};',
					'{{WRAPPER}} .mirror_wrapper'      => 'margin-right:-{{VALUE}};',
					'{{WRAPPER}} .isotope_item .img'   => 'padding-right: {{VALUE}}; padding-bottom:{{VALUE}};',
					'{{WRAPPER}} .mirror_wrapper .img' => 'padding-right: {{VALUE}};',
				),*/
			)
		);

		$this->add_control(
			'height',
			array(
				'label'      => esc_html__('Images Height', 'gt3pg_pro'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 240,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 200,
						'max'  => 600,
						'step' => 10,
					),
					'vh' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					)
				),
				'size_units' => array( 'px'/*, 'vh'*/ ),
//				'selectors'  => array(
//					'{{WRAPPER}} .isotope_item .img' => 'height: {{SIZE}}{{UNIT}};',
//				),
			)
		);

		$this->add_control(
			'fadeDuration',
			array(
				'label'          => esc_html__('Fade Duration', 'gt3pg_pro'),
				'description'    => esc_html__('Fade Duration Time', 'gt3pg_pro'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 300,
					'unit' => 'ms',
				),
				'tablet_default' => array(
					'size' => 500,
					'unit' => 'ms',
				),
				'mobile_default' => array(
					'size' => 500,
					'unit' => 'ms',
				),
				'range'          => array(
					'ms' => array(
						'min'  => 100,
						'max'  => 2000,
						'step' => 40,
					)
				),
				'size_units'     => array( 'ms' ),
				/*	'selectors'      => array(
						'{{WRAPPER}} .zoom_gallery_wrapper .isotope_item .img' =>
							'transition-duration: {{SIZE}}{{UNIT}};
							 -moz-transition-duration: {{SIZE}}{{UNIT}};
							 -webkit-transition-duration: {{SIZE}}{{UNIT}};',
					),*/
			)
		);

		$this->add_control(
			'fadeDelay',
			array(
				'label'       => esc_html__('Fade Timeout', 'gt3pg_pro'),
				'description' => esc_html__('Delay Between Loading Items', 'gt3pg_pro'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 100,
					'unit' => 'ms',
				),
				'range'       => array(
					'ms' => array(
						'min'  => 100,
						'max'  => 2000,
						'step' => 20,
					)
				),
				'size_units'  => array( 'ms' ),
			)
		);

		$this->add_control(
			'imageSize',
			array(
				'label'   => esc_html__('Choose Image Size', 'gt3pg_pro'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'         => esc_html__('Default', 'gt3pg_pro'),
					'medium'          => esc_html__('Medium (300px)', 'gt3pg_pro'),
					'medium_large'    => esc_html__('Medium Large (768px)', 'gt3pg_pro'),
					'large'           => esc_html__('Large (1024px)', 'gt3pg_pro'),
					'gt3pg_optimized' => esc_html__('Optimized', 'gt3pg_pro'),
					'full'            => esc_html__('Full', 'gt3pg_pro'),
				),
				'default' => 'medium_large',
			)
		);

		$this->end_controls_section();

		$this->lightboxControls(array( 'lightbox' => '1' ));

	}

	// php
	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();
		$settings['is_custom'] = '1';
		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();

		$settings['fadeDelay']    = $settings['fadeDelay']['size'];
		$settings['fadeDuration'] = $settings['fadeDuration']['size'];
		$settings['height']       = $settings['height']['size'];

		echo $gallery->render_block($settings);
	}

	// js
	protected function _content_template(){

	}

}

