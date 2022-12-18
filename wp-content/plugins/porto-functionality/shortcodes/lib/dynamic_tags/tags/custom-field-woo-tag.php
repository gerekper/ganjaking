<?php
/**
 * Porto Dynamic Woocommerce Field Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Field_Woo_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'porto-custom-field-woo';
	}

	public function get_title() {
		return esc_html__( 'WooCommerce', 'porto-functionality' );
	}

	public function get_group() {
		return Porto_El_Dynamic_Tags::PORTO_GROUP;
	}

	public function get_categories() {
		return array(
			Porto_El_Dynamic_Tags::TEXT_CATEGORY,
			Porto_El_Dynamic_Tags::NUMBER_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'dynamic_field_source',
			array(
				'label'   => esc_html__( 'Source', 'porto-functionality' ),
				'type'    => Elementor\Controls_Manager::HIDDEN,
				'default' => 'woo',
			)
		);

		/**
		 * Fires before set current post type.
		 */
		do_action( 'porto_dynamic_before_render' );

		//Add WooCommerce field
		do_action( 'porto_dynamic_el_extra_fields', $this, 'field', 'woo' );

		/**
		 * Fires after set current post type.
		 */
		do_action( 'porto_dynamic_after_render' );

	}

	public function is_settings_required() {
		return true;
	}

	public function render() {
		if ( is_404() ) {
			return;
		}

		/**
		 * Fires before set current post type.
		 */
		do_action( 'porto_dynamic_before_render' );

		$post_id = get_the_ID();
		$atts    = $this->get_settings();
		$ret     = '';

		/**
		 * Filters the content for dynamic extra fields.
		 */
		$ret = apply_filters( 'porto_dynamic_el_extra_fields_content', null, $atts, 'field' );

		echo porto_strip_script_tags( $ret );

		/**
		 * Fires after set current post type.
		 */
		do_action( 'porto_dynamic_after_render' );
	}
}
