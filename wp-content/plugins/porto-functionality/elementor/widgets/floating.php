<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Svg Floating Widget
 *
 * Porto Elementor widget to display floating widget.
 *
 * @since 6.1.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Floating_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_svg_floating';
	}

	public function get_title() {
		return __( 'Porto Svg Float', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'float', 'svg', 'float svg', 'svg floating' );
	}

	public function get_icon() {
		return 'eicon-divider-shape';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_floating',
			array(
				'label' => __( 'General', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'float_svg',
			array(
				'label' => __( 'Floating SVG', 'porto-functionality' ),
				'type' => Controls_Manager::CODE,
				'description' => __( 'Please writer your svg code.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'float_path',
			array(
				'label' => __( 'Path', 'porto-functionality' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( '#path1, #path2', 'porto-functionality' ),
				'description' => __( 'Please write floating path id using comma.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'float_duration',
			array(
				'label' => __( 'Floating Duration', 'porto-functionality' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'default' => 10000,
			)
		);

		$this->add_control(
			'float_easing',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Easing Method', 'porto-functionality' ),
				'options' => array_flip( porto_sh_commons( 'easing_methods' ) ),
				'default' => 'easingQuadraticInOut',
			)
		);

		$this->add_control(
			'float_repeat',
			array(
				'label' => __( 'Floating Repeat', 'porto-functionality' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'default' => 20,
			)
		);

		$this->add_control(
			'float_repeat_delay',
			array(
				'label' => __( 'Repeat Delay', 'porto-functionality' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'default' => 1000,
			)
		);

		$this->add_control(
			'float_yoyo',
			array(
				'label' => __( 'yoyo', 'porto-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		if ( $template = porto_shortcode_template( 'porto_svg_floating' ) ) {
			include $template;
		}
	}

}
