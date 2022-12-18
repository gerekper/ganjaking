<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Posts Grid Widget
 *
 * Porto Elementor widget to display posts or terms with the type built by post type builder.
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Porto_Elementor_Posts_Grid_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_posts_grid';
	}

	public function get_title() {
		return __( 'Porto Posts Grid', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'post', 'product', 'shop', 'term', 'category', 'taxonomy', 'type', 'card', 'builder', 'custom' );
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
				wp_register_script( 'jquery-hover3d', PORTO_SHORTCODES_URL . 'assets/js/jquery.hover3d.min.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );
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
		$order_by_values  = array_slice( porto_vc_woo_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );
		$slider_options   = porto_update_vc_options_to_elementor( porto_vc_product_slider_fields() );

		$slider_options['nav_pos2']['condition']['navigation']       = 'yes';
		$slider_options['nav_type']['condition']['navigation']       = 'yes';
		$slider_options['autoplay_timeout']['condition']['autoplay'] = 'yes';

		$post_types          = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'objects',
			'and'
		);
		$disabled_post_types = array( 'attachment', 'porto_builder', 'page', 'e-landing-page' );
		foreach ( $disabled_post_types as $disabled ) {
			unset( $post_types[ $disabled ] );
		}
		foreach ( $post_types as $key => $p_type ) {
			$post_types[ $key ] = esc_html( $p_type->label );
		}
		$post_types = apply_filters( 'porto_posts_grid_post_types', $post_types );

		$taxes = get_taxonomies( array(), 'objects' );
		unset( $taxes['post_format'], $taxes['product_visibility'] );
		foreach ( $taxes as $tax_name => $tax ) {
			$taxes[ $tax_name ] = esc_html( $tax->label );
		}
		$taxes = apply_filters( 'porto_posts_grid_taxonomies', $taxes );
		$left  = is_rtl() ? 'right' : 'left';
		$right = 'left' === $left ? 'right' : 'left';

		global $porto_settings;
		$status_values = array(
			''          => __( 'All', 'porto-functionality' ),
			'featured'  => __( 'Featured', 'porto-functionality' ),
			'on_sale'   => __( 'On Sale', 'porto-functionality' ),
			'pre_order' => __( 'Pre-Order', 'porto-functionality' ),
			'viewed'    => __( 'Recently Viewed', 'porto-functionality' ),
		);
		if ( empty( $porto_settings['woo-pre-order'] ) ) {
			unset( $status_values['pre_order'] );
		}

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
			'source',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Content Source', 'porto-functionality' ),
				'options'     => array(
					''      => __( 'Posts', 'porto-functionality' ),
					'terms' => __( 'Terms', 'porto-functionality' ),
				),
				'description' => __( 'Please select the content type which you would like to show.', 'porto-functionality' ),
				'default'     => '',
			)
		);

		$this->add_control(
			'post_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Post Type', 'porto-functionality' ),
				'description' => __( 'Please select a post type of posts to display.', 'porto-functionality' ),
				'options'     => $post_types,
				'condition'   => array(
					'source' => '',
				),
			)
		);

		$this->add_control(
			'product_status',
			array(
				'label'     => __( 'Product Status', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => $status_values,
				'condition' => array(
					'source'    => '',
					'post_type' => 'product',
				),
			)
		);

		$this->add_control(
			'post_tax',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Taxonomies', 'porto-functionality' ),
				'description' => __( 'Please select a post taxonomy to pull posts from.', 'porto-functionality' ),
				'options'     => '%post_type%_alltax',
				'label_block' => true,
				'condition'   => array(
					'source' => '',
				),
			)
		);

		$this->add_control(
			'post_terms',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Terms', 'porto-functionality' ),
				'description' => __( 'Please select post terms to pull posts from.', 'porto-functionality' ),
				'options'     => '%post_tax%_allterm',
				'multiple'    => 'true',
				'label_block' => true,
				'condition'   => array(
					'source'    => '',
					'post_tax!' => '',
				),
			)
		);

		$this->add_control(
			'tax',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Taxonomy', 'porto-functionality' ),
				'description' => __( 'Please select a taxonomy to use.', 'porto-functionality' ),
				'options'     => $taxes,
				'condition'   => array(
					'source' => 'terms',
				),
				'default'     => '',
			)
		);

		$this->add_control(
			'terms',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Terms', 'porto-functionality' ),
				'description' => __( 'Please select terms to display.', 'porto-functionality' ),
				'options'     => '%tax%_allterm',
				'multiple'    => 'true',
				'label_block' => true,
				'condition'   => array(
					'source' => 'terms',
					'tax!'   => '',
				),
			)
		);

		$this->add_control(
			'count',
			array(
				'type'  => Controls_Manager::SLIDER,
				'label' => __( 'Count', 'porto-functionality' ),
				'range' => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide empty', 'porto-functionality' ),
				'condition' => array(
					'source' => 'terms',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => array_flip( $order_by_values ),
				'description' => __( 'Price, Popularity and Rating values only work for product post type.', 'porto-functionality' ),
				'condition'   => array(
					'source' => '',
				),
			)
		);

		$this->add_control(
			'orderby_term',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Order by', 'porto-functionality' ),
				'options'   => array(
					''            => __( 'Default', 'porto-functionality' ),
					'name'        => __( 'Title', 'porto-functionality' ),
					'term_id'     => __( 'ID', 'porto-functionality' ),
					'count'       => __( 'Post Count', 'porto-functionality' ),
					'none'        => __( 'None', 'porto-functionality' ),
					'parent'      => __( 'Parent', 'porto-functionality' ),
					'description' => __( 'Description', 'porto-functionality' ),
					'term_group'  => __( 'Term Group', 'porto-functionality' ),
				),
				'default'   => '',
				'condition' => array(
					'source' => 'terms',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order', 'porto-functionality' ),
				'options'     => array_flip( $order_way_values ),
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
			'view',
			array(
				'label'   => __( 'View', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''         => __( 'Grid', 'porto-functionality' ),
					'creative' => __( 'Grid - Creative', 'porto-functionality' ),
					'masonry'  => __( 'Masonry', 'porto-functionality' ),
					'slider'   => __( 'Slider', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'grid_layout',
			array(
				'label'     => __( 'Grid Layout', 'porto-functionality' ),
				'type'      => 'image_choose',
				'default'   => '1',
				'options'   => array_flip( porto_sh_commons( 'masonry_layouts' ) ),
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
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'default'   => '4',
				'options'   => porto_sh_commons( 'products_columns' ),
				'condition' => array(
					'view!' => 'creative',
				),
			)
		);

		$this->add_control(
			'columns_tablet',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns on tablet ( <= 991px )', 'porto-functionality' ),
				'default'   => '',
				'options'   => array(
					''  => __( 'Default', 'porto-functionality' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'condition' => array(
					'view!' => 'creative',
				),
			)
		);

		$this->add_control(
			'columns_mobile',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
				'default'   => '',
				'options'   => array(
					''  => __( 'Default', 'porto-functionality' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'condition' => array(
					'view!' => 'creative',
				),
			)
		);

		$this->add_control(
			'pagination_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Pagination Type', 'porto-functionality' ),
				'options'   => array(
					''          => __( 'No pagination', 'porto-functionality' ),
					'ajax'      => __( 'Ajax Pagination', 'porto-functionality' ),
					'infinite'  => __( 'Infinite Scroll', 'porto-functionality' ),
					'load_more' => __( 'Load more', 'porto-functionality' ),
				),
				'condition' => array(
					'source' => '',
				),
			)
		);

		$this->add_control(
			'category_filter',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Show Category filter', 'porto-functionality' ),
				'description' => __( 'Defines whether to show or hide category filters above posts.', 'porto-functionality' ),
				'condition'   => array(
					'source' => '',
				),
			)
		);

		$this->add_control(
			'filter_cat_tax',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Taxonomy', 'porto-functionality' ),
				'description' => __( 'Please select a post taxonomy to be used as category filter.', 'porto-functionality' ),
				'options'     => '%post_type%_alltax',
				'label_block' => true,
				'condition'   => array(
					'source'          => '',
					'category_filter' => 'yes',
				),
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
					'view!' => 'creative',
				),

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
			'section_slider_options',
			array(
				'label'     => __( 'Slider Options', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => 'slider',
				),
			)
		);

		$this->add_control(
			'stage_padding',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Stage Padding (px)', 'porto-functionality' ),
				'default' => '',
				'min'     => 0,
				'max'     => 100,
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			if ( ! empty( $opt['responsive'] ) ) {
				$this->add_responsive_control( $key, $opt );
			} else {
				$this->add_control( $key, $opt );
			}
		}

		$this->end_controls_section();

		// pagination style
		$this->start_controls_section(
			'p_style',
			array(
				'label'     => esc_html__( 'Pagination Style', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_style' => 'ajax',
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
				'label'       => esc_html__( 'Background Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the color of the filters.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .sort-source a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_normal_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the color of the filters.', 'porto-functionality' ),
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
				'label'       => esc_html__( 'Active Background Color', 'porto-functionality' ),
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
				'label'       => esc_html__( 'Active Color', 'porto-functionality' ),
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
					'.elementor-element-{{ID}} .sort-source li' => "margin-{$right}: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}};",
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
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) {
			if ( empty( $atts['spacing'] ) ) {
				$atts['spacing'] = '';
			}
			if ( is_array( $atts['count'] ) ) {
				if ( isset( $atts['count']['size'] ) ) {
					$atts['count'] = $atts['count']['size'];
				} else {
					$atts['count'] = '';
				}
			}
			include $template;
		}
	}
}
