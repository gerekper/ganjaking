<?php
/**
 * Porto Dynamic Term Meta Image Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Image_Term_Meta_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'porto-custom-image-term-meta';
	}

	public function get_title() {
		return esc_html__( 'Term Meta', 'porto-functionality' );
	}

	public function get_group() {
		return Porto_El_Dynamic_Tags::PORTO_GROUP;
	}

	public function get_categories() {
		return array(
			Porto_El_Dynamic_Tags::IMAGE_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'dynamic_field_source',
			array(
				'label'   => esc_html__( 'Source', 'porto-functionality' ),
				'type'    => Elementor\Controls_Manager::HIDDEN,
				'default' => 'term-meta',
			)
		);

		/**
		 * Fires before set current post type.
		 */
		do_action( 'porto_dynamic_before_render' );

		// Add acf field
		do_action( 'porto_dynamic_el_extra_fields', $this, 'image', 'term-meta' );

		/**
		 * Fires after set current post type.
		 */
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

	public function get_value( array $options = array() ) {
		if ( is_404() ) {
			return;
		}

		/**
		 * Fires before set current post type.
		 */
		do_action( 'porto_dynamic_before_render' );

		$image_url = '';
		$atts      = $this->get_settings();

		/**
		 * Filters the content for dynamic extra fields.
		 */
		$image_url = apply_filters( 'porto_dynamic_el_extra_fields_content', null, $atts, 'image' );

		/**
		 * Fires after set current post type.
		 */
		do_action( 'porto_dynamic_after_render' );
		if ( ! $image_url ) {
			return $atts['fallback'];
		}

		return array(
			'id'  => '',
			'url' => $image_url,
		);
	}
}
