<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Product Categories Widget
 *
 * Porto Elementor widget to display products.
 *
 * @since 5.2.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Product_Categories_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_product_categories';
	}

	public function get_title() {
		return __( 'Porto Product Categories', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'product categories', 'shop', 'woocommerce' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js', 'isotope' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );
		$slider_options   = porto_update_vc_options_to_elementor( porto_vc_product_slider_fields() );

		$slider_options['nav_pos2']['condition']['navigation']       = 'yes';
		$slider_options['nav_type']['condition']['navigation']       = 'yes';
		$slider_options['autoplay_timeout']['condition']['autoplay'] = 'yes';

		$this->start_controls_section(
			'section_product_categories',
			array(
				'label' => __( 'Products Selector', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'parent',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Parent Category ID', 'porto-functionality' ),
				'options'     => 'product_cat',
				'label_block' => true,
			)
		);

		$this->add_control(
			'ids',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
				'multiple'    => 'true',
				'options'     => 'product_cat',
				'label_block' => true,
			)
		);

		$this->add_control(
			'number',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Categories Count', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'description' => __( 'The `number` field is used to display the number of products.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Hide empty', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => array(
					'name'        => __( 'Title', 'porto-functionality' ),
					'term_id'     => __( 'ID', 'porto-functionality' ),
					'count'       => __( 'Product Count', 'porto-functionality' ),
					'none'        => __( 'None', 'porto-functionality' ),
					'parent'      => __( 'Parent', 'porto-functionality' ),
					'description' => __( 'Description', 'porto-functionality' ),
					'term_group'  => __( 'Term Group', 'porto-functionality' ),
				),
				/* translators: %s: Wordpress codex page */
				'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order way', 'porto-functionality' ),
				'options'     => array_combine( array_values( $order_way_values ), array_keys( $order_way_values ) ),
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_layout',
			array(
				'label' => __( 'Products Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => __( 'View', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'            => __( 'Grid', 'porto-functionality' ),
					'products-slider' => __( 'Slider', 'porto-functionality' ),
					'creative'        => __( 'Grid - Creative', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'grid_layout',
			array(
				'label'     => __( 'Grid Layout', 'porto-functionality' ),
				'type'      => 'image_choose',
				'default'   => '1',
				'options'   => array_combine( array_values( porto_sh_commons( 'masonry_layouts' ) ), array_keys( porto_sh_commons( 'masonry_layouts' ) ) ),
				'condition' => array(
					'view' => 'creative',
				),
			)
		);

		$this->add_control(
			'grid_height',
			array(
				'label'     => __( 'Grid Height', 'porto-functionality' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '600px',
				'condition' => array(
					'view' => 'creative',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Column Spacing (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'condition' => array(
					'view' => array( 'creative' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'products-slider', 'grid' ),
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
					'view' => array( 'products-slider', 'grid' ),
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
					'view' => array( 'products-slider', 'grid' ),
				),
				'options'   => array_combine( array_values( porto_sh_commons( 'products_column_width' ) ), array_keys( porto_sh_commons( 'products_column_width' ) ) ),
			)
		);

		$this->add_control(
			'text_position',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Text Position', 'porto-functionality' ),
				'options' => array(
					'middle-left'    => __( 'Inner Middle Left', 'porto-functionality' ),
					'middle-center'  => __( 'Inner Middle Center', 'porto-functionality' ),
					'middle-right'   => __( 'Inner Middle Right', 'porto-functionality' ),
					'bottom-left'    => __( 'Inner Bottom Left', 'porto-functionality' ),
					'bottom-center'  => __( 'Inner Bottom Center', 'porto-functionality' ),
					'bottom-right'   => __( 'Inner Bottom Right', 'porto-functionality' ),
					'outside-center' => __( 'Outside', 'porto-functionality' ),
				),
				'default' => 'middle-center',
			)
		);

		$this->add_control(
			'overlay_bg_opacity',
			array(
				'type'    => Controls_Manager::SLIDER,
				'label'   => __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
				'range'   => array(
					'%' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'default' => array(
					'unit' => '%',
					'size' => 15,
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Text Color', 'porto-functionality' ),
				'options' => array(
					'dark'  => __( 'Dark', 'porto-functionality' ),
					'light' => __( 'Light', 'porto-functionality' ),
				),
				'default' => 'light',
			)
		);

		$this->add_control(
			'media_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Media Type', 'porto-functionality' ),
				'description' => __( 'If you want to use icon type, you need to input category icon in categoriy edit page.', 'porto-functionality' ),
				'options'     => array(
					''     => __( 'Image', 'porto-functionality' ),
					'icon' => __( 'Icon', 'porto-functionality' ),
					'none' => __( 'None', 'porto-functionality' ),
				),
				'default'     => '',
			)
		);

		$this->add_control(
			'show_sub_cats',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Display sub categories', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'show_featured',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Display a featured product', 'porto-functionality' ),
				'description' => __( 'If you check this option, a featured product in each category will be displayed under the product category.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'hide_count',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Hide products count', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Hover Effect', 'porto-functionality' ),
				'options' => array(
					''                    => __( 'Normal', 'porto-functionality' ),
					'show-count-on-hover' => __( 'Display product count on hover', 'porto-functionality' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Image Size', 'porto-functionality' ),
				'options'   => array_combine( array_values( porto_sh_commons( 'image_sizes' ) ), array_keys( porto_sh_commons( 'image_sizes' ) ) ),
				'default'   => '',
				'condition' => array(
					'view' => array( 'products-slider', 'grid' ),
				),
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

		$this->add_control(
			'stage_padding',
			array(
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Stage Padding', 'porto-functionality' ),
				'description' => 'unit: px',
				'condition'   => array(
					'view' => 'products-slider',
				),
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			$this->add_control( $key, $opt );
		}

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_woo_template( 'porto_product_categories' ) ) {
			if ( ! empty( $atts['parent'] ) && is_array( $atts['parent'] ) ) {
				$atts['parent'] = implode( ',', $atts['parent'] );
			}
			if ( ! empty( $atts['ids'] ) && is_array( $atts['ids'] ) ) {
				$atts['ids'] = implode( ',', $atts['ids'] );
			}
			include $template;
		}
	}
}
