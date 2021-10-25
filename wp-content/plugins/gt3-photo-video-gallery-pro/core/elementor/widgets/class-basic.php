<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Elementor\Controls\Gallery as Control_Gallery;

abstract class Basic extends Widget_Base {

	/**
	 * array $params Options
	 *        boolean ['withCategories']        Enable Categories in query
	 *        boolean ['withCustomVideoLink']   Enable Custom Video Link
	 *        boolean ['withCustomLink']        Enable Custom Link
	 *
	 * @param array $params (See above)
	 *
	 * @return void
	 **/
	protected function imagesControls($params = array()){
		$params = array_merge(
			array(
				'withCategories'      => false,
				'withCustomVideoLink' => false,
				'withCustomLink'      => false,
				'withSearch'          => false,
			), $params
		);
		if(class_exists('GT3_Post_Type_Gallery')
		   && post_type_exists(GT3_Post_Type_Gallery::post_type)) {
			$options = array(
				'module'  => esc_html__('Media Library (WordPress media library)', 'gt3pg_pro'),
				'gallery' => esc_html__('Galleries (custom post type GT3 Galleries)', 'gt3pg_pro'),
			);
			if($params['withCategories']) {
				$options['categories'] = esc_html__('Categories (custom post type GT3 Galleries)', 'gt3pg_pro');
			}
			$this->add_control(
				'source',
				array(
					'label'       => esc_html__('Select Source', 'gt3pg_pro'),
					'type'        => Controls_Manager::SELECT,
					'options'     => $options,
					'default'     => 'module',
					'label_block' => true,
				)
			);

			$this->add_control(
				'gallery',
				array(
					'label'       => esc_html__('Select Gallery', 'gt3pg_pro'),
					'type'        => Controls_Manager::SELECT2,
					'options'     => GT3_Post_Type_Gallery::get_galleries(),
					'condition'   => array(
						'source' => 'gallery',
					),
					'label_block' => true,
				)
			);

			if($params['withCategories']) {
				$this->add_control(
					'categories',
					array(
						'label'       => esc_html__('Select Categories', 'gt3pg_pro'),
						'type'        => Controls_Manager::SELECT2,
						'options'     => GT3_Post_Type_Gallery::get_galleries_categories(),
						'multiple'    => true,
						'condition'   => array(
							'source' => 'categories',
						),
						'label_block' => true
					)
				);
			}

			$this->add_control(
				'ids',
				array(
					'type'                => Control_Gallery::TYPE,
					'condition'           => array(
						'source' => 'module',
					),
					'withCustomVideoLink' => $params['withCustomVideoLink'],
				)
			);

			if ($params['withSearch']) {
				$this->add_control(
					'search',
					array(
						'label'       => esc_html__('Use Search', 'gt3pg_pro'),
						'label_block' => true,
						'type'        => Controls_Manager::SELECT,
						'options'     => array(
							'default' => esc_html__('Default', 'gt3pg_pro'),
							'1'       => esc_html__('Yes', 'gt3pg_pro'),
							'0'       => esc_html__('No', 'gt3pg_pro'),
						),
						'default'     => 'default',
					)
				);
			}

			$this->add_control(
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
						'source'  => 'categories',
						'search!' => '1',
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
						'source'       => 'categories',
						'filterEnable' => '1',
						'search!'      => '1',
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
						'source'       => 'categories',
						'filterEnable' => '1',
						'search!'      => '1',
					),
				)
			);
		} else {
			$this->add_control(
				'ids',
				array(
					'type'            => Control_Gallery::TYPE,
					'customVideoLink' => $params['withCustomVideoLink'],
					'customLink'      => $params['withCustomLink'],
				)
			);
		}
	}

	protected function loadMoreControls(){
		$this->add_control(
			'loadMoreEnable',
			array(
				'label'       => esc_html__('Load More', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => esc_html__('Yes', 'gt3pg_pro'),
					'0' => esc_html__('No', 'gt3pg_pro'),
				),
				'default'     => '0',
			)
		);

		$this->add_control(
			'loadMoreButtonText',
			array(
				'label'       => esc_html__('Load More button', 'gt3pg_pro'),
				'label_block' => true,
				'default'     => esc_html__('Load More', 'gt3pg_pro'),
				'condition'   => array(
					'loadMoreEnable' => '1',
				),
			)
		);

		$this->add_control(
			'loadMoreFirst',
			array(
				'label'       => esc_html__('First Images', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
				'default'     => '12',
				'condition'   => array(
					'loadMoreEnable' => '1',
				),
			)
		);

		$this->add_control(
			'loadMoreLimit',
			array(
				'label'       => esc_html__('Load Images', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'default'     => '4',
				'min'         => '0',
				'condition'   => array(
					'loadMoreEnable' => '1',
				),
			)
		);
	}

	protected function lightboxControls(array $condition = array( 'linkTo' => 'lightbox' )){
		$this->start_controls_section(
			'lightboxSettings', array(
				'label'     => esc_html__('Lightbox Settings', 'gt3pg_pro'),
				'tab'       => Controls_Manager::TAB_SETTINGS,
				'condition' => $condition,
			)
		);

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
			'lightboxTheme',
			array(
				'label'       => esc_html__('Lightbox Theme', 'gt3pg_pro'),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__('Default', 'gt3pg_pro'),
					'dark'    => esc_html__('Dark', 'gt3pg_pro'),
					'light'   => esc_html__('Light', 'gt3pg_pro'),
				),
				'default'     => 'default',
			)
		);

		$this->add_control(
			'lightboxAutoplay',
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
			'lightboxAutoplayTime',
			array(
				'label'     => esc_html__('Autoplay Time (sec.)', 'gt3pg_pro'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 6,
				'condition' => array(
					'lightboxAutoplay' => 1,
				),
			)
		);

		$this->add_control(
			'lightboxThumbnails',
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
			'lightboxImageSize',
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
			'lightboxCover',
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
			'lightboxDeeplink',
			array(
				'label'       => esc_html__('Deeplink', 'gt3pg_pro'),
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
			'socials',
			array(
				'label'       => esc_html__('Social Links', 'gt3pg_pro'),
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
			'allowDownload',
			array(
				'label'       => esc_html__('Download Image', 'gt3pg_pro'),
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
			'lightboxAllowZoom',
			array(
				'label'       => esc_html__('Image Zoom', 'gt3pg_pro'),
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
	}

	function get_style_depends(){
		return array_merge(parent::get_style_depends(), array( 'gt3pg-pro-blocks-frontend', 'gt3pg-pro-blocks-frontend-css' )); // TODO: Change the autogenerated stub
	}
}

