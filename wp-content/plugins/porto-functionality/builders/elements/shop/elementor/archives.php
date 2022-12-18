<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Posts Grid Widget
 *
 * Porto Elementor widget to display posts or terms built by mini type builder.
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Porto_Elementor_SB_Archives_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_archives';
	}

	public function get_title() {
		return __( 'Type Builder Archives', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'posts', 'products', 'shop', 'type', 'card', 'archives', 'builder', 'custom' );
	}

	public function get_icon() {
		return 'eicon-posts-group';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/porto-post-grid-widget/';
	}

	public function get_script_depends() {
		if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
			if ( ! wp_style_is( 'jquery-hover3d', 'registered' ) ) {
				wp_register_script( 'jquery-hover3d', PORTO_SHORTCODES_URL . 'assets/js/jquery.hover3d.min.js', array(), PORTO_SHORTCODES_VERSION, true );
			}
			if ( ! wp_style_is( 'jquery-hoverdir', 'registered' ) ) {
				wp_register_script( 'jquery-hoverdir', PORTO_SHORTCODES_URL . 'assets/js/jquery.hoverdir.min.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );
			}
			return array( 'porto-elementor-widgets-js', 'isotope', 'jquery-hover3d', 'jquery-hoverdir' );
		} else {
			return array();
		}
	}

	protected function register_controls() {
		$order_by_values  = array_flip( porto_vc_woo_order_by() );
		$order_way_values = array_flip( porto_vc_woo_order_way() );

		$order_by_values['']  = __( 'Default', 'porto-functionality' );
		$order_way_values[''] = __( 'Default', 'porto-functionality' );

		$left  = is_rtl() ? 'right' : 'left';
		$right = 'left' === $left ? 'right' : 'left';

		$this->start_controls_section(
			'section_selector',
			array(
				'label' => __( 'Posts Selector', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'builder_id',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Post Layout', 'porto-functionality' ),
				/* translators: starting and end A tags which redirects to edit page */
				'description' => sprintf( __( 'Please select a saved Post Layout template which was built using post type builder. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
				'options'     => 'porto_builder_type',
				'label_block' => true,
			)
		);

		$this->add_control(
			'list_builder_id',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Post Layout for List View', 'porto-functionality' ),
				/* translators: starting and end A tags which redirects to edit page */
				'description' => sprintf( __( 'Please select a saved Post Layout template which will be used in the list view when using Grid / List Toggle element. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
				'options'     => 'porto_builder_type',
				'label_block' => true,
			)
		);

		$this->add_control(
			'count',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Count (per page)', 'porto-functionality' ),
				/* translators: starting and end A tags which redirects to theme options page */
				'description' => sprintf( __( 'Please leave empty if you want to use default value which is set using WooCommerce -> Product Archives -> Products per Page in %1$sTheme Options%2$s.', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'themes.php?page=porto_settings' ) ) . '">', '</a>' ),
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
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => $order_by_values,
				'description' => __( 'Price, Popularity and Rating values only work for product post type.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order', 'porto-functionality' ),
				'options'     => $order_way_values,
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Posts Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'               => Controls_Manager::SLIDER,
				'label'              => __( 'Column Spacing (px)', 'porto-functionality' ),
				'description'        => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
				'range'              => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'render_type'        => 'template',
				'frontend_available' => true,
				'selectors'          => array(
					'.elementor-element-{{ID}} .porto-posts-grid' => '--porto-el-spacing: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Columns', 'porto-functionality' ),
				'default' => '4',
				'options' => porto_sh_commons( 'products_columns' ),
			)
		);

		$this->add_control(
			'columns_tablet',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Columns on tablet ( <= 991px )', 'porto-functionality' ),
				'default' => '',
				'options' => array(
					''  => __( 'Default', 'porto-functionality' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);

		$this->add_control(
			'columns_mobile',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
				'default' => '',
				'options' => array(
					''  => __( 'Default', 'porto-functionality' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			)
		);

		$this->add_control(
			'list_col',
			array(
				'label'       => esc_html__( 'Columns on List View', 'porto-functionality' ),
				'description' => esc_html__( 'Select number of columns to display on desktop( >= 992px ).', 'porto-functionality' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''  => 1,
					'2' => 2,
					'3' => 3,
				),
				'condition'   => array(
					'list_builder_id!' => '',
				),
			)
		);

		$this->add_control(
			'pagination_style',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Pagination Type', 'porto-functionality' ),
				'options' => array(
					''          => __( 'Default', 'porto-functionality' ),
					'ajax'      => __( 'Ajax Pagination', 'porto-functionality' ),
					'infinite'  => __( 'Infinite Scroll', 'porto-functionality' ),
					'load_more' => __( 'Load more', 'porto-functionality' ),
					'none'      => __( 'None', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'category_filter',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Show Category filter', 'porto-functionality' ),
				'description' => esc_html__( 'Defines whether to show or hide category filters above products.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Image Size', 'porto-functionality' ),
				'options' => array_flip( porto_sh_commons( 'image_sizes' ) ),
				'default' => '',
			)
		);

		$this->add_control(
			'el_class',
			array(
				'label' => __( 'Custom Class', 'porto-functionality' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'post_advanced_section',
			array(
				'label' => esc_html__( 'Advanced', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'post_found_nothing',
				array(
					'type'        => Controls_Manager::WYSIWYG,
					'label'       => esc_html__( 'Nothing Found Message', 'porto-functionality' ),
					'description' => __( 'Text when no results are found.', 'porto-functionality' ),
				)
			);
		$this->end_controls_section();

		// pagination style
		$this->start_controls_section(
			'p_style',
			array(
				'label'     => esc_html__( 'Pagination Style', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_style' => array( '', 'ajax' ),
				),
			)
		);

		$this->add_responsive_control(
			'p_align',
			array(
				'label'       => esc_html__( 'Horizontal Align', 'porto-functionality' ),
				'type'        => Controls_Manager::CHOOSE,
				'description' => esc_html__( 'Control the horizontal align of pagination part.', 'porto-functionality' ),
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .pagination' => 'justify-content:{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'p_margin',
			array(
				'label'       => esc_html__( 'Margin', 'porto-functionality' ),
				'description' => esc_html__( 'Set custom margin of pagination part.', 'porto-functionality' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array(
					'px',
					'%',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .pagination-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// load more style
		$this->start_controls_section(
			'lm_style',
			array(
				'label'     => esc_html__( 'Load More Button Style', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_style' => 'load_more',
				),
			)
		);

		$this->add_control(
			'lm_width',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Width', 'porto-functionality' ),
				'default'   => '',
				'options'   => array(
					''     => '100%',
					'auto' => 'auto',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .load-more .next' => 'width: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'lm_typography',
				'label'    => esc_html__( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .load-more .next',
			)
		);

		$this->add_control(
			'lm_padding',
			array(
				'label'       => esc_html__( 'Padding', 'porto-functionality' ),
				'description' => esc_html__( 'Controls padding value of button.', 'porto-functionality' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array(
					'px',
					'%',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'lm_spacing',
			array(
				'label'       => esc_html__( 'Spacing (px)', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the spacing of load more button.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .pagination-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_btn_cat' );

		$this->start_controls_tab(
			'tab_btn_normal',
			array(
				'label' => esc_html__( 'Normal', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'lm_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lm_back_color',
			array(
				'label'       => esc_html__( 'Background Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the background color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lm_border_color',
			array(
				'label'       => esc_html__( 'Border Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the border color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_btn_hover',
			array(
				'label' => esc_html__( 'Hover', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'lm_color_hover',
			array(
				'label'       => esc_html__( 'Hover Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the hover color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lm_back_color_hover',
			array(
				'label'       => esc_html__( 'Hover Background Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the hover background color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lm_border_color_hover',
			array(
				'label'       => esc_html__( 'Hover Border Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the hover border color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .load-more .next:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// filters style
		$this->start_controls_section(
			'filter_style',
			array(
				'label'     => esc_html__( 'Filters Style', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'category_filter' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_typography',
				'label'    => esc_html__( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .sort-source a',
			)
		);

		$this->add_control(
			'filter_align',
			array(
				'label'       => esc_html__( 'Alignment', 'porto-functionality' ),
				'description' => esc_html__( 'Controls filters\'s alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs( 'filter_cats' );

		$this->start_controls_tab(
			'filter_normal',
			array(
				'label' => esc_html__( 'Normal', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'filter_normal_bgc',
			array(
				'label'       => esc_html__( 'Item Background Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the item\'s background color.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_normal_color',
			array(
				'label'       => esc_html__( 'Item Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the item\'s color.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_active',
			array(
				'label' => esc_html__( 'Hover/Active', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'filter_active_bgc',
			array(
				'label'       => esc_html__( 'Item Active Background Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the active and hover color of the filters.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source li.active > a, .elementor-element-{{ID}} .sort-source a:hover, .elementor-element-{{ID}} .sort-source a:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_active_color',
			array(
				'label'       => esc_html__( 'Item Active Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the active and hover color of the filters.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source li.active > a, .elementor-element-{{ID}} .sort-source a:hover, .elementor-element-{{ID}} .sort-source a:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'filter_between_spacing',
			array(
				'label'       => esc_html__( 'Between Spacing (px)', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the spacing between filters.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source li:not(:last-child)' => "margin-{$right}: {{SIZE}}{{UNIT}};",
				),
			)
		);

		$this->add_control(
			'filter_spacing',
			array(
				'label'       => esc_html__( 'Bottom Spacing (px)', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the spacing of the filters.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'post_advanced_style',
			array(
				'label'     => esc_html__( 'Found Nothing', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'post_found_nothing!' => '',
				),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'nothing_tg',
					'label'    => esc_html__( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .nothing-found-message',
				)
			);

			$this->add_control(
				'nothing_clr',
				array(
					'label'     => esc_html__( 'Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .nothing-found-message' => 'color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( class_exists( 'Woocommerce' ) && ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) ) {
			if ( empty( $atts['spacing'] ) ) {
				$atts['spacing'] = '';
			} else {
				$atts['spacing'] = $atts['spacing']['size'];
			}
			if ( is_array( $atts['count'] ) ) {
				if ( isset( $atts['count']['size'] ) ) {
					$atts['count'] = $atts['count']['size'];
				} else {
					$atts['count'] = '';
				}
			}

			if ( is_singular( PortoBuilders::BUILDER_SLUG ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_REQUEST['editor_post_id'] ) ) ) {
				$atts['post_type'] = 'product';
				if ( empty( $atts['orderby'] ) ) {
					$atts['orderby'] = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
				}
				if ( empty( $atts['order'] ) ) {
					$atts['order'] = 'ASC';
				}
				if ( empty( $atts['count'] ) ) {
					$atts['count'] = apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page', 12 ) );
				}

				global $porto_settings;
				$porto_settings['shop_pg_type'] = isset( $atts['pagination_style'] ) ? $atts['pagination_style'] : '';

				if ( isset( $atts['pagination_style'] ) ) {
					if ( 'none' == $atts['pagination_style'] ) {
						$atts['pagination_style'] = '';
					} elseif ( ! $atts['pagination_style'] ) {
						$atts['pagination_style'] = '1';
					}
				}
				$atts['filter_cat_tax'] = 'product_cat';
			} else {
				$atts['shortcode_type'] = 'shop';
			}

			include $template;
		}
	}
}
