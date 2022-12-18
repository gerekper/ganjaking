<?php
/**
 * Porto WooCommerce dynamic tags For Product Field
 *
 * @author     P-THEMES
 * @since      2.3.0
 */

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || die;

class Porto_Func_WooCommerce {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_filter( 'porto_dynamic_el_tags', array( $this, 'woo_add_tags' ) );
			add_filter( 'porto_dynamic_el_extra_fields_content', array( $this, 'woo_render' ), 10, 3 );
			add_action( 'porto_dynamic_el_extra_fields', array( $this, 'woo_add_control' ), 10, 3 );
		}
	}

	/**
	 * Render Woo Field
	 */
	public function woo_render( $result, $settings, $widget = 'field' ) {
		if ( 'woo' == $settings['dynamic_field_source'] ) {
			$option = 'dynamic_woo_' . $widget;
			$key    = isset( $settings[ $option ] ) ? $settings[ $option ] : false;

			$result = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( 'woocommerce', $key, $widget );
		}
		return $result;
	}

	/**
	 * Add Dynamic Woo Tags
	 */
	public function woo_add_tags( $tags ) {
		if ( ! porto_is_elementor_preview() || ( PortoBuilders::BUILDER_SLUG == get_post_type() && 'product' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) ) {
			array_push( $tags, 'Porto_El_Custom_Field_Woo_Tag' );
		}
		return $tags;
	}
	/**
	 * Add control for WOO object
	 */
	public function woo_add_control( $object, $widget = 'field', $plugin = 'woo' ) {
		if ( 'woo' == $plugin ) {
			$control_key = 'dynamic_woo_' . $widget;
			$object->add_control(
				$control_key,
				array(
					'label'   => esc_html__( 'Woo Field', 'porto-functionality' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'sku',
					'groups'  => Porto_Func_Dynamic_Tags_Content::get_instance()->get_woo_fields(),
				)
			);
		}
	}

}

new Porto_Func_WooCommerce;
