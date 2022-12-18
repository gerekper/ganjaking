<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Wishlist Icon widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Divider_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_divider';
	}

	public function get_title() {
		return __( 'Vertical Divider', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'divider', 'vertical', 'separator' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-control-pause';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/porto-vertical-divider-element/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_hb_divider',
			array(
				'label' => __( 'Vertical Divider', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'width',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Width', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors' => array(
					'#header .elementor-element-{{ID}} .separator' => 'border-left-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'height',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Height', 'porto-functionality' ),
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
				'default'    => array(
					'unit' => 'em',
					'size' => 1.2,
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'#header .elementor-element-{{ID}} .separator' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'#header .elementor-element-{{ID}} .separator' => 'border-left-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'porto_header_elements' ) ) {
			porto_header_elements( array( (object) array( 'divider' => '' ) ) );
		}
	}
}
