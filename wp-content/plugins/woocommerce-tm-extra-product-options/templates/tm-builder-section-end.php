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
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;
echo '</div>'; // tc-row
if ( $style == "box" ) {
	echo '</div>';
}
if ( $style == "collapse" || $style == "collapseclosed" || $style == "accordion" ) {
	echo '</div></div>';
}
if ( isset( $sections_type ) && $sections_type == "popup" ) {
	echo '</div>';
}
if ( ! empty( $description ) && ! empty( $description_position ) && $description_position == "below" ) {
	echo '<div';
	$descriptionclass = "";
	if ( ! empty( $description_color ) ) {
		$descriptionclass = " color-" . sanitize_hex_color_no_hash( $description_color );
	}
	// $description contains HTML code
	echo ' class="tm-description' . esc_attr( $descriptionclass ) . '">' . apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ) . '</div>';
}
?>
</div></div></div></div>