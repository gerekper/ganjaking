<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Mobile Menu Icon widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Menu_Icon_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_menu_icon';
	}

	public function get_title() {
		return __( 'Mobile Menu Icon', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'menu', 'icon', 'navigation', 'trigger' );
	}

	public function get_icon() {
		return 'porto-icon-bars';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_menu_icon',
			array(
				'label' => __( 'Mobile Menu Icon', 'porto-functionality' ),
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
					'#header .mobile-toggle' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .mobile-toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Icon Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .mobile-toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$custom_icon = 'fas fa-bars';
		if ( isset( $settings['icon_cl'] ) && ! empty( $settings['icon_cl']['value'] ) ) {
			if ( isset( $settings['icon_cl']['library'] ) && ! empty( $settings['icon_cl']['value']['id'] ) ) {
				$custom_icon = $settings['icon_cl']['value']['id'];
			} else {
				$custom_icon = $settings['icon_cl']['value'];
			}
		}
		echo apply_filters( 'porto_header_builder_mobile_toggle', '<a class="mobile-toggle' . ( empty( $settings['bg_color'] ) ? ' ps-0' : '' ) . '"><i class="' . esc_attr( $custom_icon ) . '"></i></a>' );
	}
}
