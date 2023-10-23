<?php
/**
 * Badge Content Premium
 *
 * @var int|string      $product_id Product ID on which the badge is.
 * @var YITH_WCBM_Badge $badge      The Badge Object.
 *
 * @package YITH\BadgeManagementPremium\Views
 */

$position_data      = $badge->get_positions();
$position_data_json = htmlspecialchars( wp_json_encode( $position_data ) );
$position_data_html = " data-position='$position_data_json'";

$is_preview = 'preview' === $product_id;
// WPML.
$text = yith_wcbm_wpml_string_translate( 'yith-woocommerce-badges-management', sanitize_title( $badge->get_text() ), $badge->get_text() );

// TODO: deprecate these filters, use CRUD ones instead.
if ( $badge->is_type( 'text' ) ) {
	$text = apply_filters( 'yith_wcbm_text_badge_text', $text, $badge->get_data(), $badge );
} elseif ( $badge->is_type( 'css' ) ) {
	$text = apply_filters( 'yith_wcbm_css_badge_text', $text, $badge->get_data(), $badge );
}

$product = wc_get_product( $product_id );

$badge_classes = $badge->get_classes( $product );

switch ( $badge->get_type() ) {

	case 'text':
	case 'custom':
		yith_wcbm_get_view( 'badges/text.php', compact( 'badge', 'product', 'is_preview' ) );
		break;

	case 'image':
		$image_alt = apply_filters( 'yith_wcbm_image_badge_alt_text', 'YITH Badge', $badge->get_id() );
		yith_wcbm_get_view( 'badges/image.php', compact( 'badge', 'product', 'image_alt', 'is_preview' ) );
		break;

	case 'css':
		$css_badge_style = absint( str_replace( '.svg', '', $badge->get_css() ) );
		$css_badge_style = $css_badge_style ? $css_badge_style : 1;

		yith_wcbm_get_view( 'badges/css.php', compact( 'badge', 'product', 'css_badge_style', 'is_preview' ) );
		break;

	case 'advanced':
		$product_is_on_sale = yith_wcbm_product_is_on_sale( $product );

		if ( ( $product && $product_is_on_sale ) || 'preview' === $product_id ) {
			$id_advanced_badge = $badge->get_id();
			$advanced_style    = absint( str_replace( '.svg', '', $badge->get_advanced() ) );
			yith_wcbm_get_view( 'badges/advanced.php', compact( 'badge', 'product', 'position_data_html', 'advanced_style', 'is_preview' ) );
		}
		break;
}
