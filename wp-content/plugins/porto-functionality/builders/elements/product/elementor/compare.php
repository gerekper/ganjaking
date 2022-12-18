<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Compare Widget
 *
 * @since 2.6.0
 */
use Elementor\Controls_Manager;
class Porto_Elementor_CP_Compare_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'porto_cp_compare';
	}

	public function get_title() {
		return __( 'Product Compare', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'yith', 'product', 'single', 'compare' );
	}

	public function get_icon() {
		return 'porto-icon-compare';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_compare( $settings );
		}
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_cp_compare',
			array(
				'label' => __( 'Product Compare', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'compare_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .compare',
				)
			);
			$this->add_control(
				'pd',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .compare' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_button' );
				$this->start_controls_tab(
					'tab_bt_normal',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'bt_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .compare' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'bt_bd_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Border Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .compare' => 'border-color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'bt_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .compare' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();
				$this->start_controls_tab(
					'tab_bt_hover',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'bt_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .compare:hover' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'bt_bd_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Border Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .compare:hover' => 'border-color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'bt_bg_color_hover',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .compare:hover' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
