<?php

namespace GT3\PhotoVideoGallery\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGallery\Block\Masonry as Gallery;

class Masonry extends Basic {

	public function get_name(){
		return 'gt3pg-masonry';
	}

	public function get_title(){
		return esc_html__('Masonry', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-masonry';
	}

	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGallery\Block\Basic $gallery */
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

		$this->imagesControls();

		$this->end_controls_section();

		$this->start_controls_section('settings', array(
			'label' => esc_html__('Settings', 'gt3pg_pro'),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		));

		$this->add_control(
			'linkTo',
			array(
				'label'       => esc_html__('Link Image To', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'  => esc_html__('Default', 'gt3pg_pro'),
					'post'     => esc_html__('Attachment Page', 'gt3pg_pro'),
					'file'     => esc_html__('File', 'gt3pg_pro'),
					'lightbox' => esc_html__('Lightbox', 'gt3pg_pro'),
					'none'     => esc_html__('None', 'gt3pg_pro'),
				),
				'default'     => 'default',
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
			'isMargin',
			array(
				'label'       => esc_html__('Margin', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'custom'  => esc_html__('Custom', 'gt3pg_pro'),
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
				'condition'   => array(
					'isMargin' => 'custom',
				),
			)
		);
		$this->add_control(
			'cornersType',
			array(
				'label'       => esc_html__('Corners Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'  => esc_html__('Default', 'gt3pg_pro'),
					'standard' => esc_html__('Standard', 'gt3pg_pro'),
					'rounded'  => esc_html__('Rounded', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'borderType',
			array(
				'label'       => esc_html__('Image Border', 'gt3pg_pro'),
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
			'borderSize',
			array(
				'label'     => esc_html__('Border Size, px', 'gt3pg_pro'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => '0',
				'default'   => '1',
				'condition' => array(
					'borderType' => '1',
				),
			)
		);

		$this->add_control(
			'borderPadding',
			array(
				'label'     => esc_html__('Border Padding, px', 'gt3pg_pro'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => '0',
				'default'   => '1',
				'condition' => array(
					'borderType' => '1',
				),
			)
		);

		$this->add_control(
			'borderColor',
			array(
				'label'     => esc_html__('Border Color', 'gt3pg_pro'),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'borderType' => '1',
				),
			)
		);

		$this->end_controls_section();
	}
}

