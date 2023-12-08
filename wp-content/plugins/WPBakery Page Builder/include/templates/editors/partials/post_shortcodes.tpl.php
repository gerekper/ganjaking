<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_tag = 'script'; // TODO: Update response to ajax array
?>
<<?php echo esc_attr( $custom_tag ); ?>>
	window.vc_post_shortcodes = JSON.parse( decodeURIComponent( ("<?php echo rawurlencode( wp_json_encode( $editor->post_shortcodes ) ); ?>" + '') ) );
</<?php echo esc_attr( $custom_tag ); ?>>
