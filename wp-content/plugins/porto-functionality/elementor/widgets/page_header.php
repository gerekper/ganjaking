<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Page Header Widget
 *
 * Porto Elementor widget to display page header section which contains breadcrumbs
 *
 * @since 2.2.0
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
		return array( 'breadcrumbs', 'page title', 'page subtitle' );
	}

	public function get_icon() {
		return 'eicon-product-breadcrumbs';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_page_header',
			array(
				'label' => __( 'Page Header', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'description_theme_option',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( esc_html__( 'Please see %1$sTheme Options -> Breadcrumbs%2$s.', 'porto-functionality' ), '<b>', '</b>' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'description_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( esc_html__( 'If the type is different with theme option, it doesn\'t work well.', 'porto-functionality' ), '<b>', '</b>' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
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
			'hide_page_title',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide Page Title', 'porto-functionality' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'page_title_heading',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Page Title', 'porto-functionality' ),
				'condition' => array(
					'hide_page_title' => '',
				),
			)
		);

		$this->add_control(
			'page_title',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Page Title', 'porto-functionality' ),
				'description' => __( 'Please leave this field blank to display default page title.', 'porto-functionality' ),
				'condition'   => array(
					'hide_page_title' => '',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'pt_font',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Title Typography', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .page-title',
				'condition' => array(
					'hide_page_title' => '',
				),
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
				'condition' => array(
					'hide_page_title' => '',
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
				'condition'  => array(
					'hide_page_title' => '',
				),
			)
		);

		$this->add_control(
			'page_subtitle_heading',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Page Sub Title', 'porto-functionality' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'pst_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Subtitle Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .page-sub-title',
			)
		);

		$this->add_control(
			'page_sub_title',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Page Sub Title', 'porto-functionality' ),
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

		$this->add_control(
			'breadcrumb_path',
			array(
				'label'     => esc_html__( 'Breadcrumb Path', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_breadcrumb',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Hide Breadcrumbs', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'bc_font',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Breadcrumb Typography', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .breadcrumb',
				'condition' => array(
					'hide_breadcrumb' => '',
				),
			)
		);

		$this->add_control(
			'delimiter_font_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Delimiter Font Size', 'porto-functionality' ),
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
					'{{WRAPPER}} .page-top ul.breadcrumb > li i.delimiter' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'hide_breadcrumb' => '',
				),
			)
		);

		$this->add_control(
			'bc_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Breadcrumbs Text Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .breadcrumbs-wrap .breadcrumb' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'hide_breadcrumb' => '',
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
				'condition' => array(
					'hide_breadcrumb' => '',
				),
			)
		);

		$this->add_control(
			'bc_margin',
			array(
				'label'       => esc_html__( 'Margin', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the margin value of breadcrumb path.', 'porto-functionality' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array(
					'px',
					'em',
				),
				'condition'   => array(
					'hide_breadcrumb' => '',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .breadcrumbs-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_page_header' ) ) {
			$atts['page_builder'] = 'elementor';
			include $template;
		}
	}
}
