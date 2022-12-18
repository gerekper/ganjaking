<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Portfolios Slider Widget
 *
 * Porto Elementor widget to display portfolios slider.
 *
 * @since 1.7.2
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Recent_Portfolios_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_recent_portfolios';
	}

	public function get_title() {
		return __( 'Porto Portfolios Carousel', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'portfolio', 'article', 'slider', 'carousel', 'image' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {
		$slider_options = porto_vc_product_slider_fields();
		unset( $slider_options[8] );
		unset( $slider_options[9] );
		$slider_options = porto_update_vc_options_to_elementor( $slider_options );

		unset( $slider_options['dots_pos'], $slider_options['navigation']['default'] );

		$slider_options['nav_pos2']['condition']['navigation'] = 'yes';
		$slider_options['nav_type']['condition']['navigation'] = 'yes';

		$order_by_values  = array_slice( porto_vc_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );

		$this->start_controls_section(
			'section_recent_portfolios',
			array(
				'label' => __( 'Recent Portfolios Carousel', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'         => __( 'View Type', 'porto-functionality' ),
				'type'          => 'image_choose',
				'default'       => 'classic',
				'options'       => array(
					'classic'  => 'portfolio/archive_view_1.jpg',
					'default'  => 'portfolio/archive_view_1.jpg',
					'full'     => 'portfolio/archive_view_2.jpg',
					'outimage' => 'portfolio/archive_view_3.jpg',
				),
				'display_label' => true,
			)
		);

		$this->add_control(
			'info_view',
			array(
				'label'         => __( 'Info View Type', 'porto-functionality' ),
				'type'          => 'image_choose',
				'default'       => '',
				'display_label' => true,
				'options'       => array(
					''                 => 'portfolio/info_view.jpg',
					'left-info'        => 'portfolio/info_view_1.jpg',
					'centered-info'    => 'portfolio/info_view_2.jpg',
					'bottom-info'      => 'portfolio/info_view_3.jpg',
					'bottom-info-dark' => 'portfolio/info_view_4.jpg',
					'hide-info-hover'  => 'portfolio/info_view_5.jpg',
				),
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

		$this->add_control(
			'thumb_bg',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Image Overlay Background', 'porto-functionality' ),
				'description' => __( 'Controls the overlay background of featured image.', 'porto' ),
				'default'     => '',
				'options'     => array(
					''                => __( 'Standard', 'porto-functionality' ),
					'darken'          => __( 'Darken', 'porto-functionality' ),
					'lighten'         => __( 'Lighten', 'porto-functionality' ),
					'hide-wrapper-bg' => __( 'Transparent', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'thumb_image',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Hover Image Effect', 'porto-functionality' ),
				'description' => __( 'Controls the hover effect of image.', 'porto' ),
				'default'     => '',
				'options'     => array(
					''        => __( 'Standard', 'porto-functionality' ),
					'zoom'    => __( 'Zoom', 'porto-functionality' ),
					'no-zoom' => __( 'No Zoom', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'ajax_load',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Enable Ajax Load', 'porto-functionality' ),
				'description' => __( 'If enabled, portfolio content should be displayed at the top of portfolios or on modal when you click portfolio item in the list.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'ajax_modal',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Ajax Load on Modal', 'porto-functionality' ),
				'condition' => array(
					'ajax_load' => 'yes',
				),
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Portfolio Count', 'porto-functionality' ),
				'default' => 8,
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'options'     => 'portfolio_cat',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'post_in',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Portfolio IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of portfolio ids', 'porto-functionality' ),
				'options'     => 'portfolio',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => array_merge(
					array(
						'' => __( 'Default', 'porto-functionality' ),
					),
					array_flip( $order_by_values )
				),
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Select how to sort retrieved portfolios. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order way', 'porto-functionality' ),
				'options'     => array_merge(
					array(
						'' => __( 'Default', 'porto-functionality' ),
					),
					array_flip( $order_way_values )
				),
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				'separator'   => 'after',
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'               => Controls_Manager::NUMBER,
				'label'              => __( 'Column Spacing (px)', 'porto-functionality' ),
				'render_type'        => 'template',
				'frontend_available' => true,
				'selectors'          => array(
					'.elementor-element-{{ID}} .porto-recent-portfolios' => '--porto-el-spacing: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Large Desktop', 'porto-functionality' ),
				'default' => '',
			)
		);

		$this->add_control(
			'items_desktop',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Desktop', 'porto-functionality' ),
				'default' => 4,
			)
		);

		$this->add_control(
			'items_tablets',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Tablets', 'porto-functionality' ),
				'default' => 3,
			)
		);

		$this->add_control(
			'items_mobile',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Mobile', 'porto-functionality' ),
				'default' => 2,
			)
		);

		$this->add_control(
			'items_row',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items Row', 'porto-functionality' ),
				'default' => 1,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_portfolios_slider_options',
			array(
				'label' => __( 'Slider Options', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'slider_config',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Change Slider Options', 'porto-functionality' ),
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			$opt['condition']['slider_config'] = 'yes';
			if( ! empty( $opt['responsive'] ) ) {
				$this->add_responsive_control( $key, $opt );
			} else {
				$this->add_control( $key, $opt );
			}
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_recent_portfolios_title_style',
			array(
				'label' => esc_html__( 'Title', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .thumb-info-title',
			)
		);
		$this->add_control(
			'title_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .thumb-info-title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'title_bgc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .thumb-info-title' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'title_pd',
			array(
				'label'     => __( 'Padding', 'porto-functionality' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .thumb-info-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( isset( $atts['navigation'] ) && 'yes' == $atts['navigation'] ) {
			$atts['show_nav'] = $atts['navigation'];
		} else {
			$atts['show_nav'] = false;
		}
		if ( isset( $atts['pagination'] ) && 'yes' == $atts['pagination'] ) {
			$atts['show_dots'] = $atts['pagination'];
		} else {
			$atts['show_dots'] = false;
		}

		if ( $template = porto_shortcode_template( 'porto_recent_portfolios' ) ) {
			if ( ! empty( $atts['cats'] ) && is_array( $atts['cats'] ) ) {
				$atts['cats'] = implode( ',', $atts['cats'] );
			}
			if ( ! empty( $atts['post_in'] ) && is_array( $atts['post_in'] ) ) {
				$atts['post_in'] = implode( ',', $atts['post_in'] );
			}
			include $template;
		}
	}
}
