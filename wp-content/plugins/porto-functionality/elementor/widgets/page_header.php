<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Page Header Widget
 *
 * Porto Elementor widget to display page header section which contains breadcrumbs
 *
 * @since 6.2.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Page_Header_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_page_header';
	}

	public function get_title() {
		return __( 'Porto Page Header', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'breadcrumbs', 'page header' );
	}

	public function get_icon() {
		return 'eicon-product-breadcrumbs';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_page_header',
			array(
				'label' => __( 'Page Header', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'breadcrumbs_type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Breadcrumbs Type', 'porto-functionality' ),
				'options' => array(
					''  => __( 'Theme Options', 'porto-functionality' ),
					'1' => __( 'Type 1', 'porto-functionality' ),
					'2' => __( 'Type 2', 'porto-functionality' ),
					'3' => __( 'Type 3', 'porto-functionality' ),
					'4' => __( 'Type 4', 'porto-functionality' ),
					'5' => __( 'Type 5', 'porto-functionality' ),
					'6' => __( 'Type 6', 'porto-functionality' ),
					'7' => __( 'Type 7', 'porto-functionality' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'page_title',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Page Title', 'porto-functionality' ),
				'description' => __( 'Please leave this field blank to display default page title.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'page_sub_title',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Page Sub Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'hide_breadcrumb',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide Breadcrumbs', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'bc_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Breadcrumbs Text Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .breadcrumbs-wrap' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bc_link_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Breadcrumbs Link Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .breadcrumbs-wrap a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'pt_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Title Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .page-title',
			)
		);

		$this->add_control(
			'pt_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Page Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .page-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pt_mb',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Page Title Margin Bottom', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .page-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pst_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Page Sub Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .page-sub-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_page_header' ) ) {
			include $template;
		}
	}
}
