<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Carousel as Gallery;

class Carousel extends Basic {

	public function get_name(){
		return 'gt3pg-carousel';
	}

	public function get_title(){
		return esc_html__('Carousel', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-carousel';
	}

	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();

		echo $gallery->render_block($settings);

	}

	protected function _controls(){
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__('Images', 'gt3pg_pro'),
			)
		);

		$this->imagesControls(
			array(
				'withCustomVideoLink' => false,
				'withCategories'      => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'settings', array(
				'label' => esc_html__('Settings', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'       => esc_html__('Height, px', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'default'     => 300,
				'selectors' => array(
					'{{WRAPPER}} .gallery-isotope-wrapper,{{WRAPPER}} img' => 'height: {{VALUE}}px; max-height: {{VALUE}}px',
				)
			)
		);

		$this->add_control(
			'centerMode',
			array(
				'label'       => esc_html__('Center Mode', 'gt3pg_pro'),
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
			'infinite',
			array(
				'label'       => esc_html__('Infinite', 'gt3pg_pro'),
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
			'variableWidth',
			array(
				'label'       => esc_html__('Variable Width', 'gt3pg_pro'),
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

/*		$this->add_control(
			'fade',
			array(
				'label'       => esc_html__('Fade', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('Enabled', 'gt3pg_pro'),
					'0'       => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);*/

		$this->add_control(
			'dots',
			array(
				'label'       => esc_html__('Dots', 'gt3pg_pro'),
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
			'arrow',
			array(
				'label'       => esc_html__('Arrows', 'gt3pg_pro'),
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

		/*$this->add_control(
			'slidesToShow',
			array(
				'label'       => esc_html__('# of slides to show', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('1', 'gt3pg_pro'),
					'2'       => esc_html__('2', 'gt4pg_pro'),
					'3'       => esc_html__('3', 'gt5pg_pro'),
					'4'       => esc_html__('4', 'gt6pg_pro'),
				),
				'default'     => 'default',
			)
		);*/

		$this->add_control(
			'slidesToScroll',
			array(
				'label'       => esc_html__('# of slides to scroll', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('1', 'gt3pg_pro'),
					'2'       => esc_html__('2', 'gt4pg_pro'),
					'3'       => esc_html__('3', 'gt5pg_pro'),
					'4'       => esc_html__('4', 'gt6pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'centerPadding',
			array(
				'label'   => esc_html__('Padding, px', 'gt3pg_pro'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 40,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'       => esc_html__('Enables Autoplay', 'gt3pg_pro'),
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
			'autoplaySpeed',
			array(
				'label'   => esc_html__('Autoplay Speed, ms', 'gt3pg_pro'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 2000,
			)
		);

		$this->add_control(
			'showTitle',
			array(
				'label'       => esc_html__('Show Image Title', 'gt3pg_pro'),
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
				'label'       => esc_html__('Show Captions', 'gt3pg_pro'),
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
				'label'       => esc_html__('Select Image Size', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'         => esc_html__('Default', 'gt3pg_pro'),
					'medium'          => esc_html__('Medium (300px)', 'gt3pg_pro'),
					'medium_large'    => esc_html__('Thumbnail (768px)', 'gt3pg_pro'),
					'large'           => esc_html__('Large (1024px)', 'gt3pg_pro'),
					'gt3pg_optimized' => esc_html__('Optimized', 'gt3pg_pro'),
					'full'            => esc_html__('Full Size', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->end_controls_section();
	}
}

