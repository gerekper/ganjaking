<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Products Filter Widget
 *
 * Porto Elementor widget to display a list of select boxes to filter products by category, price or attributes.
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Products_Filter_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_products_filter';
	}

	public function get_title() {
		return __( 'Porto Products Filter', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'products', 'filter', 'select', 'price', 'category', 'attribute', 'woocommerce' );
	}

	public function get_icon() {
		return 'eicon-filter';
	}

	protected function _register_controls() {

		$filter_areas         = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$filter_areas[ $tax->attribute_name ] = $tax->attribute_name;
				}
			}
		}

		$this->start_controls_section(
			'section_products_filter',
			array(
				'label' => __( 'Products Filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'filter_areas',
			array(
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Filter Areas', 'porto-functionality' ),
				'options'  => array_merge(
					array(
						'category' => __( 'Category', 'porto-functionality' ),
						'price'    => __( 'Price', 'porto-functionality' ),
					),
					$filter_areas
				),
				'multiple' => true,
			)
		);

		$this->add_control(
			'filter_titles',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Filter Titles', 'porto-functionality' ),
				'description' => __( 'comma separated list of titles', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'price_range',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Price Range', 'porto-functionality' ),
				'description' => __( 'Example: 0-10, 10-100, 100-200, 200-500', 'porto-functionality' ),
				'condition'   => array(
					'filter_areas' => 'price',
				),
			)
		);

		$this->add_control(
			'price_format',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Price Format', 'porto-functionality' ),
				'description' => __( 'Example: $from to $to', 'porto-functionality' ),
				'condition'   => array(
					'filter_areas' => 'price',
				),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Hide empty categories/attributes', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'display_type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Display type', 'woocommerce' ),
				'options' => array(
					''     => __( 'Dropdown', 'woocommerce' ),
					'list' => __( 'List', 'woocommerce' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'query_type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Query type', 'woocommerce' ),
				'options' => array(
					'and' => __( 'AND', 'woocommerce' ),
					'or'  => __( 'OR', 'woocommerce' ),
				),
				'default' => 'and',
			)
		);

		$this->add_control(
			'submit_value',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Submit Button Text', 'porto-functionality' ),
				'condition' => array(
					'display_type' => '',
				),
			)
		);

		$this->add_control(
			'submit_class',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Submit Button Class', 'porto-functionality' ),
				'condition' => array(
					'display_type' => '',
				),
			)
		);

		$this->add_control(
			'el_class',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Custom CSS Class', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_woo_template( 'porto_products_filter' ) ) {
			include $template;
		}
	}
}
