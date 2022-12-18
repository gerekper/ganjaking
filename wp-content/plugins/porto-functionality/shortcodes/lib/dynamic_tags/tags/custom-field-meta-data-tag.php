<?php
/**
 * Porto Dynamic Meta Data Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Field_Meta_Data_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'porto-custom-field-meta-data';
	}

	public function get_title() {
		return esc_html__( 'Meta Data', 'porto-functionality' );
	}

	public function get_group() {
		return Porto_El_Dynamic_Tags::PORTO_GROUP;
	}

	public function get_categories() {
		return array(
			Porto_El_Dynamic_Tags::TEXT_CATEGORY,
			Porto_El_Dynamic_Tags::NUMBER_CATEGORY,
			Porto_El_Dynamic_Tags::URL_CATEGORY,
			Porto_El_Dynamic_Tags::POST_META_CATEGORY,
			Porto_El_Dynamic_Tags::COLOR_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control(
			'dynamic_field_custom_meta_key',
			array(
				'label'       => esc_html__( 'Custom meta key', 'porto-functionality' ),
				'type'        => Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);
	}


	public function render() {
		if ( is_404() ) {
			return;
		}
		do_action( 'porto_dynamic_before_render' );

		$post_id = get_the_ID();
		$atts    = $this->get_settings();
		$ret     = '';

		if ( ! empty( $atts['dynamic_field_custom_meta_key'] ) ) {
			$meta_key = $atts['dynamic_field_custom_meta_key'];
			$ret      = get_post_meta( $post_id, $meta_key, true );
		}

		echo porto_strip_script_tags( $ret );

		do_action( 'porto_dynamic_after_render' );
	}
}
