<?php
/**
 * UAEL WooCommerce Add To Cart Button.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Categories.
 */
class Woo_Categories extends Common_Widget {

	/**
	 * Retrieve Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Woo_Categories' );
	}

	/**
	 * Retrieve Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Categories' );
	}

	/**
	 * Retrieve Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Categories' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Woo_Categories' );
	}

	/**
	 * Get Script Depends.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array scripts.
	 */
	public function get_script_depends() {
		return array( 'imagesloaded', 'uael-slick', 'uael-woocommerce' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		/* Product Control */
		$this->register_content_general_controls();
		$this->register_content_grid_controls();
		$this->register_content_slider_controls();
		$this->register_content_filter_controls();
		$this->register_helpful_information();

		/* Style */
		$this->register_style_layout_controls();
		$this->register_style_category_controls();
		$this->register_style_category_desc_controls();
		$this->register_style_navigation_controls();

	}

	/**
	 * Register Woo Products General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_general_controls() {

		$this->start_controls_section(
			'section_general_field',
			array(
				'label' => __( 'General', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'products_layout_type',
				array(
					'label'        => __( 'Layout', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'grid',
					'options'      => array(
						'grid'   => __( 'Grid', 'uael' ),
						'slider' => __( 'Carousel', 'uael' ),
					),
					'prefix_class' => 'uael-woo-category-',
					'render_type'  => 'template',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register grid Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_grid_controls() {
		$this->start_controls_section(
			'section_grid_options',
			array(
				'label'     => __( 'Grid Options', 'uael' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'products_layout_type' => 'grid',
				),
			)
		);
			$this->add_responsive_control(
				'cat_columns',
				array(
					'label'              => __( 'Columns', 'uael' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => '4',
					'tablet_default'     => '3',
					'mobile_default'     => '2',
					'options'            => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'condition'          => array(
						'products_layout_type' => 'grid',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'cats_count',
				array(
					'label'     => __( 'Categories Count', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => '8',
					'condition' => array(
						'products_layout_type' => 'grid',
					),
				)
			);

		$this->add_control(
			'cat_hide_count',
			array(
				'label'                => __( 'Hide Count', 'uael' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => __( 'Yes', 'uael' ),
				'label_off'            => __( 'No', 'uael' ),
				'return_value'         => 'yes',
				'default'              => 'no',
				'selectors_dictionary' => array(
					'yes' => 'display: none',
				),
				'selectors'            => array(
					'{{WRAPPER}} .uael-woo-categories .uael-category__title-wrap .uael-count' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cat_title_position',
			array(
				'label'        => __( 'Title/Count Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => array(
					'default'     => __( 'Default', 'uael' ),
					'below-image' => __( 'Below Image', 'uael' ),
				),
				'prefix_class' => 'uael-woo-cat-title-pos-',
			)
		);

		$this->add_control(
			'cat_title_style',
			array(
				'label'        => __( 'Title/Count Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => array(
					'default' => __( 'Default', 'uael' ),
					'inline'  => __( 'Inline', 'uael' ),
				),
				'prefix_class' => 'uael-woo-cat-title-style-',
				'condition'    => array(
					'cat_hide_count!' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Slider Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_slider_controls() {
		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Slider Options', 'uael' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'products_layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'     => __( 'Navigation', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => array(
					'both'   => __( 'Arrows and Dots', 'uael' ),
					'arrows' => __( 'Arrows', 'uael' ),
					'dots'   => __( 'Dots', 'uael' ),
					'none'   => __( 'None', 'uael' ),
				),
				'condition' => array(
					'products_layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_products_per_page',
			array(
				'label'       => __( 'Total Categories', 'uael' ),
				'description' => __( 'Note: <b>Total Categories</b> should be greater than <b>Categories to Show</b>.', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '8',
				'condition'   => array(
					'products_layout_type' => 'slider',
				),
			)
		);

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'              => __( 'Categories to Show', 'uael' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 4,
				'tablet_default'     => 3,
				'mobile_default'     => 1,
				'condition'          => array(
					'products_layout_type' => 'slider',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'              => __( 'Categories to Scroll', 'uael' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'condition'          => array(
					'products_layout_type' => 'slider',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'products_layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Speed', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'selectors' => array(
					'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
				),
				'condition' => array(
					'products_layout_type' => 'slider',
					'autoplay'             => 'yes',
				),
			)
		);
		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause on Hover', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'products_layout_type' => 'slider',
					'autoplay'             => 'yes',
				),
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => __( 'Infinite Loop', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'products_layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'transition_speed',
			array(
				'label'     => __( 'Transition Speed (ms)', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'products_layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'cat_slide_hide_count',
			array(
				'label'                => __( 'Hide Count', 'uael' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => __( 'Yes', 'uael' ),
				'label_off'            => __( 'No', 'uael' ),
				'return_value'         => 'yes',
				'default'              => 'no',
				'selectors_dictionary' => array(
					'yes' => 'display: none',
				),
				'selectors'            => array(
					'{{WRAPPER}} .uael-woo-categories .uael-category__title-wrap .uael-count' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cat_slide_title_position',
			array(
				'label'        => __( 'Title/Count Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => array(
					'default'     => __( 'Default', 'uael' ),
					'below-image' => __( 'Below Image', 'uael' ),
				),
				'prefix_class' => 'uael-woo-cat-title-pos-',
			)
		);

		$this->add_control(
			'cat_slide_title_style',
			array(
				'label'        => __( 'Title/Count Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => array(
					'default' => __( 'Default', 'uael' ),
					'inline'  => __( 'Inline', 'uael' ),
				),
				'prefix_class' => 'uael-woo-cat-title-style-',
				'condition'    => array(
					'cat_slide_hide_count!' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Navigation Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_navigation_controls() {
		$this->start_controls_section(
			'section_style_navigation',
			array(
				'label'     => __( 'Navigation', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'arrows', 'dots', 'both' ),
				),
			)
		);

		$this->add_control(
			'heading_style_arrows',
			array(
				'label'     => __( 'Arrows', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'arrows', 'both' ),
				),
			)
		);

		$this->add_control(
			'arrows_position',
			array(
				'label'        => __( 'Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'outside',
				'options'      => array(
					'inside'  => __( 'Inside', 'uael' ),
					'outside' => __( 'Outside', 'uael' ),
				),
				'prefix_class' => 'uael-woo-cat-arrow-',
				'condition'    => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'arrows', 'both' ),
				),
			)
		);

		$this->add_control(
			'arrows_style',
			array(
				'label'        => __( 'Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'circle',
				'options'      => array(
					''       => __( 'Default', 'uael' ),
					'circle' => __( 'Circle', 'uael' ),
					'square' => __( 'Square', 'uael' ),
				),
				'prefix_class' => 'uael-woo-cat-arrow-',
				'condition'    => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'arrows', 'both' ),
				),
			)
		);

		$this->add_control(
			'arrows_size',
			array(
				'label'     => __( 'Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 20,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-woo-category-slider .slick-slider .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'arrows', 'both' ),
				),
			)
		);

		$this->start_controls_tabs( 'arrow_tabs_style' );
			$this->start_controls_tab(
				'arrow_style_normal',
				array(
					'label'     => __( 'Normal', 'uael' ),
					'condition' => array(
						'products_layout_type' => 'slider',
						'navigation'           => array( 'arrows', 'both' ),
					),
				)
			);
				$this->add_control(
					'arrows_color',
					array(
						'label'     => __( 'Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}.uael-woo-category-slider .slick-slider .slick-arrow' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'products_layout_type' => 'slider',
							'navigation'           => array( 'arrows', 'both' ),
						),
					)
				);
				$this->add_control(
					'arrows_bg_color',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}.uael-woo-category-slider .slick-slider .slick-arrow' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							'products_layout_type' => 'slider',
							'navigation'           => array( 'arrows', 'both' ),
							'arrows_style'         => array( 'circle', 'square' ),
						),
					)
				);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'arrow_style_hover',
				array(
					'label'     => __( 'Hover', 'uael' ),
					'condition' => array(
						'products_layout_type' => 'slider',
						'navigation'           => array( 'arrows', 'both' ),
					),
				)
			);
				$this->add_control(
					'arrows_hover_color',
					array(
						'label'     => __( 'Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}.uael-woo-category-slider .slick-slider .slick-arrow:hover' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'products_layout_type' => 'slider',
							'navigation'           => array( 'arrows', 'both' ),
						),
					)
				);
				$this->add_control(
					'arrows_hover_bg_color',
					array(
						'label'     => __( 'Background Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}.uael-woo-category-slider .slick-slider .slick-arrow:hover' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							'products_layout_type' => 'slider',
							'navigation'           => array( 'arrows', 'both' ),
							'arrows_style'         => array( 'circle', 'square' ),
						),
					)
				);
			$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'heading_style_dots',
			array(
				'label'     => __( 'Dots', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'dots', 'both' ),
				),
			)
		);

		$this->add_control(
			'dots_size',
			array(
				'label'     => __( 'Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 5,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-woo-category-slider .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'dots', 'both' ),
				),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.uael-woo-category-slider .slick-dots li button:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'products_layout_type' => 'slider',
					'navigation'           => array( 'dots', 'both' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo Products Filter Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_filter_controls() {

		$this->start_controls_section(
			'section_filter_field',
			array(
				'label' => __( 'Filters', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'category_filter_rule',
				array(
					'label'   => __( 'Category Filter Rule', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'all',
					'options' => array(
						'all'     => __( 'Show All', 'uael' ),
						'top'     => __( 'Only Top Level', 'uael' ),
						'include' => __( 'Match These Categories', 'uael' ),
						'exclude' => __( 'Exclude These Categories', 'uael' ),
					),
				)
			);
			$this->add_control(
				'category_filter',
				array(
					'label'       => __( 'Category Filter', 'uael' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'default'     => '',
					'options'     => $this->get_product_categories(),
					'condition'   => array(
						'category_filter_rule' => array( 'include', 'exclude' ),
					),
				)
			);
			$this->add_control(
				'display_cat_desc',
				array(
					'label'        => __( 'Display Category Description', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => 'Yes',
					'label_off'    => 'No',
					'return_value' => 'yes',
				)
			);
			$this->add_control(
				'display_empty_cat',
				array(
					'label'        => __( 'Display Empty Categories', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => 'Yes',
					'label_off'    => 'No',
					'return_value' => 'yes',
				)
			);
			$this->add_control(
				'orderby',
				array(
					'label'   => __( 'Order by', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'name',
					'options' => array(
						'name'       => __( 'Name', 'uael' ),
						'slug'       => __( 'Slug', 'uael' ),
						'desc'       => __( 'Description', 'uael' ),
						'count'      => __( 'Count', 'uael' ),
						'menu_order' => __( 'Menu Order', 'uael' ),
					),
				)
			);

			$this->add_control(
				'order',
				array(
					'label'   => __( 'Order', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => array(
						'desc' => __( 'Descending', 'uael' ),
						'asc'  => __( 'Ascending', 'uael' ),
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Style Tab
	 */
	/**
	 * Register Layout Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => __( 'Layout', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'              => __( 'Columns Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 20,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-categories li.product' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .uael-woo-categories ul.products' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'              => __( 'Rows Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 35,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-categories li.product' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'products_layout_type' => 'grid',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Category Content Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_category_controls() {
		$this->start_controls_section(
			'section_design_cat_content',
			array(
				'label' => __( 'Category Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'category_name_tag',
			array(
				'label'   => __( 'HTML Tag', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1' => __( 'H1', 'uael' ),
					'h2' => __( 'H2', 'uael' ),
					'h3' => __( 'H3', 'uael' ),
					'h4' => __( 'H4', 'uael' ),
					'h5' => __( 'H5', 'uael' ),
					'h6' => __( 'H6', 'uael' ),
				),
				'default' => 'h2',
			)
		);

			$this->add_control(
				'cat_content_alignment',
				array(
					'label'        => __( 'Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'label_block'  => false,
					'options'      => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'      => 'center',
					'prefix_class' => 'uael-woo-cat--align-',
					'separator'    => 'after',
				)
			);

			$this->start_controls_tabs( 'cat_content_tabs_style' );

				$this->start_controls_tab(
					'cat_content_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'cat_content_color',
						array(
							'label'     => __( 'Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories li.product .woocommerce-loop-category__title, {{WRAPPER}} .uael-woo-categories li.product .uael-category__title-wrap .uael-count' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'cat_content_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories li.product .uael-category__title-wrap' => 'background-color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'cat_content_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'cat_content_hover_color',
						array(
							'label'     => __( 'Text Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories li.product-category > a:hover .woocommerce-loop-category__title, {{WRAPPER}} .uael-woo-categories li.product-category > a:hover .uael-category__title-wrap .uael-count' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'cat_content_background_hover_color',
						array(
							'label'     => __( 'Background Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories li.product-category > a:hover .uael-category__title-wrap' => 'background-color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'cat_content_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'default'    => array(
						'top'      => '10',
						'right'    => '',
						'bottom'   => '10',
						'left'     => '',
						'isLinked' => false,
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-woo-categories li.product .uael-category__title-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_control(
				'cat_content_typography',
				array(
					'label'     => __( 'Typography', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'cat_content_title_typography',
					'label'    => __( 'Title', 'uael' ),
					'selector' => '{{WRAPPER}} .uael-woo-categories li.product .woocommerce-loop-category__title',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'cat_content_count_typography',
					'label'     => __( 'Count', 'uael' ),
					'selector'  => '{{WRAPPER}} .uael-woo-categories li.product .uael-category__title-wrap .uael-count',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'separator' => 'after',
					'condition' => array(
						'products_layout_type' => 'grid',
						'cat_hide_count!'      => 'yes',
					),
				)
			);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cat_slider_content_count_typography',
				'label'     => __( 'Count', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-woo-categories li.product .uael-category__title-wrap .uael-count',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'separator' => 'after',
				'condition' => array(
					'products_layout_type'  => 'slider',
					'cat_slide_hide_count!' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Category Description Content Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_category_desc_controls() {

		$this->start_controls_section(
			'section_design_cat_desc',
			array(
				'label'     => __( 'Category Description', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_cat_desc' => 'yes',
				),
			)
		);
			$this->add_control(
				'cat_desc_alignment',
				array(
					'label'       => __( 'Alignment', 'uael' ),
					'type'        => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options'     => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'     => 'left',
					'selectors'   => array(
						'{{WRAPPER}} .uael-woo-categories .uael-term-description' => 'text-align: {{VALUE}};',
					),
					'condition'   => array(
						'display_cat_desc' => 'yes',
					),
				)
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'cat_desc_typography',
					'label'     => '',
					'selector'  => '{{WRAPPER}} .uael-woo-categories .uael-term-description',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'condition' => array(
						'display_cat_desc' => 'yes',
					),
				)
			);

			$this->start_controls_tabs( 'desc_tabs_style' );

				$this->start_controls_tab(
					'desc_normal',
					array(
						'label'     => __( 'Normal', 'uael' ),
						'condition' => array(
							'display_cat_desc' => 'yes',
						),
					)
				);
					$this->add_control(
						'cat_desc_color',
						array(
							'label'     => __( 'Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_TEXT,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories .uael-term-description' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'display_cat_desc' => 'yes',
							),
						)
					);
					$this->add_control(
						'cat_desc_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories .uael-product-cat-desc' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'display_cat_desc' => 'yes',
							),
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'desc_hover',
					array(
						'label'     => __( 'Hover', 'uael' ),
						'condition' => array(
							'display_cat_desc' => 'yes',
						),
					)
				);
					$this->add_control(
						'cat_desc_hover_color',
						array(
							'label'     => __( 'Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_TEXT,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories li.product-category > a:hover .uael-term-description' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'display_cat_desc' => 'yes',
							),
						)
					);
					$this->add_control(
						'cat_desc_background__hover_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-woo-categories li.product-category > a:hover .uael-product-cat-desc' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'display_cat_desc' => 'yes',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'cat_desc_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'default'    => array(
						'top'      => '15',
						'right'    => '15',
						'bottom'   => '15',
						'left'     => '15',
						'isLinked' => true,
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-woo-categories .uael-product-cat-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'display_cat_desc' => 'yes',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.1.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/woo-categories-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		$help_link_2 = UAEL_DOMAIN . 'docs/how-to-set-description-for-category-in-woocommerce/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to set description for category? » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Get WooCommerce Product Categories.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function get_product_categories() {

		$product_cat = array();

		$cat_args = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$product_categories = get_terms( 'product_cat', $cat_args );

		if ( ! empty( $product_categories ) ) {

			foreach ( $product_categories as $key => $category ) {

				$product_cat[ $category->term_id ] = $category->name;
			}
		}

		return $product_cat;
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function set_slider_attr() {

		$settings = $this->get_settings();

		if ( 'slider' !== $settings['products_layout_type'] ) {
			return;
		}
		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) );
		$show_arrows = ( in_array( $settings['navigation'], array( 'arrows', 'both' ), true ) );

		$slick_options = array(
			'slidesToShow'   => ( $settings['slides_to_show'] ) ? $settings['slides_to_show'] : '4',
			'slidesToScroll' => ( $settings['slides_to_scroll'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => $show_arrows,
			'dots'           => $show_dots,
			'rtl'            => $is_rtl,
			'prevArrow'      => '<button type="button" data-role="none" class="slick-prev slick-arrow fa fa-angle-left" aria-label="Previous" role="button"></button>',
			'nextArrow'      => '<button type="button" data-role="none" class="slick-next slick-arrow fa fa-angle-right" aria-label="Next" role="button"></button>',
		);

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {

			$slick_options['responsive'] = array();

			if ( $settings['slides_to_show_tablet'] ) {

				$tablet_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_scroll = ( $settings['slides_to_scroll_tablet'] ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( $settings['slides_to_show_mobile'] ) {

				$mobile_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_scroll = ( $settings['slides_to_scroll_mobile'] ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$this->add_render_attribute(
			'cat-wrapper',
			array(
				'data-cat_slider' => wp_json_encode( $slick_options ),
			)
		);
	}

	/**
	 * List all product categories.
	 *
	 * @return string
	 */
	public function query_product_categories() {

		$settings    = $this->get_settings();
		$include_ids = array();
		$exclude_ids = array();
		$woo_cat_slider;

		if ( 'grid' === $settings['products_layout_type'] ) {
			$woo_cat_slider = $settings['cats_count'];
		} elseif ( 'slider' === $settings['products_layout_type'] ) {
			$woo_cat_slider = $settings['slider_products_per_page'];
		}

		$atts = array(
			'limit'   => ( $woo_cat_slider ) ? $woo_cat_slider : '-1',
			'columns' => ( $settings['cat_columns'] ) ? $settings['cat_columns'] : '4',
			'parent'  => '',
		);

		if ( 'top' === $settings['category_filter_rule'] ) {
			$atts['parent'] = 0;
		} elseif ( 'include' === $settings['category_filter_rule'] && is_array( $settings['category_filter'] ) ) {
			$include_ids = array_filter( array_map( 'trim', $settings['category_filter'] ) );
		} elseif ( 'exclude' === $settings['category_filter_rule'] && is_array( $settings['category_filter'] ) ) {
			$exclude_ids = array_filter( array_map( 'trim', $settings['category_filter'] ) );
		}

		$hide_empty = ( 'yes' === $settings['display_empty_cat'] ) ? 0 : 1;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => ( $settings['orderby'] ) ? $settings['orderby'] : 'name',
			'order'      => ( $settings['order'] ) ? $settings['order'] : 'ASC',
			'hide_empty' => $hide_empty,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
			'include'    => $include_ids,
			'exclude'    => $exclude_ids,
		);

		$product_categories = get_terms( 'product_cat', $args );

		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter(
				$product_categories,
				array(
					'parent' => $atts['parent'],
				)
			);
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( 0 === $category->count ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		$atts['limit'] = intval( $atts['limit'] );

		if ( $atts['limit'] > 0 ) {
			$product_categories = array_slice( $product_categories, 0, $atts['limit'] );
		}

		$columns = absint( $atts['columns'] );

		wc_set_loop_prop( 'columns', $columns );

		/* Category Link */
		remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		add_action( 'woocommerce_before_subcategory', array( $this, 'template_loop_category_link_open' ), 10 );

		/* Category Wrapper */
		add_action( 'woocommerce_before_subcategory', array( $this, 'category_wrap_start' ), 15 );
		add_action( 'woocommerce_after_subcategory', array( $this, 'category_wrap_end' ), 8 );

		if ( 'yes' === $settings['display_cat_desc'] ) {
			add_action( 'woocommerce_after_subcategory', array( $this, 'category_description' ), 8 );
		}

		/* Category Title */
		remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
		add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'template_loop_category_title' ), 10 );

		ob_start();

		if ( $product_categories ) {
			do_action( 'uael_before_product_loop_start' );
			woocommerce_product_loop_start();

			foreach ( $product_categories as $category ) {

				include UAEL_MODULES_DIR . 'woocommerce/templates/content-product-cat.php';
			}

			woocommerce_product_loop_end();
		}

		woocommerce_reset_loop();

		$inner_classes  = ' uael-woo-cat__column-' . $settings['cat_columns'];
		$inner_classes .= ' uael-woo-cat__column-tablet-' . $settings['cat_columns_tablet'];
		$inner_classes .= ' uael-woo-cat__column-mobile-' . $settings['cat_columns_mobile'];

		$inner_content = ob_get_clean();

		/* Category Link */
		add_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		remove_action( 'woocommerce_before_subcategory', array( $this, 'template_loop_category_link_open' ), 10 );

		/* Category Wrapper */
		remove_action( 'woocommerce_before_subcategory', array( $this, 'category_wrap_start' ), 15 );
		remove_action( 'woocommerce_after_subcategory', array( $this, 'category_wrap_end' ), 8 );

		if ( 'yes' === $settings['display_cat_desc'] ) {
			remove_action( 'woocommerce_after_subcategory', array( $this, 'category_description' ), 8 );
		}

		/* Category Title */
		remove_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'template_loop_category_title' ), 10 );
		add_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );

		return '<div class="uael-woo-categories-inner ' . $inner_classes . '">' . $inner_content . '</div>';
	}

	/**
	 * Wrapper Start.
	 *
	 * @param object $category Category object.
	 */
	public function template_loop_category_link_open( $category ) {
		$link = apply_filters( 'uael_woo_category_link', get_term_link( $category, 'product_cat' ) );

		echo '<a href="' . esc_url( $link ) . '">';
	}

	/**
	 * Wrapper Start.
	 *
	 * @param object $category Category object.
	 */
	public function category_wrap_start( $category ) {
		echo '<div class="uael-product-cat-inner">';
	}


	/**
	 * Wrapper End.
	 *
	 * @param object $category Category object.
	 */
	public function category_wrap_end( $category ) {
		echo '</div>';
	}

	/**
	 * Category Description.
	 *
	 * @param object $category Category object.
	 */
	public function category_description( $category ) {

		if ( $category && ! empty( $category->description ) ) {

			echo '<div class="uael-product-cat-desc">';
				echo '<div class="uael-term-description">' . wp_kses_post( wc_format_content( $category->description ) ) . '</div>';
			echo '</div>';
		}
	}


	/**
	 * Show the subcategory title in the product loop.
	 *
	 * @param object $category Category object.
	 */
	public function template_loop_category_title( $category ) {
		$settings              = $this->get_settings();
		$single_product_string = apply_filters( 'uael_woo_cat_product_string', __( 'Product', 'uael' ) );
		$product_string        = apply_filters( 'uael_woo_cat_products_string', __( 'Products', 'uael' ) );

		$text_tag = UAEL_Helper::validate_html_tag( $settings['category_name_tag'] );

		$output          = '<div class="uael-category__title-wrap">';
			$output     .= '<' . esc_attr( $text_tag ) . ' class="woocommerce-loop-category__title">';
				$output .= esc_html( $category->name );
			$output     .= '</' . esc_attr( $text_tag ) . '>';

		if ( $category->count > 0 ) {
				$output .= sprintf( // WPCS: XSS OK.
					/* translators: 1: number of products */
					_nx( '<mark class="uael-count">%1$s %2$s</mark>', '<mark class="uael-count">%1$s %3$s</mark>', $category->count, 'product categories', 'uael' ), // phpcs:ignore WordPress.WP.I18n.MismatchedPlaceholders, WordPress.WP.I18n.NoHtmlWrappedStrings
					number_format_i18n( $category->count ),
					$single_product_string,
					$product_string
				);
		}
		$output .= '</div>';

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();
		$node_id  = $this->get_id();
		$this->set_slider_attr();

		$out_html      = '<div class="uael-woo-categories uael-woo-categories-' . $settings['products_layout_type'] . ' uael-woocommerce"' . $this->get_render_attribute_string( 'cat-wrapper' ) . '>';
			$out_html .= $this->query_product_categories();
		$out_html     .= '</div>';

		echo $out_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
