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

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/';
	}

	protected function register_controls() {
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
				'label'    => __( 'Typography', 'porto-functionality' ),
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
		if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
			ob_start();
			/**
			 * Hook: woocommerce_archive_description.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
			$desc = ob_get_clean();
			if ( $desc ) {
				echo porto_strip_script_tags( $desc );
			} else {
				echo '<p>' . esc_html__( 'Category description', 'porto-functionality' ) . '</p>';
			}
		} else {
			/**
			 * Hook: woocommerce_archive_description.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
		}
	}
}
