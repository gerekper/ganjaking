<?php
/**
 * Porto Dynamic Post Author Field Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Plugin;
class Porto_El_Custom_Field_Post_User_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'porto-custom-field-post-user';
	}

	public function get_title() {
		return esc_html__( 'Post / Author', 'porto-functionality' );
	}

	public function get_group() {
		return Porto_El_Dynamic_Tags::PORTO_GROUP;
	}

	public function get_categories() {
		return array(
			Porto_El_Dynamic_Tags::TEXT_CATEGORY,
			Porto_El_Dynamic_Tags::NUMBER_CATEGORY,
			Porto_El_Dynamic_Tags::POST_META_CATEGORY,
			Porto_El_Dynamic_Tags::COLOR_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control(
			'dynamic_field_post_object',
			array(
				'label'   => esc_html__( 'Object Field', 'porto-functionality' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => 'post_title',
				'groups'  => Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_object_fields(),
			)
		);
		$this->add_control(
			'date_format',
			array(
				'label'       => esc_html__( 'Date Format', 'porto-functionality' ),
				'description' => esc_html__( 'j = 1-31, F = January-December, M = Jan-Dec, m = 01-12, n = 1-12', 'porto-functionality' ),
				'type'        => Elementor\Controls_Manager::TEXT,
				'condition'   => array(
					'dynamic_field_post_object' => 'post_date',
				),
			)
		);
	}

	public function render() {

		do_action( 'porto_dynamic_before_render' );
		$post_id     = get_the_ID();
		$atts        = $this->get_settings();
		$property    = $atts['dynamic_field_post_object'];
		$date_format = ! empty( $atts['date_format'] ) ? $atts['date_format'] : '';
		$ret         = (string) Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_field_prop( $property, $date_format );
		if ( 'post_content' === $property ) {
			if ( Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {

				global $post;
				$temp = $post;
				$post = '';
				$ret  = Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_id );
				$post = $temp;

			} else {
				$ret = apply_filters( 'the_content', $ret );
			}
		}
		$ret = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_field( $ret );
		echo porto_strip_script_tags( $ret );
		do_action( 'porto_dynamic_after_render' );
	}

}
