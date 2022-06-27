<?php
/**
 * The template for displaying the end of a section in the builder mode options
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-builder-section-end.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

echo '</div>';

if ( 'box' === $style ) {
	echo '</div>';
}
if ( 'collapse' === $style || 'collapseclosed' === $style || 'accordion' === $style ) {
	echo '</div></div>';
}
if ( isset( $sections_type ) && 'popup' === $sections_type ) {
	echo '</div>';
}
if ( ! empty( $description ) && ! empty( $description_position ) && 'below' === $description_position ) {
	echo '<div';
	$descriptionclass = '';
	if ( ! empty( $description_color ) ) {
		$descriptionclass = ' color-' . sanitize_hex_color_no_hash( $description_color );
	}
	// $description contains HTML code
	echo ' class="tm-section-description tm-description' . esc_attr( $descriptionclass ) . '">' . apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
echo '</div></div></div></div>';
