<?php
/**
 * Porto Dynamic Post User Image Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Image_Post_User_Tag extends Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'porto-custom-image-post-user';
	}

	public function get_title() {
		return esc_html__( 'Posts / Users', 'porto-functionality' );
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
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => 'featured',
				'options' => Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_object_image(),
			)
		);

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
		do_action( 'porto_dynamic_before_render' );

		$atts = $this->get_settings();

		$img = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_image( $atts['dynamic_field_source'] );

		do_action( 'porto_dynamic_after_render' );

		if ( ! $img['id'] && ! $img['url'] ) {
			return $atts['fallback'];
		}

		return array(
			'id'  => $img['id'],
			'url' => $img['id'] ? wp_get_attachment_image_src( $img['id'], 'full' )[0] : $img['url'],
		);
	}
}
