<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder - Archive Description Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_SB_Description_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_description';
	}

	public function get_title() {
		return __( 'Archive Description', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'description', 'shop', 'archive' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-speech';
	}

	public function get_script_depends() {
		return array();
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_description_layout',
			array(
				'label' => __( 'Archive Description', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'notice_skin',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'To change the Products Archive’s layout, go to Porto / Theme Options / WooCommerce / Product Archives.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'notice_wrong_data',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'The editor\'s preview might look different from the live site. Please check the frontend.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}}, {{WRAPPER}} p',
			)
		);

		$this->add_control(
			'desc_font_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		do_action( 'woocommerce_archive_description' );
	}
}
