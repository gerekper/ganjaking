<?php
/**
 * The template for displaying the template element
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-template.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$tm_product = false;
if ( ! empty( $tm_product_id ) ) {
	$tm_product = wc_get_product( $tm_product_id );
}
