<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/** @var Vc_Backend_Editor|Vc_Frontend_Editor $editor */
?>

<input type="hidden" name="vc_post_custom_css" id="vc_post-custom-css" value="<?php echo esc_attr( $editor->post_custom_css ); ?>" autocomplete="off"/>
<input type="hidden" name="vc_post_custom_js_header" id="vc_post-custom-js-header" value="<?php echo esc_attr( $editor->post_custom_js_header ); ?>" autocomplete="off"/>
<input type="hidden" name="vc_post_custom_js_footer" id="vc_post-custom-js-footer" value="<?php echo esc_attr( $editor->post_custom_js_footer ); ?>" autocomplete="off"/>
<input type="hidden" name="vc_post_custom_layout" id="vc_post-custom-layout" value="<?php echo esc_attr( $editor->post_custom_layout ); ?>" autocomplete="off"/>
<input type="hidden" name="vc_post_custom_seo_settings" id="vc_post-custom-seo-settings" value="<?php echo esc_attr( $editor->post_custom_seo_settings ); ?>" autocomplete="off"/>
