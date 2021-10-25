<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Thumbnails as Gallery;

class Thumbnails extends Basic {

	public function get_name(){
		return 'gt3pg-thumbnails';
	}

	public function get_title(){
		return esc_html__('Thumbnails', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-thumbnails';
	}

	protected function construct(){

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
			'thumbnails_Lightbox',
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
			'thumbnails_Size',
			array(
				'label'       => esc_html__('Gallery Size', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'16x9'    => esc_html__('16x9', 'gt3pg_pro'),
					'4x3'     => esc_html__('4x3', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'thumbnails_Controls',
			array(
				'label'       => esc_html__('Thumbnails Controls', 'gt3pg_pro'),
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
			'thumbnails_thumbnailsSize',
			array(
				'label'       => esc_html__('Thumbnails Size', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'auto'    => esc_html__('Auto', 'gt3pg_pro'),
					'fixed'   => esc_html__('Fixed', 'gt3pg_pro'),
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
					'medium_large'    => esc_html__('Thumbnail (768px)', 'gt3pg_pro'),
					'large'           => esc_html__('Large (1024px)', 'gt3pg_pro'),
					'gt3pg_optimized' => esc_html__('Optimized', 'gt3pg_pro'),
					'full'            => esc_html__('Full Size', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'thumbnails_thumbnailsImageSize',
			array(
				'label'       => esc_html__('Select Thumbnails Image Size', 'gt3pg_pro'),
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

		$this->end_controls_section();

		$this->lightboxControls(array( 'thumbnails_Lightbox' => '1' ));

	}

	protected function _render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery  = Gallery::instance();
		echo $gallery->render_block($settings);
	}
}

