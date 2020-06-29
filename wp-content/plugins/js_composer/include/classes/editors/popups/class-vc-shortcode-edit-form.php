<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder main class.
 *
 * @package WPBakeryPageBuilder
 * @since   4.2
 */

/**
 * Edit form for shortcodes with ability to manage shortcode attributes in more convenient way.
 *
 * @since   4.2
 */
class Vc_Shortcode_Edit_Form {
	protected $initialized;

	/**
	 *
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;

		add_action( 'wp_ajax_vc_edit_form', array(
			$this,
			'renderFields',
		) );

		add_filter( 'vc_single_param_edit', array(
			$this,
			'changeEditFormFieldParams',
		) );
		add_filter( 'vc_edit_form_class', array(
			$this,
			'changeEditFormParams',
		) );
	}

	/**
	 *
	 */
	public function render() {
		vc_include_template( 'editors/popups/vc_ui-panel-edit-element.tpl.php', array(
			'box' => $this,
		) );
	}

	/**
	 * Build edit form fields.
	 *
	 * @since 4.4
	 */
	public function renderFields() {
		$tag = vc_post_param( 'tag' );
		vc_user_access()->checkAdminNonce()->validateDie( esc_html__( 'Access denied', 'js_composer' ) )->wpAny( array(
			'edit_post',
			(int) vc_request_param( 'post_id' ),
		) )->validateDie( esc_html__( 'Access denied', 'js_composer' ) )->check( 'vc_user_access_check_shortcode_edit', $tag )->validateDie( esc_html__( 'Access denied', 'js_composer' ) );

		$params = (array) stripslashes_deep( vc_post_param( 'params' ) );
		$params = array_map( 'vc_htmlspecialchars_decode_deep', $params );

		require_once vc_path_dir( 'EDITORS_DIR', 'class-vc-edit-form-fields.php' );
		$fields = new Vc_Edit_Form_Fields( $tag, $params );
		$output = $fields->render();
		// @codingStandardsIgnoreLine
		wp_die( $output );
	}

	/**
	 * @param $param
	 *
	 * @return mixed
	 */
	public function changeEditFormFieldParams( $param ) {
		$css = $param['vc_single_param_edit_holder_class'];
		if ( isset( $param['edit_field_class'] ) ) {
			$new_css = $param['edit_field_class'];
		} else {
			$new_css = 'vc_col-xs-12';
		}
		array_unshift( $css, $new_css );
		$param['vc_single_param_edit_holder_class'] = $css;

		return $param;
	}

	/**
	 * @param $css_classes
	 *
	 * @return mixed
	 */
	public function changeEditFormParams( $css_classes ) {
		$css = '';
		array_unshift( $css_classes, $css );

		return $css_classes;
	}
}
