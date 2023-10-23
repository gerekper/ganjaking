<?php
/**
 * Badge Content
 *
 * @var string $text      The badge text.
 * @var string $position  The badge position.
 * @var string $image_url The badge image URL.
 * @var string $type      The badge type.
 * @var int    $id_badge  The id of the badge.
 *
 * @package YITH\BadgeManagement\Templates
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

$position_css = '';
if ( 'top-left' === $position ) {
	$position_css = 'top: 0; left: 0;';
} elseif ( 'top-right' === $position ) {
	$position_css = 'top: 0; right: 0;';
} elseif ( 'bottom-left' === $position ) {
	$position_css = 'bottom: 0; left: 0;';
} elseif ( 'bottom-right' === $position ) {
	$position_css = 'bottom: 0; right: 0;';
}

// WPML integration.
$text = yith_wcbm_wpml_string_translate( 'yith-woocommerce-badges-management', sanitize_title( $text ), $text );
if ( 'text' !== $type ) {
	// Image Badge.
	if ( 'upload' === $image_url ) {
		$image_url = defined( 'YITH_WCBM_PREMIUM' ) ? wp_get_attachment_image_url( get_post_meta( $id_badge, '_uploaded_image_id', true ), 'full' ) : '';
	} else {
		$image_url = YITH_WCBM_ASSETS_URL . 'images/image-badges/' . $image_url;
	}
	$text = '<img src="' . $image_url . '" />';
}
?>
<div class='yith-wcbm-badge yith-wcbm-badge-custom yith-wcbm-badge-<?php echo absint( $id_badge ); ?>'><?php echo wp_kses_post( $text ); ?></div><!--yith-wcbm-badge-->
