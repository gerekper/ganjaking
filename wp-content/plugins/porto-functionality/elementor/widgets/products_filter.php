<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Products Filter Widget
 *
 * Porto Elementor widget to display a list of select boxes to filter products by category, price or attributes.
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

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

	protected function register_controls() {

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
				'separator'   => 'before',
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
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide empty categories/attributes', 'porto-functionality' ),
				'separator' => 'before',
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
			'submit_value',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Submit Button Text', 'porto-functionality' ),
				'condition' => array(
					'display_type' => '',
				),
				'separator' => 'before',
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
				'separator' => 'before',
			)
		);

		$this->add_control(
			'set_inline',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Set Inline', 'porto-functionality' ),
				'description' => __( 'If you set this option, you can control the width of selectors and button.', 'porto-functionality' ),
				'selectors'   => array(
					'{{WRAPPER}} .porto_products_filter_form' => 'display: flex; flex-wrap: wrap;',
					'{{WRAPPER}} .porto_products_filter_form .btn-submit' => 'margin-top: 0;',
				),
				'condition'   => array(
					'display_type' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'selector_style_option',
			array(
				'label'     => __( 'Select Style', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_type' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'select_typography',
				'label'    => esc_html__( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} select',
			)
		);

		$this->add_control(
			'selector_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} select' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'selector_bg_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} select' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_pos',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Dropdown Arrow Position (%)', 'porto-functionality' ),
				'range'      => array(
					'%' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'size_units' => array(
					'%',
				),
				'selectors'  => array(
					'{{WRAPPER}} select' => 'background-position: {{SIZE}}%;',
				),
			)
		);

		$this->add_control(
			'vertical_space',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Vertical Space', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} select' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'before',
				'condition'  => array(
					'set_inline' => '',
				),
			)
		);

		$this->add_control(
			'select_width',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Select Width (%)', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto_products_filter_form' => '--porto-product-filter-select-width: {{VALUE}}%;',
				),
				'default'   => '20',
				'condition' => array(
					'set_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'hr_space',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Horizontal Space', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .porto_products_filter_form' => '--porto-product-filter-space: {{SIZE}}{{UNIT}};',
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'condition'  => array(
					'set_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'select_width_md',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Select Width (< 992px) (%)', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto_products_filter_form' => '--porto-product-filter-select-width-md: {{VALUE}}%;',
				),
				'default'   => '20',
				'condition' => array(
					'set_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'hr_space_md',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Horizontal Space (< 992px) (%)', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .porto_products_filter_form' => '--porto-product-filter-space-md: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'set_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'selector_height',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Height', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} select' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'selector_border',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Border Style', 'porto-functionality' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'selector_b_r',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Border Radius', 'porto-functionality' ),
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
					'{{WRAPPER}} select' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'selector_br_width',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Border Width', 'porto-functionality' ),
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
					'{{WRAPPER}} select' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'selector_br_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} select' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style_option',
			array(
				'label'     => __( 'Button Style', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_type' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submit_typography',
				'label'    => esc_html__( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .btn-submit',
			)
		);

		$this->add_control(
			'submit_width',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Button Width (%)', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto_products_filter_form' => '--porto-product-filter-submit-width: {{VALUE}}%;',
				),
				'default'   => '20',
				'condition' => array(
					'set_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'submit_width_md',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Button Width (< 992px) (%)', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto_products_filter_form' => '--porto-product-filter-submit-width-md: {{VALUE}}%;',
				),
				'default'   => '20',
				'condition' => array(
					'set_inline' => 'yes',
				),
			)
		);

		$this->add_control(
			'submit_height',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Height', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .btn-submit' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'submit_b_r',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Border Radius', 'porto-functionality' ),
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
					'{{WRAPPER}} .btn-submit' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
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
