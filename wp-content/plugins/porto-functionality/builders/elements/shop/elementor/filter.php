<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder - Grid / List Toggle Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_SB_Filter_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_filter';
	}

	public function get_title() {
		return __( 'Filter Toggle', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'filter', 'shop', 'toggle', 'widget', 'sidebar' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-eye';
	}

	public function get_script_depends() {
		return array();
	}

	public function get_style_depends() {
		$depends = array();
		return $depends;
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_filter_layout',
			array(
				'label' => __( 'Filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'notice_skin',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'You can use this widget to show / hide the shop sidebar in "Horizontal Filter 1" and "Off Canvas" filter layout.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'notice_skin2',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'This widget displays sidebar widgets in "Woo Category Filter" sidebar when using "Horizontal Filter 2" filter layout.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'notice_skin1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				/* translators: starting and ending A tags */
				'raw'             => sprintf( esc_html__( 'You can set these layouts in %1$sTheme Options -> WooCommerce -> Product Archives -> Filter Layout%2$s.', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'themes.php?page=porto_settings' ) ) . '" target="_blank">', '</a>' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info fw-bold',
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

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		include PORTO_BUILDERS_PATH . '/elements/shop/wpb/filter.php';
	}
}
