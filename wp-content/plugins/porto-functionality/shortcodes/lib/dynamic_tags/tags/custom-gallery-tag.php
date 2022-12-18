<?php
/**
 * Porto Dynamic Meta Box Custom Gallery Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Gallery_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'porto-custom-gallery';
	}

	public function get_title() {
		return esc_html__( 'Meta Box', 'porto-functionality' );
	}

	public function get_group() {
		return Porto_El_Dynamic_Tags::PORTO_GROUP;
	}

	public function get_categories() {
		return array(
			Porto_El_Dynamic_Tags::GALLERY_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'dynamic_field_source',
			array(
				'label'   => esc_html__( 'Source', 'porto-functionality' ),
				'type'    => Elementor\Controls_Manager::HIDDEN,
				'default' => 'meta-box',
			)
		);

		/**
		 * Fires before set current post type.
		 */
		do_action( 'porto_dynamic_before_render' );

		/**
		 * Add metabox field
		 */
		do_action( 'porto_dynamic_el_extra_fields', $this, 'image', 'meta-box' );

		/**
		 * Fires after set current post type.
		 */
		do_action( 'porto_dynamic_after_render' );

	}

	public function is_settings_required() {
		return true;
	}

	public function get_value( array $options = array() ) {
		if ( is_404() ) {
			return;
		}

		/**
		 * Fires before set current post type.
		 */
		do_action( 'porto_dynamic_before_render' );

		/**
		 * Filters the content for dynamic extra fields.
		 */
		$image_id = apply_filters( 'porto_dynamic_el_extra_fields_content', null, $this->get_settings(), 'image' );

		/**
		 * Fires after set current post type.
		 */
		do_action( 'porto_dynamic_after_render' );

		if ( ! $image_id ) {
			return array();
		}

		return array_map(
			function( $item ) {
				return array( 'id' => $item );
			},
			$image_id
		);
	}

}
