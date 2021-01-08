<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder Sort By Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_SB_Sort_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_sort';
	}

	public function get_title() {
		return __( 'Sort By', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'sort by', 'shop', 'woocommerce' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-arrow-down';
	}

	public function get_script_depends() {
		return array();
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_sort_layout',
			array(
				'label' => __( 'Sort By Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'notice_skin',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'To change the Products Archive’s layout, go to Porto / Theme Options / WooCommerce / Product Archives.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'notice_wrong_data',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'The editor\'s preview might look different from the live site. Please check the frontend.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		include PORTO_BUILDERS_PATH . '/elements/shop/wpb/sort.php';
	}
}
