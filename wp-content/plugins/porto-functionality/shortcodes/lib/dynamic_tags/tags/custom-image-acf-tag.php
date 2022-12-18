<?php
/**
 * Porto Dynamic Acf Image Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Image_Acf_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'porto-custom-image-acf';
	}

	public function get_title() {
		return esc_html__( 'ACF', 'porto-functionality' );
	}

	public function get_group() {
		return Porto_El_Dynamic_Tags::PORTO_GROUP;
	}

	public function get_categories() {
		return array(
			Porto_El_Dynamic_Tags::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'dynamic_field_source',
			array(
				'label'   => esc_html__( 'Source', 'porto-functionality' ),
				'type'    => Elementor\Controls_Manager::HIDDEN,
				'default' => 'acf',
			)
		);
		do_action( 'porto_dynamic_before_render' );

		//Add acf field
		do_action( 'porto_dynamic_el_extra_fields', $this, 'image', 'acf' );

		do_action( 'porto_dynamic_after_render' );
	}

	public function register_advanced_section() {
		$this->start_controls_section(
			'advanced',
			array(
				'label' => esc_html__( 'Advanced', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'fallback',
			array(
				'label' => esc_html__( 'Fallback', 'porto-functionality' ),
				'type'  => Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->end_controls_section();
	}

	public function is_settings_required() {
		return true;
	}

	public function get_value( array $options = array() ) {
		if ( is_404() ) {
			return;
		}
		do_action( 'porto_dynamic_before_render' );

		$image_id = '';
		$atts     = $this->get_settings();

		/**
		 * Filters the content for dynamic extra fields.
		 *
		 * @since 1.0
		 */
		$image_id = apply_filters( 'porto_dynamic_el_extra_fields_content', null, $atts, 'image' );

		do_action( 'porto_dynamic_after_render' );

		if ( ! $image_id ) {
			return $atts['fallback'];
		}

		return array(
			'id'  => $image_id,
			'url' => wp_get_attachment_image_src( $image_id, 'full' )[0],
		);
	}

}
