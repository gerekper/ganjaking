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
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $style ) ) {
	$style = '';
}
echo '</div>';

if ( 'collapse' === $style || 'collapseclosed' === $style || 'accordion' === $style ) {
	echo '</div>';
}
if ( isset( $sections_type ) && 'popup' === $sections_type ) {
	echo '</div>';
}
echo '</div>';
echo '</div></div></div>';
