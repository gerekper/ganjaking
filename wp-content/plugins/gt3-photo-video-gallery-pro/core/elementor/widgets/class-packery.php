<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Block\Packery as Gallery;

class Packery extends Basic {

	public function get_name(){
		return 'gt3pg-packery';
	}

	public function get_title(){
		return esc_html__('Packery', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-packery';
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

		$this->imagesControls(array(
			'withCategories'      => true,
			'withCustomVideoLink' => true,
			'withSearch'          => true,
		));

		$this->end_controls_section();

		$this->start_controls_section('settings', array(
			'label' => esc_html__('Settings', 'gt3pg_pro'),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		));

		$this->loadMoreControls();

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
			'packery',
			array(
				'label'       => esc_html__('Packery Grid', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('Type 1', 'gt3pg_pro'),
					'2'       => esc_html__('Type 2', 'gt3pg_pro'),
					'3'       => esc_html__('Type 3', 'gt3pg_pro'),
					'4'       => esc_html__('Type 4', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		for($i = 1; $i <= 4; $i++) {
			$packery_img = esc_url(GT3PG_PRO_PLUGINROOTURL.'/dist/img/type'.$i.'.png');
			$image       = esc_attr('background-image: url("'.$packery_img.'")');

			$this->add_control(
				'packery_type'.$i.'_description',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => '<div class="packery_preview" style="'.$image.'"></div>',
					'condition' => array(
						'packery' => ''.$i,
					)
				)
			);
		}
		$this->add_control(
			'lazyLoad',
			array(
				'label'       => esc_html__('Lazy Load', 'gt3pg_pro'),
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
				'condition'   => array(
					'gridType!' => 'circle',
				),
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
		$this->lightboxControls();
	}
}

