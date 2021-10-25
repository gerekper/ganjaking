<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

use GT3\PhotoVideoGalleryPro\Block\Albums as Gallery;
use GT3_Post_Type_Gallery;
use WP_Query;

use GT3\PhotoVideoGalleryPro\Elementor\Controls\Query as Control_Gallery;

class Albums extends Album_Basic {

	public function get_name(){
		return 'gt3pg-albums';
	}

	public function get_title(){
		return esc_html__('Albums', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-albums';
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

		$this->add_control(
			'query',
			array(
				'label'    => esc_html__('Query', 'gt3pg_pro'),
				'type'     => 'gt3pg_pro-query',
				'settings' => array(
					'showCategory'  => true,
					'showPost'      => true,
					'post_type'     => self::POST_TYPE,
					'post_taxonomy' => self::TAXONOMY,
				),
			)
		);

		/*$this->add_control(
			'filterEnable',
			array(
				'label'       => esc_html__('Use Filter', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('Yes', 'gt3pg_pro'),
					'0'       => esc_html__('No', 'gt3pg_pro'),
				),
				'default'     => 'default',
				'condition'   => array(
					'query[taxonomy]!' => [],
				),
			)
		);

		$this->add_control(
			'filterText',
			array(
				'label'       => esc_html__('All Items Text', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('All', 'gt3pg_pro'),
				'default'     => esc_html__('All', 'gt3pg_pro'),
				'condition'   => array(
					'query[taxonomy]!' => [],
					'filterEnable' => '1',
				),
			)
		);

		$this->add_control(
			'filterShowCount',
			array(
				'label'       => esc_html__('Item Count', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'1'       => esc_html__('Enabled', 'gt3pg_pro'),
					'0'       => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => 'default',
				'condition'   => array(
					'query[taxonomy]!' => [],
					'filterEnable' => '1',
				),
			)
		);*/

		$this->end_controls_section();

		$this->start_controls_section('section_album', array(
			'label' => esc_attr__('Album Settings', 'gt3pg_pro'),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		));

		$this->add_control(
			'albumType',
			array(
				'label'       => esc_html__('Album Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'masonry' => esc_html__('Masonry', 'gt3pg_pro'),
					'grid'    => esc_html__('Grid', 'gt3pg_pro'),
					'packery' => esc_html__('Packery', 'gt3pg_pro'),
				),
				'default'     => 'masonry',
			)
		);

		$this->add_control(
			'packery',
			array(
				'label'       => esc_html__('Packery Grid', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Type 1', 'gt3pg_pro'),
					'2' => esc_html__('Type 2', 'gt3pg_pro'),
					'3' => esc_html__('Type 3', 'gt3pg_pro'),
					'4' => esc_html__('Type 4', 'gt3pg_pro'),
				),
				'default'     => '1',
				'condition'   => array(
					'albumType' => 'packery',
				),
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
						'packery'   => ''.$i,
						'albumType' => 'packery',
					)
				)
			);
		}

		$this->add_control(
			'gridType',
			array(
				'label'       => esc_html__('Grid Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'square'         => esc_html__('Square', 'gt3pg_pro'),
					'rectangle'      => esc_html__('Rectangle 4x3', 'gt3pg_pro'),
					'rectangle-16x9' => esc_html__('Rectangle 16x9', 'gt3pg_pro'),
					'circle'         => esc_html__('Circle', 'gt3pg_pro'),
				),
				'default'     => 'square',
				'condition'   => array(
					'albumType' => 'grid',
				),
			)
		);

		$this->add_control(
			'paginationType',
			array(
				'label'       => esc_html__('Pagination Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default'    => esc_html__('Default', 'gt3pg_pro'),
					'pagination' => esc_html__('Pagination', 'gt3pg_pro'),
					'loadMore'   => esc_html__('Ajax Load More', 'gt3pg_pro'),
					'none'       => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'loadMoreButtonText',
			array(
				'label'       => esc_html__('Load Mode button Text', 'gt3pg_pro'),
				'label_block' => true,
				'default'     => esc_html__('Load More', 'gt3pg_pro'),
				'condition'   => array(
					'paginationType' => 'loadMore',
				),
			)
		);

		$this->add_control(
			'loadMoreLimit',
			array(
				'label'       => esc_html__('Ajax Load Count', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'default'     => '4',
				'min'         => '0',
				'condition'   => array(
					'paginationType' => 'loadMore',
				),
			)
		);

		$this->add_control(
			'showMeta',
			array(
				'label'       => esc_html__('Show Meta', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '1',
			)
		);

		$this->add_control(
			'showMetaTitle',
			array(
				'label'       => esc_html__('Show Meta Title', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '1',
				'condition'   => array(
					'showMeta' => '1',
				),
			)
		);

		$this->add_control(
			'showMetaCount',
			array(
				'label'       => esc_html__('Show Meta Count', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '1',
				'condition'   => array(
					'showMeta' => '1',
				),
			)
		);
		$this->add_control(
			'showMetaDate',
			array(
				'label'       => esc_html__('Show Meta Date', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '1',
				'condition'   => array(
					'showMeta' => '1',
				),
			)
		);

		$formats           = array_map(function($format){
			return date($format);
		}, Gallery::getDateFormats());
		$formats['system'] = esc_html__('From Settings', 'gt3pg_pro');

		$this->add_control(
			'showMetaDateFormat',
			array(
				'label'       => esc_html__('Show Meta Date Format', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => $formats,
				'default'     => 'system',
				'condition'   => array(
					'showMeta'     => '1',
					'showMetaDate' => '1',
				),
			)
		);

		$this->add_control(
			'lazyLoad',
			array(
				'label'       => esc_html__('Lazy Load', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '0',
			)
		);
		$this->add_control(
			'rightClick',
			array(
				'label'       => esc_html__('Right Click Guard', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '0',
			)
		);
		$this->add_control(
			'imageSize',
			array(
				'label'       => esc_html__('Select Image Size', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'medium'          => esc_html__('Medium (300px)', 'gt3pg_pro'),
					'medium_large'    => esc_html__('Thumbnail (768px)', 'gt3pg_pro'),
					'large'           => esc_html__('Large (1024px)', 'gt3pg_pro'),
					'gt3pg_optimized' => esc_html__('Optimized', 'gt3pg_pro'),
					'full'            => esc_html__('Full Size', 'gt3pg_pro'),
				),
				'default'     => 'medium_large',
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'       => esc_html__('Columns', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('1', 'gt3pg_pro'),
					'2' => esc_html__('2', 'gt3pg_pro'),
					'3' => esc_html__('3', 'gt3pg_pro'),
					'4' => esc_html__('4', 'gt3pg_pro'),
					'5' => esc_html__('5', 'gt3pg_pro'),
					'6' => esc_html__('6', 'gt3pg_pro'),
					'7' => esc_html__('7', 'gt3pg_pro'),
					'8' => esc_html__('8', 'gt3pg_pro'),
					'9' => esc_html__('9', 'gt3pg_pro'),
				),
				'default'     => '3',
				'condition'   => array(
					'albumType!' => 'packery',
				),
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
			'cornersType',
			array(
				'label'       => esc_html__('Corners Type', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'standard' => esc_html__('Standard', 'gt3pg_pro'),
					'rounded'  => esc_html__('Rounded', 'gt3pg_pro'),
				),
				'default'     => 'standard',
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
					'1' => esc_html__('Enabled', 'gt3pg_pro'),
					'0' => esc_html__('Disabled', 'gt3pg_pro'),
				),
				'default'     => '0',
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
