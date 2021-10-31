<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Language/Currency Switcher widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Switcher_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_switcher';
	}

	public function get_title() {
		return __( 'Switcher', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'language', 'switcher', 'currency' );
	}

	public function get_icon() {
		return 'porto-icon-us-dollar';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_switcher',
			array(
				'label' => __( 'Switcher', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Type', 'porto-functionality' ),
				'options' => array(
					'language-switcher' => __( 'Language Switcher', 'porto-functionality' ),
					'currency-switcher' => __( 'Currency Switcher', 'porto-functionality' ),
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'switcher_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Top Level Typograhy', 'porto-functionality' ),
				'selector' => '#header .elementor-element-{{ID}} .porto-view-switcher > li.menu-item > a',
			)
		);

		$this->add_control(
			'top_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Top Level Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .elementor-element-{{ID}} .porto-view-switcher > li.menu-item > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Top Level Hover Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .elementor-element-{{ID}} .porto-view-switcher > li.menu-item:hover > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'porto_header_elements' ) && ! empty( $settings['type'] ) ) {
			porto_header_elements( array( (object) array( $settings['type'] => '' ) ) );
		}
	}
}
