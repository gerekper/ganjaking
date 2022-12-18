<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor One Page Category Products Widget
 *
 * Porto Elementor widget to display one page navigation of product categories and products by category.
 *
 * @since 1.6.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_One_Page_Category_Products_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_one_page_category_products';
	}

	public function get_title() {
		return __( 'Porto One Page Category Products', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'products', 'one-page', 'category', 'sticky', 'nav', 'one page', 'woocommerce' );
	}

	public function get_icon() {
		return 'eicon-products-archive';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {
		$order_by_values  = array_slice( porto_vc_woo_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );
		$order_by_values  = array_combine( array_values( $order_by_values ), array_keys( $order_by_values ) );
		$order_way_values = array_combine( array_values( $order_way_values ), array_keys( $order_way_values ) );
		$slider_options   = porto_update_vc_options_to_elementor( porto_vc_product_slider_fields( 'products-slider', 'dots-style-1' ) );

		$slider_options['nav_pos2']['condition']['navigation']       = 'yes';
		$slider_options['nav_type']['condition']['navigation']       = 'yes';
		$slider_options['autoplay_timeout']['condition']['autoplay'] = 'yes';

		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'description_one_page_category',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( esc_html__( 'Before using, please set the icon and image of product categories.', 'porto-functionality' ), '<b>', '</b>' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'category_orderby',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Category Order by', 'porto-functionality' ),
				'options' => array(
					''            => __( 'Default', 'porto-functionality' ),
					'name'        => __( 'Title', 'porto-functionality' ),
					'term_id'     => __( 'ID', 'porto-functionality' ),
					'count'       => __( 'Product Count', 'porto-functionality' ),
					'none'        => __( 'None', 'porto-functionality' ),
					'parent'      => __( 'Parent', 'porto-functionality' ),
					'description' => __( 'Description', 'porto-functionality' ),
					'term_group'  => __( 'Term Group', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'category_order',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Category Order way', 'porto-functionality' ),
				'options' => $order_way_values,
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Hide empty categories', 'porto-functionality' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_products',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Show Products', 'porto-functionality' ),
				'default'     => 'yes',
				'description' => __( 'If you uncheck this option, only category lists will be displayed on the left side and products will not be displayed. If you click category in the list, it will redirect to that page.', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'product_option',
			array(
				'label'     => __( 'Product Layout', 'porto-functionality' ),
				'condition' => array(
					'show_products' => 'yes',
				),
			)
		);

		$this->add_control(
			'infinite_scroll',
			array(
				'type'        => Controls_Manager::HIDDEN,
				'label'       => __( 'Ajax load', 'porto-functionality' ),
				'description' => __( 'Show category products one by one category using ajax infinite load when the page is scrolling to the bottom.', 'porto-functionality' ),
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => __( 'View mode', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'products-slider' => __( 'Carousel', 'porto-functionality' ),
					'grid'            => __( 'Grid', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'count',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Count.', 'porto-functionality' ),
				'description' => __( 'The number of products in a category.', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'show_products' => 'yes',
					'view'          => array( 'products-slider', 'grid' ),
				),
				'default'   => '4',
				'options'   => porto_sh_commons( 'products_columns' ),
			)
		);

		$this->add_control(
			'columns_mobile',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
				'condition' => array(
					'show_products' => 'yes',
					'view'          => array( 'products-slider', 'grid' ),
				),
				'default'   => '',
				'options'   => array(
					''  => __( 'Default', 'porto-functionality' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			)
		);

		$this->add_control(
			'column_width',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Column Width', 'porto-functionality' ),
				'condition' => array(
					'show_products' => 'yes',
					'view'          => array( 'products-slider', 'grid' ),
				),
				'options'   => array_combine( array_values( porto_sh_commons( 'products_column_width' ) ), array_keys( porto_sh_commons( 'products_column_width' ) ) ),
			)
		);

		$this->add_control(
			'product_orderby',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Order by', 'porto-functionality' ),
				'options' => $order_by_values,
			)
		);

		$this->add_control(
			'product_order',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Order way', 'porto-functionality' ),
				'options' => $order_way_values,
			)
		);

		$this->add_control(
			'addlinks_pos',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Layout', 'porto-functionality' ),
				'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
				'options'     => array_combine( array_values( porto_sh_commons( 'products_addlinks_pos' ) ), array_keys( porto_sh_commons( 'products_addlinks_pos' ) ) ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Image Size', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'image_sizes' ) ), array_keys( porto_sh_commons( 'image_sizes' ) ) ),
				'default' => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Slider Options', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => 'products-slider',
				),
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			if( ! empty( $opt['responsive'] ) ) {
				$this->add_responsive_control( $key, $opt );
			} else {
				$this->add_control( $key, $opt );
			}
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_position',
			array(
				'label'     => __( 'Position', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_products' => 'yes',
				),
			)
		);

		$this->add_control(
			'sticky_pos',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Position Fixed?', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'sticky_top',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Top', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'%'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'condition'  => array(
					'sticky_pos' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-fix-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sticky_left',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Left', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'%'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'condition'  => array(
					'sticky_pos' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-fix-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sticky_bottom',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Bottom', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'%'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'condition'  => array(
					'sticky_pos' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-fix-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sticky_right',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Right', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'%'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'condition'  => array(
					'sticky_pos' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-fix-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_list_style',
			array(
				'label' => __( 'List Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'space_top',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Top Space', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-top-space: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_space_vertical',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Space between the category list items', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-space-vertical: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_space_horizontal',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Space between the category list items (< 768px)', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--porto-one-page-category-space-horizontal: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_bg',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-onepage-category > .category-list' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		
		if ( $template = porto_shortcode_woo_template( 'porto_one_page_category_products' ) ) {
			if ( ! empty( $atts['count'] ) ) {
				$atts['count'] = $atts['count']['size'];
			}
			include $template;
		}
	}
}
