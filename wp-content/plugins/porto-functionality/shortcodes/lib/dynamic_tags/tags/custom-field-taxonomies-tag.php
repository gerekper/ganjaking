<?php
/**
 * Porto Dynamic Taxonomy Field Tags class
 *
 * @author     P-THEMES
 * @version    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_El_Custom_Field_Taxonomies_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'porto-custom-field-taxonomies';
	}

	public function get_title() {
		return esc_html__( 'Taxonomies', 'porto-functionality' );
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
			'dynamic_field_taxonomy',
			array(
				'label'   => esc_html__( 'Taxonomy Field', 'porto-functionality' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => '',
				'groups'  => $this->get_taxonomy_fields(),
			)
		);
	}

	public function get_taxonomy_fields() {

		do_action( 'porto_dynamic_before_render' );

		$result        = array();
		$option_fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_taxonomy();
		$result        = array(
			array(
				'label'   => esc_html__( 'Taxonomies', 'porto-functionality' ),
				'options' => $option_fields,
			),
		);

		do_action( 'porto_dynamic_after_render' );

		return $result;
	}

	public function render() {
		if ( is_404() ) {
			return;
		}
		do_action( 'porto_dynamic_before_render' );

		$post_id = get_the_ID();
		$atts    = $this->get_settings();
		$ret     = '';

		$tax = $atts['dynamic_field_taxonomy'];
		if ( $tax ) {
			$ret = get_the_term_list( $post_id, $tax, '', ', ', '' );
		}
		$ret = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_field( $ret );
		echo porto_strip_script_tags( $ret );

		do_action( 'porto_dynamic_after_render' );
	}
}
