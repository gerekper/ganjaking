<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Instagram as Gallery;

class Instagram extends Basic {

	public function get_name(){
		return 'gt3pg-instagram';
	}

	public function get_title(){
		return esc_html__('Instagram', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-instagram';
	}

	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery  = Gallery::instance();

		echo $gallery->render_block($settings);
	}

	protected function _controls(){
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__('Images', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'       => esc_html__('Select Source', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'user' => esc_html__('Username', 'gt3pg_pro'),
					'tag'  => esc_html__('Tag', 'gt3pg_pro'),
				),
				'default'     => 'user',
			)
		);

		$this->add_control(
			'userName',
			array(
				'label'       => esc_html__('Username', 'gt3pg_pro'),
				'label_block' => true,
				'condition'   => array(
					'source' => 'user',
				),
			)
		);

		$this->add_control(
			'userID',
			array(
				'label'       => esc_html__('User ID', 'gt3pg_pro'),
				'label_block' => true,
				'condition'   => array(
					'source' => 'user',
				),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'       => esc_html__('Tag', 'gt3pg_pro'),
				'label_block' => true,
				'condition'   => array(
					'source' => 'tag',
				),
			)
		);

		$this->add_control(
			'linkTo',
			array(
				'label'       => esc_html__('Link To', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'   => esc_html__('Default', 'gt3pg_pro'),
					'instagram' => esc_html__('Instagram', 'gt3pg_pro'),
					'lightbox'  => esc_html__('Lightbox', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);


		$this->end_controls_section();

		$this->start_controls_section('settings', array(
			'label' => esc_html__('Settings', 'gt3pg_pro'),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		));

		$this->add_control(
			'gridType',
			array(
				'label'       => esc_html__('Grid Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'        => esc_html__('Default', 'gt3pg_pro'),
					'square'         => esc_html__('Square', 'gt3pg_pro'),
					'rectangle'      => esc_html__('Rectangle 4x3', 'gt3pg_pro'),
					'rectangle-16x9' => esc_html__('Rectangle 16x9', 'gt3pg_pro'),
					'circle'         => esc_html__('Circle', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'margin',
			array(
				'label'       => esc_html__('Margin, px', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'default'     => '20',
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
			'columns',
			array(
				'label'       => esc_html__('Columns', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('1', 'gt3pg_pro'),
					'2'       => esc_html__('2', 'gt3pg_pro'),
					'3'       => esc_html__('3', 'gt3pg_pro'),
					'4'       => esc_html__('4', 'gt3pg_pro'),
					'5'       => esc_html__('5', 'gt3pg_pro'),
					'6'       => esc_html__('6', 'gt3pg_pro'),
					'7'       => esc_html__('7', 'gt3pg_pro'),
					'8'       => esc_html__('8', 'gt3pg_pro'),
					'9'       => esc_html__('9', 'gt3pg_pro'),
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
			'loadMoreFirst',
			array(
				'label'       => esc_html__('Show Images', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 12,
				'default'     => 12,
			)
		);

		$this->end_controls_section();
	}
}

