<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Portfolio Categories Widget
 *
 * Porto Elementor widget to display portfolio categories.
 *
 * @since 1.7.5
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Portfolios_Category_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_portfolios_category';
	}

	public function get_title() {
		return __( 'Porto Portfolio Categories', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'portfolio', 'posts', 'category', 'categories' );
	}

	public function get_icon() {
		return 'eicon-folder-o';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'isotope', 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );

		$this->start_controls_section(
			'section_portfolio_categories',
			array(
				'label' => __( 'Layout', 'porto-functionality' ),
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
			'category_layout',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Category Layout', 'porto-functionality' ),
				'default' => 'strip',
				'options' => array(
					'stripes'  => 'Strip',
					'parallax' => 'Parallax',
					'list'     => __( 'Simple List', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'info_view',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Info View Type', 'porto-functionality' ),
				'default'   => '',
				'options'   => array(
					''                 => __( 'Basic', 'porto-functionality' ),
					'bottom-info'      => __( 'Bottom Info', 'porto-functionality' ),
					'bottom-info-dark' => __( 'Bottom Info Dark', 'porto-functionality' ),
				),
				'condition' => array(
					'category_layout' => array( 'stripes', 'parallax' ),
				),
			)
		);

		$this->add_control(
			'thumb_image',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Hover Image Effect', 'porto-functionality' ),
				'options'   => array(
					'zoom'      => __( 'Zoom', 'porto-functionality' ),
					'slow-zoom' => __( 'Slow Zoom', 'porto-functionality' ),
					'no-zoom'   => __( 'No Zoom', 'porto-functionality' ),
				),
				'condition' => array(
					'category_layout' => 'stripes',
				),
			)
		);

		$this->add_control(
			'portfolios_counter',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Portfolios Counter', 'porto-functionality' ),
				'description' => __( 'Show the number of portfolios in the category.', 'porto-functionality' ),
				'default'     => 'show',
				'options'     => array(
					'show' => __( 'Show', 'porto-functionality' ),
					'hide' => __( 'Hide', 'porto-functionality' ),
				),
				'condition'   => array(
					'category_layout' => array( 'stripes', 'parallax' ),
				),
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Category Count', 'porto-functionality' ),
				'default' => 5,
				'min'     => 1,
				'max'     => 99,
			)
		);

		$this->add_control(
			'cat_in',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
				'options'     => 'portfolio_cat',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Order by', 'porto-functionality' ),
				'default' => 'name',
				'options' => array(
					'name'        => __( 'Title', 'porto-functionality' ),
					'term_id'     => __( 'ID', 'porto-functionality' ),
					'count'       => __( 'Portfolio Count', 'porto-functionality' ),
					'none'        => __( 'None', 'porto-functionality' ),
					'parent'      => __( 'Parent', 'porto-functionality' ),
					'description' => __( 'Description', 'porto-functionality' ),
					'term_group'  => __( 'Term Group', 'porto-functionality' ),
				),
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
			'section_portfolio_title_style',
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
				'selector' => '.elementor-element-{{ID}} .portfolio-item-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .portfolio-parallax h2',
			)
		);
		$this->add_control(
			'title_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .portfolio-item-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .portfolio-parallax h2' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'title_bgc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .portfolio-item-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .portfolio-parallax h2' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'title_mg',
			array(
				'label'      => __( 'Margin', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .portfolio-item-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .portfolio-parallax h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);
		$this->add_control(
			'title_pd',
			array(
				'label'      => __( 'Padding', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .portfolio-item-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .portfolio-parallax h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_portfolio_pc_style',
			array(
				'label'     => esc_html__( 'Portfolios Counter', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'category_layout'    => array( 'stripes', 'parallax' ),
					'portfolios_counter' => 'show',
				),
			)
		);
		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'pc_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .thumb-info-icons .thumb-info-icon, .elementor-element-{{ID}} .thumb-info-bottom-info .thumb-info-type',
			)
		);
		$this->add_control(
			'pc_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-icons .thumb-info-icon, .elementor-element-{{ID}} .thumb-info-bottom-info .thumb-info-type' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'pc_bgc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-icons .thumb-info-icon, .elementor-element-{{ID}} .thumb-info-bottom-info .thumb-info-type' => 'background-color: {{VALUE}} !important;',
				),
			)
		);
		$this->add_control(
			'pc_pd',
			array(
				'label'      => __( 'Padding', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .thumb-info-icons .thumb-info-icon, .elementor-element-{{ID}} .thumb-info-bottom-info .thumb-info-type' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);
		$this->add_control(
			'pc_br',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Border Radius (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-icons .thumb-info-icon, .elementor-element-{{ID}} .thumb-info-bottom-info .thumb-info-type' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		if ( $template = porto_shortcode_template( 'porto_portfolios_category' ) ) {
			$atts = $this->get_settings_for_display();
			if ( ! empty( $atts['cat_in'] ) && is_array( $atts['cat_in'] ) ) {
				$atts['cat_in'] = implode( ',', $atts['cat_in'] );
			}
			include $template;
		}
	}
}
