<?php
/**
 * Porto Elementor Single Post Format Widget
 *
 * @author     P-THEMES
 * @since      2.5.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

class Porto_Elementor_Single_Post_Format_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_single_post_format';
	}

	public function get_title() {
		return esc_html__( 'Post Format', 'porto-functionality' );
	}

	public function get_icon() {
		return 'fas fa-file-alt';
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'post', 'format', 'gallery', 'link', 'image', 'date', 'quote', 'video', 'audio', 'chat', 'status' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-builder-elements/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_single_post_format',
			array(
				'label' => __( 'Post Format', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$this->add_control(
			'description_format',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( __( 'This widget is only available for Single Post. For this, you should set post format on Post Settings.', 'porto-functionality' ), '<b>', '</b>' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'format_heading',
				'selector' => '.elementor-element-{{ID}} .gallery > i',
			)
		);

		$this->add_control(
			'format_padding',
			array(
				'label'      => __( 'Padding', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .format i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'format_radius',
			array(
				'label'      => __( 'Border Radius', 'porto-functionality' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'rem',
					'em',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .format' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'format_color',
			array(
				'label'     => __( 'Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .format i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'format_bg_color',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .format' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'format_shadow',
				'selector' => '.elementor-element-{{ID}} .format',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts['page_builder'] = 'elementor';
		$atts['elementor_id'] = 'elementor-element-' . $this->get_id();
		echo PortoBuildersSingle::get_instance()->shortcode_single_post_format( $atts );
	}
}
