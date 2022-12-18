<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Product Categories Widget
 *
 * Porto Elementor widget to display products.
 *
 * @since 1.5.2
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

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

	protected function register_controls() {
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );
		$slider_options   = porto_update_vc_options_to_elementor( porto_vc_product_slider_fields( 'products-slider', 'dots-style-1' ) );;

		$slider_options['nav_pos2']['condition']['navigation']        = 'yes';
		$slider_options['nav_type']['condition']['navigation']        = 'yes';
		$slider_options['autoplay_timeout']['condition']['autoplay']  = 'yes';
		
		$this->start_controls_section(
			'section_product_categories',
			array(
				'label' => __( 'Selector', 'porto-functionality' ),
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
				'label' => __( 'Layout', 'porto-functionality' ),
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
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Text Position', 'porto-functionality' ),
				'options'     => array(
					'middle-left'    => __( 'Inner Middle Left', 'porto-functionality' ),
					'middle-center'  => __( 'Inner Middle Center', 'porto-functionality' ),
					'middle-right'   => __( 'Inner Middle Right', 'porto-functionality' ),
					'bottom-left'    => __( 'Inner Bottom Left', 'porto-functionality' ),
					'bottom-center'  => __( 'Inner Bottom Center', 'porto-functionality' ),
					'bottom-right'   => __( 'Inner Bottom Right', 'porto-functionality' ),
					'outside-center' => __( 'Outside', 'porto-functionality' ),
				),
				'default'     => 'middle-center',
				'qa_selector' => 'li.product-category:first-child .thumb-info-title',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_categories_option',
			array(
				'label' => __( 'Option', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Text Color', 'porto-functionality' ),
				'options' => array(
					'dark'      => __( 'Dark', 'porto-functionality' ),
					'light'     => __( 'Light', 'porto-functionality' ),
					'primary'   => __( 'Primary', 'porto-functionality' ),
					'secondary' => __( 'Secondary', 'porto-functionality' ),
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
				'description' => __( 'If you check this option, a featured product in each category will be displayed under the product category. This option isn\'t available to "Grid-Creative" of "View".', 'porto-functionality' ),
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
			if( ! empty( $opt['responsive'] ) ) {
				$this->add_responsive_control( $key, $opt );
			} else {
				$this->add_control( $key, $opt );
			}
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_option',
			array(
				'label'       => __( 'Style', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => 'li.product-category:first-child',
			)
		);

			$this->add_control(
				'thumb_border_radius',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Image Border Radius', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'%'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
					),
					'condition'  => array(
						'media_type' => '',
					),
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info .thumb-info-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'thumb_min_height',
				array(
					'type'      => Controls_Manager::SLIDER,
					'label'     => __( 'Min Height of Text', 'porto-functionality' ),
					'range'     => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
					'condition' => array(
						'media_type' => 'none',
					),
					'selectors' => array(
						'{{WRAPPER}} .product-category .thumb-info' => 'min-height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_padding',
				array(
					'label'      => esc_html__( 'Icon Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'media_type' => array( 'icon' ),
					),
				)
			);

			$this->add_control(
				'icon_border_radius',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Border Radius', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'%'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
					),
					'condition'  => array(
						'media_type' => 'icon',
					),
					'selectors'  => array(
						'{{WRAPPER}} .cat-has-icon .thumb-info i' => 'border-radius: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs(
				'thumb_bg_tab',
				array(
					'condition' => array(
						'media_type' => array( 'none', 'icon' ),
					),
				)
			);

				$this->start_controls_tab(
					'normal',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);

					$this->add_control(
						'thumb_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} .thumb-info' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'icon_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Icon Background Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} .cat-has-icon .thumb-info i' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'media_type' => array( 'icon' ),
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'hover',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);

					$this->add_control(
						'thumb_bg_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} .thumb-info:hover' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'icon_bg_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Icon Background Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} .cat-has-icon .thumb-info:hover i' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'media_type' => array( 'icon' ),
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'text_style_option',
			array(
				'label'       => __( 'Text Style', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => 'li.product-category:first-child .thumb-info-title a',
			)
		);

			$this->add_control(
				'text_title',
				array(
					'type'  => Controls_Manager::HEADING,
					'label' => __( 'Typography', 'porto-functionality' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_typo',
					'label'    => __( 'Title Typography', 'porto-functionality' ),
					'selector' => '{{WRAPPER}} .thumb-info h3',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'category_typo',
					'label'     => __( 'Category Typography', 'porto-functionality' ),
					'selector'  => '{{WRAPPER}} .sub-categories li',
					'condition' => array(
						'show_sub_cats' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'product_count',
					'label'     => __( 'Product Count Typography', 'porto-functionality' ),
					'selector'  => '{{WRAPPER}} .thumb-info-type',
					'condition' => array(
						'hide_count' => '',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'icon_typo',
					'label'     => __( 'Icon Typography', 'porto-functionality' ),
					'selector'  => '{{WRAPPER}} .thumb-info i',
					'condition' => array(
						'media_type' => array( 'icon' ),
					),

				)
			);

			$this->add_control(
				'thumb_padding',
				array(
					'label'      => esc_html__( 'Text Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} .product-category:not(.cat-has-icon) .thumb-info .thumb-info-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .product-category.cat-has-icon .thumb-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'media_type' => array( 'none', 'icon' ),
					),
				)
			);

			$this->start_controls_tabs( 'text_color_tab' );

				$this->start_controls_tab(
					'text_normal',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);

					$this->add_control(
						'text_normal_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} .thumb-info-title' => 'color: {{VALUE}};',
								'{{WRAPPER}} .thumb-info i'     => 'color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'text_hover',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);

					$this->add_control(
						'text_hover_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'{{WRAPPER}} .thumb-info:hover .thumb-info-title' => 'color: {{VALUE}};',
								'{{WRAPPER}} .thumb-info:hover i' => 'color: {{VALUE}};',
								'{{WRAPPER}} .thumb-info .thumb-info-inner' => 'transition: none;',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		
		if ( $template = porto_shortcode_woo_template( 'porto_product_categories' ) ) {
			if ( ! empty( $atts['parent'] ) && is_array( $atts['parent'] ) ) {
				$atts['parent'] = implode( ',', $atts['parent'] );
			}
			if ( is_array( $atts['ids'] ) ) {
				if ( ! empty( $atts['ids'] ) ) {
					$atts['ids'] = implode( ',', $atts['ids'] );
				} else {
					$atts['ids'] = '';
				}
			}
			if ( ! empty( $atts['thumb_border_radius'] ) && ! empty( $atts['thumb_border_radius']['size'] ) ) {
				$atts['thumb_border_radius'] = $atts['thumb_border_radius']['size'] . $atts['thumb_border_radius']['unit'];
			}
			include $template;
		}
	}
}
