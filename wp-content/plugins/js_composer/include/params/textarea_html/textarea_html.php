<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $vc_html_editor_already_is_use;
$vc_html_editor_already_is_use = false;
/**
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.2
 */
function vc_textarea_html_form_field( $settings, $value ) {
	global $vc_html_editor_already_is_use;
	$output = '';
	if ( false !== $vc_html_editor_already_is_use ) {
		$output .= '<textarea name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textarea ' . esc_attr( $settings['param_name'] ) . ' textarea">' . $value . '</textarea>';
		$output .= '<div class="updated"><p>' . sprintf( esc_html__( 'Field type is changed from "textarea_html" to "textarea", because it is already used by %s field. Textarea_html field\'s type can be used only once per shortcode.', 'js_composer' ), $vc_html_editor_already_is_use ) . '</p></div>';
	} elseif ( function_exists( 'wp_editor' ) ) {
		$default_content = $value;
		// WP 3.3+
		ob_start();
		wp_editor( '', 'wpb_tinymce_' . esc_attr( $settings['param_name'] ), array(
			'editor_class' => 'wpb-textarea visual_composer_tinymce ' . esc_attr( $settings['param_name'] . ' ' . $settings['type'] ),
			'media_buttons' => true,
			'wpautop' => false,
		) );
		$output_value = ob_get_contents();
		ob_end_clean();
		$output .= $output_value . '<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '"  class="vc_textarea_html_content wpb_vc_param_value ' . esc_attr( $settings['param_name'] ) . '" value="' . htmlspecialchars( $default_content ) . '"/>';
		$vc_html_editor_already_is_use = $settings['param_name'];
	}

	return $output;
}
