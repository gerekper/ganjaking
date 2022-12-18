<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder - Products Result Count Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;

class Porto_Elementor_SB_Result_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_result';
	}

	public function get_title() {
		return __( 'Products Result Count', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'result', 'shop', 'woocommerce', 'count', 'products' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-note';
	}

	public function get_script_depends() {
		return array();
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_count_layout',
			array(
				'label' => __( 'Products Result Count', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tg',
				'label'    => esc_html__( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .woocommerce-result-count',
			)
		);

		$this->add_control(
			'clr',
			array(
				'label'     => esc_html__( 'Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .woocommerce-result-count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		include PORTO_BUILDERS_PATH . '/elements/shop/wpb/result.php';
	}
}
