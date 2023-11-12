<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @var Vc_Ai_Modal_Controller $ai_modal_controller
 * @var string $ai_element_type
 * @var string $ai_element_id
 */
?>
<div class="vc_col-xs-12 wpb_el_type_textarea vc_wrapper-param-type-textarea vc_shortcode-param vc_column">
	<div class="wpb_element_label"><?php esc_html_e( 'Describe content', 'js_composer' ); ?></div>
	<div class="edit_form_line">
		<textarea name="prompt" class="wpb_vc_param_value wpb-textarea text textarea"></textarea>
	</div>
</div>
<div class="vc_col-sm-12 vc_column">
	<button class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw vc_ai-generate-button">
		<?php
		esc_html_e( 'Generate', 'js_composer' );
		?>
	</button>
</div>
<div class="vc_col-xs-12 wpb_el_type_textarea vc_wrapper-param-type-textarea vc_shortcode-param vc_column">
	<div class="wpb_element_label"><?php esc_html_e( 'Output', 'js_composer' ); ?></div>
	<div class="edit_form_line">
		<textarea name="text" class="wpb_vc_param_value wpb-textarea text textarea wpb_ai-generated-content" rows="10" disabled></textarea>
		<span class="vc_description vc_clearfix"><?php printf( esc_html__( 'WPBakery AI generated content will appear here.', 'js_composer' ) ); ?></span>
	</div>
</div>
<input type="hidden" name="wpb-ai-element-type" value="<?php echo empty( $ai_element_type ) ? 'textarea' : esc_attr( $ai_element_type ); ?>">
<input type="hidden" name="wpb-ai-element-id" value="<?php echo empty( $ai_element_id ) ? 'textarea' : esc_attr( $ai_element_id ); ?>">
