<?php
/**
 * The template for displaying the end of the quantity selector of an option
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-element-quantity-end.php
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

if ( ! empty( $quantity ) ) {
	echo '</div></div></div>';
}
