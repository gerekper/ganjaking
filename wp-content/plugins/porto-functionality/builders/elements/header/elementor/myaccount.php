<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder My Account Icon widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Myaccount_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_myaccount';
	}

	public function get_title() {
		return __( 'My Account', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'account', 'my account', 'icon' );
	}

	public function get_icon() {
		return 'porto-icon-user-2';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_myaccount',
			array(
				'label' => __( 'My Account Icon', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'icon_cl',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => '',
					'library' => '',
				),
			)
		);

		$this->add_control(
			'size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Font Size', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 72,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 5,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'#header .elementor-element-{{ID}} .my-account' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .elementor-element-{{ID}} .my-account' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( class_exists( 'Woocommerce' ) ) {
			$icon_cl = 'porto-icon-user-2';
			if ( isset( $settings['icon_cl'] ) && ! empty( $settings['icon_cl']['value'] ) ) {
				if ( isset( $settings['icon_cl']['library'] ) && ! empty( $settings['icon_cl']['value']['id'] ) ) {
					$icon_cl = $settings['icon_cl']['value']['id'];
				} else {
					$icon_cl = $settings['icon_cl']['value'];
				}
			}
			echo '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '"' . ' title="' . esc_attr__( 'My Account', 'porto' ) . '" class="my-account"><i class="' . esc_attr( $icon_cl ) . '"></i></a>';
		}
	}
}
