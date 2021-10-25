<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\FS_Slider as Gallery;

class FS_Slider extends Basic {

	public function get_name(){
		return 'gt3pg-fsslider';
	}

	public function get_title(){
		return esc_html__('FS Slider', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-fsslider';
	}

	protected function _controls(){
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__('Images', 'gt3pg_pro'),
			)
		);

		$this->imagesControls(array(
			'withCustomVideoLink' => true,
		));

		$this->end_controls_section();

		$this->start_controls_section('settings', array(
			'label' => esc_html__('Settings', 'gt3pg_pro'),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		));

		$this->add_control(
			'externalVideoThumb',
			array(
				'label'       => esc_html__('External Video Thumb', 'gt3pg_pro'),
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
			'animationType',
			array(
				'label'       => esc_html__('Animation Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'slide'  => esc_html__('Slide', 'gt3pg_pro'),
					'fade'    => esc_html__('Fade', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'       => esc_html__('Autoplay', 'gt3pg_pro'),
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
			'interval',
			array(
				'label'     => esc_html__('Autoplay Time (s)', 'gt3pg_pro'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 6,
				'condition' => array(
					'autoplay' => '1',
				),
			)
		);

		$this->add_control(
			'thumbnails',
			array(
				'label'       => esc_html__('Thumbnails', 'gt3pg_pro'),
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
			'showTitle',
			array(
				'label'       => esc_html__('Show Title', 'gt3pg_pro'),
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
			'showCaption',
			array(
				'label'       => esc_html__('Show Description', 'gt3pg_pro'),
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
			'scroll',
			array(
				'label'       => esc_html__('Scroll', 'gt3pg_pro'),
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
			'boxed',
			array(
				'label'       => esc_html__('Boxed', 'gt3pg_pro'),
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
			'cover',
			array(
				'label'       => esc_html__('Image Scaling', 'gt3pg_pro'),
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
			'imageSize',
			array(
				'label'       => esc_html__('Select image size', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'         => esc_html__('Default', 'gt3pg_pro'),
					'medium_large'    => esc_html__('Thumbnail (768px)', 'gt3pg_pro'),
					'large'           => esc_html__('Large (1024px)', 'gt3pg_pro'),
					'gt3pg_optimized' => esc_html__('Optimized', 'gt3pg_pro'),
					'full'            => esc_html__('Full Size', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'rightClick',
			array(
				'label'       => esc_html__('Right Click Guard', 'gt3pg_pro'),
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
			'ytWidth',
			array(
				'label'       => esc_html__('YouTube & Vimeo Width', 'gt3pg_pro'),
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
			'random',
			array(
				'label'       => esc_html__('Random Order', 'gt3pg_pro'),
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

		$this->end_controls_section();

		$this->start_controls_section('style', array(
			'label' => esc_html__('Style', 'gt3pg_pro'),
			'tab'   => Controls_Manager::TAB_STYLE,
		));

		$this->add_control(
			'footerAboveSlider',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => esc_html__('Footer Above Slider', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'textColor',
			array(
				'type'  => Controls_Manager::COLOR,
				'label' => esc_html__('Color', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'borderOpacity',
			array(
				'label'       => esc_html__('Border Opacity', 'gt3pg_pro'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 0.1,
				),
				'range'       => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0,
						'step' => 0.05,
					),
				),
				'description' => esc_html__('Border Color Opacity', 'gt3pg_pro'),
			)
		);

		$this->end_controls_section();
	}

	protected function _render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();
		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();

		$settings['borderOpacity'] = $settings['borderOpacity']['size'];

		echo $gallery->render_block($settings);
	}
}

