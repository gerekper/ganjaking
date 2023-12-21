<?php
/**
 * Product Category Carousel widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;

defined( 'ABSPATH' ) || die();

class Edd_Category_Carousel extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Category Carousel', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-Category-Carousel';
	}

	public function get_keywords() {
		return [ 'ecommerce', 'edd', 'product', 'categroy', 'carousel', 'sale', 'ha-skin' ];
	}

	/**
	 * Overriding default function to add custom html class.
	 *
	 * @return string
	 */
	public function get_html_wrapper_class() {
		$html_class  = parent::get_html_wrapper_class();
		$html_class .= ' happy-addon-pro';
		$html_class .= ' ' . str_replace( '-new', '', $this->get_name() );
		return $html_class;
	}

	/**
	 * Get parent category list
	 */
	protected function get_parent_cats() {
		if ( ! function_exists( 'EDD' ) ) {
			return;
		}
		$parent_categories = [ 'none' => __( 'None', 'happy-addons-pro' ) ];

		$args        = array(
			'parent' => 0,
		);
		$parent_cats = get_terms( 'download_category', $args );

		foreach ( $parent_cats as $parent_cat ) {
			$parent_categories[ $parent_cat->term_id ] = $parent_cat->name;
		}
		return $parent_categories;
	}

	/**
	 * Get all category list
	 */
	protected function get_all_cats_list() {
		if ( ! function_exists( 'EDD' ) ) {
			return;
		}
		$cats_list = [];

		$args = [
			'orderby' => 'name',
			'order'   => 'DESC',
		];
		$cats = get_terms( 'download_category', $args );

		if ( $cats ) {
			foreach ( $cats as $cat ) {
				$cats_list[ $cat->term_id ] = $cat->name;
			}
		}

		return $cats_list;
	}

	/**
	 * Register content controls
	 */
	protected function register_content_controls() {

		//Layout content controls
		$this->layout_content_tab_controls();

		//Query content controls
		$this->query_content_tab_controls();

		//Carousel Settings controls
		$this->carousel_settings_content_tab_controls();

	}

	/**
	 * Layout content controls
	 */
	protected function layout_content_tab_controls() {

		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'skin',
			[
				'label'   => __( 'Skin', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'classic' => __( 'Classic', 'happy-addons-pro' ),
					'minimal' => __( 'Minimal', 'happy-addons-pro' ),
					'remote_carousel' => __( 'Remote Carousel', 'happy-addons-pro' ),
				],
				'default' => 'classic',
			]
		);

		$this->add_control(
			'edd_category_carousel_rcc_unique_id',
			[
				'label' => __( 'Unique ID', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter remote carousel unique id', 'happy-addons-pro' ),
                'description' => __('Input carousel ID that you want to remotely connect', 'happy-addons-pro'),
                'condition' => [ 'skin' => 'remote_carousel' ]
			]
		);

		$this->add_control(
			'cat_image_show',
			[
				'label'        => __( 'Generic Featured Image', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'cat_featured_image',
			[
				'label'     => esc_html__( 'Choose Image', 'happy-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'cat_image_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'cat_image',
				'default'   => 'thumbnail',
				'exclude'   => [
					'custom',
				],
				'condition' => [
					'cat_image_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label'                => __( 'Image Overlay', 'happy-addons-pro' ),
				'type'                 => Controls_Manager::SWITCHER,
				'default'              => '',
				'label_on'             => 'Yes',
				'label_off'            => 'No',
				'return_value'         => 'yes',
				'prefix_class'         => 'ha-image-overlay-',
				'selectors_dictionary' => [
					'yes' => 'content:\'\';',
				],
				'selectors'            => [
					'{{WRAPPER}} .ha-product-cat-carousel-thumbnail:before' => '{{VALUE}}',
				],
				'condition'            => [
					'cat_image_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_cats_count',
			[
				'label'        => __( 'Count Number', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => __( 'Title HTML Tag', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Query content controls
	 */
	protected function query_content_tab_controls() {

		$this->start_controls_section(
			'_term_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'query_type',
			[
				'label'   => __( 'Type', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => [
					'all'     => __( 'All', 'happy-addons-pro' ),
					'parents' => __( 'Only Parents', 'happy-addons-pro' ),
					'child'   => __( 'Only Child', 'happy-addons-pro' ),
				],
			]
		);

		$this->start_controls_tabs( '_tabs_terms_include_exclude',
			[
				'condition' => [ 'query_type' => 'all' ],
			]
		);
		$this->start_controls_tab(
			'_tab_term_include',
			[
				'label'     => __( 'Include', 'happy-addons-pro' ),
				'condition' => [ 'query_type' => 'all' ],
			]
		);

		$this->add_control(
			'cats_include_by_id',
			[
				'label'       => __( 'Categories', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [
					'query_type' => 'all',
				],
				'options'     => $this->get_all_cats_list(),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_term_exclude',
			[
				'label'     => __( 'Exclude', 'happy-addons-pro' ),
				'condition' => [ 'query_type' => 'all' ],
			]
		);

		$this->add_control(
			'cats_exclude_by_id',
			[
				'label'       => __( 'Categories', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [
					'query_type' => 'all',
				],
				'options'     => $this->get_all_cats_list(),
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'parent_cats',
			[
				'label'     => __( 'Child Categories of', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => $this->get_parent_cats(),
				'condition' => [
					'query_type' => 'child',
				],
			]
		);

		$this->add_control(
			'order_by',
			[
				'label'   => __( 'Order By', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'  => __( 'Name', 'happy-addons-pro' ),
					'count' => __( 'Count', 'happy-addons-pro' ),
					'slug'  => __( 'Slug', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'desc' => __( 'Descending', 'happy-addons-pro' ),
					'asc'  => __( 'Ascending', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'show_empty_cat',
			[
				'label'        => __( 'Show Empty Categories', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'cat_per_page',
			[
				'label' => __( 'Number of Categories', 'happy-addons-pro' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 1,
				'step'  => 1,
				'max'   => 1000,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Carousel Settings controls
	 */
	protected function carousel_settings_content_tab_controls() {

		$this->start_controls_section(
			'_section_settings',
			[
				'label' => __( 'Carousel Settings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label'              => __( 'Animation Speed', 'happy-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 100,
				'step'               => 10,
				'max'                => 10000,
				'default'            => 800,
				'description'        => __( 'Slide speed in milliseconds', 'happy-addons-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'              => __( 'Autoplay?', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'Yes', 'happy-addons-pro' ),
				'label_off'          => __( 'No', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'              => __( 'Autoplay Speed', 'happy-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 100,
				'step'               => 100,
				'max'                => 10000,
				'default'            => 2000,
				'description'        => __( 'Autoplay speed in milliseconds', 'happy-addons-pro' ),
				'condition'          => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'              => __( 'Infinite Loop?', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'Yes', 'happy-addons-pro' ),
				'label_off'          => __( 'No', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'              => __( 'Navigation', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'none'  => __( 'None', 'happy-addons-pro' ),
					'arrow' => __( 'Arrow', 'happy-addons-pro' ),
					'dots'  => __( 'Dots', 'happy-addons-pro' ),
					'both'  => __( 'Arrow & Dots', 'happy-addons-pro' ),
				],
				'default'            => 'arrow',
				'frontend_available' => true,
				'style_transfer'     => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'              => __( 'Slides To Show', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					1 => __( '1 Slide', 'happy-addons-pro' ),
					2 => __( '2 Slides', 'happy-addons-pro' ),
					3 => __( '3 Slides', 'happy-addons-pro' ),
					4 => __( '4 Slides', 'happy-addons-pro' ),
					5 => __( '5 Slides', 'happy-addons-pro' ),
					6 => __( '6 Slides', 'happy-addons-pro' ),
				],
				'desktop_default'    => 3,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'frontend_available' => true,
				'style_transfer'     => true,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Register style controls
	 */
	protected function register_style_controls() {

		//Item Box Style Start
		$this->item_box_style_tab_controls();

		//Feature Image Style Start
		$this->image_style_tab_controls();

		//Content Style Start
		$this->content_style_tab_controls();

		//Nav Arrow Style Start
		$this->nav_arrow_style_tab_controls();

		//Nav Dot Style Start
		$this->nav_dot_style_tab_controls();
	}

	/**
	 * Item Box Style controls
	 */
	protected function item_box_style_tab_controls() {

		$this->start_controls_section(
			'_section_item_box_style',
			[
				'label' => __( 'Carousel Item', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_heght',
			[
				'label'     => __( 'Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 250,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-product-cat-carousel-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .ha-product-cat-carousel-item-inner',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-product-cat-carousel-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-cat-carousel-item-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-product-cat-carousel-item-inner',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Image Style controls
	 */
	protected function image_style_tab_controls() {

		$this->start_controls_section(
			'_section_image_style',
			[
				'label'     => __( 'Image', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'cat_image_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_width',
			[
				'label'     => __( 'Width', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 2000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-thumbnail img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_height',
			[
				'label'     => __( 'Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 2000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-thumbnail img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-product-cat-carousel-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_overlay_color',
			[
				'label'     => __( 'Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'image_overlay' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-thumbnail:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content Style controls
	 */
	protected function content_style_tab_controls() {

		$this->start_controls_section(
			'_section_content_style',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->content_area_style_tab_controls();

		$this->title_style_tab_controls();

		$this->count_style_tab_controls();

		$this->end_controls_section();

	}

	/**
	 * Content area Style controls
	 */
	protected function content_area_style_tab_controls() {

		$this->add_control(
			'content_align',
			[
				'label'        => __( 'Alignment', 'happy-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'      => 'left',
				'prefix_class' => 'ha-product-cat-carousel-content-align-',
				'condition'    => [
					'skin' => 'minimal',
				],
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-product-cat-carousel-content-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-product-cat-carousel-content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-product-cat-carousel-content-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'content_item_border',
				'selector' => '{{WRAPPER}} .ha-product-cat-carousel-content-inner',
			]
		);

		$this->add_responsive_control(
			'content_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-product-cat-carousel-content-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

	}

	/**
	 * Title area Style controls
	 */
	protected function title_style_tab_controls() {

		$this->add_control(
			'_heading_title',
			[
				'label'     => __( 'Title', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-product-cat-carousel-title a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => __( 'Hover Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

	}

	/**
	 * Count area Style controls
	 */
	protected function count_style_tab_controls() {

		$this->add_control(
			'_heading_count',
			[
				'label'     => __( 'Count', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_cats_count' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'classic_count_space',
			[
				'label'     => __( 'Left Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-count' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin'            => 'classic',
					'show_cats_count' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'minimal_count_space',
			[
				'label'     => __( 'Top Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-count' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin'            => 'minimal',
					'show_cats_count' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'count_typography',
				'label'     => __( 'Typography', 'happy-addons-pro' ),
				'selector'  => '{{WRAPPER}} .ha-product-cat-carousel-count',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'show_cats_count' => 'yes',
				],
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-carousel-count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_cats_count' => 'yes',
				],
			]
		);

	}

	/**
	 * Nav Arrow style controls
	 */
	protected function nav_arrow_style_tab_controls() {

		$this->start_controls_section(
			'_section_style_arrow',
			[
				'label' => __( 'Navigation - Arrow', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrow_position_toggle',
			[
				'label'        => __( 'Position', 'happy-addons-pro' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'happy-addons-pro' ),
				'label_on'     => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'arrow_sync_position',
			[
				'label'        => __( 'Sync Position', 'happy-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
					'yes' => [
						'title' => __( 'Yes', 'happy-addons-pro' ),
						'icon'  => 'eicon-sync',
					],
					'no'  => [
						'title' => __( 'No', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'condition'    => [
					'arrow_position_toggle' => 'yes',
				],
				'default'      => 'no',
				'toggle'       => false,
				'prefix_class' => 'ha-arrow-sync-',
			]
		);

		$this->add_control(
			'sync_position_alignment',
			[
				'label'                => __( 'Alignment', 'happy-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition'            => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position'   => 'yes',
				],
				'default'              => 'center',
				'toggle'               => false,
				'selectors_dictionary' => [
					'left'   => 'left: 0',
					'center' => 'left: 50%',
					'right'  => 'left: 100%',
				],
				'selectors'            => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_y',
			[
				'label'      => __( 'Vertical', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'arrow_position_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_x',
			[
				'label'      => __( 'Horizontal', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'arrow_position_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 1200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.ha-arrow-sync-no .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-no .slick-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next, {{WRAPPER}}.ha-arrow-sync-yes .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_spacing',
			[
				'label'      => __( 'Space between Arrows', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position'   => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label'      => __( 'Box Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 70,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_font_size',
			[
				'label'      => __( 'Icon Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 2,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'arrow_border',
				'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
			]
		);

		$this->add_responsive_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_arrow' );

		$this->start_controls_tab(
			'_tab_arrow_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_arrow_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrow_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Nav Dot style controls
	 */
	protected function nav_dot_style_tab_controls() {

		$this->start_controls_section(
			'_section_style_dots',
			[
				'label' => __( 'Navigation - Dots', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'dots_nav_position_y',
			[
				'label'      => __( 'Vertical Position', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_spacing',
			[
				'label'      => __( 'Space Between', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_align',
			[
				'label'       => __( 'Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'      => true,
				'selectors'   => [
					'{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_dots' );
		$this->start_controls_tab(
			'_tab_dots_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_active_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get query
	 *
	 * @return object
	 */
	public function get_query() {
		$settings = $this->get_settings_for_display();

		$args = array(
			'orderby'    => ( $settings['order_by'] ) ? $settings['order_by'] : 'name',
			'order'      => ( $settings['order'] ) ? $settings['order'] : 'ASC',
			'hide_empty' => $settings['show_empty_cat'] == 'yes' ? false : true,
		);

		if ( $settings['query_type'] == 'all' ) {
			! empty( $settings['cats_include_by_id'] ) ? $args['include'] = $settings['cats_include_by_id'] : null;
			! empty( $settings['cats_exclude_by_id'] ) ? $args['exclude'] = $settings['cats_exclude_by_id'] : null;
		} elseif ( $settings['query_type'] == 'parents' ) {
			$args['parent'] = 0;
		} elseif ( $settings['query_type'] == 'child' ) {
			if ( $settings['parent_cats'] != 'none' && ! empty( $settings['parent_cats'] ) ) {
				$args['child_of'] = $settings['parent_cats'];
			} elseif ( $settings['parent_cats'] == 'none' ) {
				if ( is_admin() ) {
					return printf( '<div class="ha-category-carousel-error">%s</div>', __( 'Select Parent Category from <strong>Query > Child Categories of</strong>.', 'happy-addons-pro' ) );
				}
			}
		}

		$product_cats = get_terms( 'download_category', $args );
		if ( ! empty( $settings['cat_per_page'] ) && count( $product_cats ) > $settings['cat_per_page'] ) {
			$product_cats = array_splice( $product_cats, 0, $settings['cat_per_page'] );
		}

		return $product_cats;
	}



	/**
	 * render content
	 *
	 * @return void
	 */
	public function render() {

		if ( ! function_exists( 'EDD' ) ) {
			printf( '<div style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;">%s</div>', __( 'Easy Digital Downloads is missing! Please install and activate Easy Digital Downloads.', 'happy-addons-pro' ) );

			return;
		}

		$settings     = $this->get_settings_for_display();
		$product_cats = $this->get_query();

		if ( empty( $product_cats ) ) {
			if ( is_admin() ) {
				return printf( '<div class="ha-cat-carousel-error">%s</div>', __( 'Nothing Found. Please Add Category.', 'happy-addons-pro' ) );
			}
		}

		$this->add_render_attribute(
			'wrapper',
			'class',
			[
				'ha-product-cat-carousel',
				'ha-product-cat-carousel-' . $settings['skin'],
			]
		);

		$harcc_uid = !empty($settings['edd_category_carousel_rcc_unique_id']) && $settings['skin'] == 'remote_carousel' ? 'harccuid_' . $settings['edd_category_carousel_rcc_unique_id'] : '';

		?>

		<div data-ha_rcc_uid="<?php echo esc_attr( $harcc_uid ); ?>" <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			foreach ( $product_cats as $product_cat ) :

				$image_src = Utils::get_placeholder_image_src();
				// $thumbnail_id = get_term_meta( $product_cat->term_id, 'thumbnail_id', true );
				$thumbnail_id = isset( $settings['cat_featured_image'] ) ? $settings['cat_featured_image']['id'] : '';
				$image        = wp_get_attachment_image_src( $thumbnail_id, $settings['cat_image_size'], false );
				if ( $image ) {
					$image_src = $image[0];
				}

				$has_image = '';
				if ( 'yes' == $settings['cat_image_show'] ) {
					$has_image = esc_attr( ' ha-product-cat-carousel-has-image' );
				}
				?>
				<article class="ha-product-cat-carousel-item<?php echo esc_attr( ' ' . $has_image ); ?>">
					<div class="ha-product-cat-carousel-item-inner">
						<?php if ( $image_src && 'yes' == $settings['cat_image_show'] ) : ?>
							<div class="ha-product-cat-carousel-thumbnail">
								<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $product_cat->name ); ?>">
							</div>
						<?php endif; ?>
						<div class="ha-product-cat-carousel-content">
							<div class="ha-product-cat-carousel-content-inner">
								<<?php echo ha_escape_tags( $settings['title_tag'], 'h2' ) . ' class="ha-product-cat-carousel-title"'; ?>>
									<a href="<?php echo esc_url( get_term_link( $product_cat->term_id, 'download_category' ) ); ?>">
										<?php echo esc_html( $product_cat->name ); ?>
									</a>
								</<?php echo ha_escape_tags( $settings['title_tag'], 'h2' ); ?>>

								<?php if ( $settings['show_cats_count'] == 'yes' ) : ?>
									<?php
										$count_text = '';
									if ( 'classic' == $settings['skin'] ) {
										$count_text = '(' . $product_cat->count . ')';
									}
									if ( 'minimal' == $settings['skin'] ) {
										$count_text = $product_cat->count > 1 ? $product_cat->count . __( ' Items', 'happy-addons-pro' ) : $product_cat->count . __( ' Item', 'happy-addons-pro' );
									}
									?>
									<div class="ha-product-cat-carousel-count">
										<?php esc_html_e( $count_text ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</article>
				<?php
			endforeach;
			?>
		</div>

		<?php
	}

}
