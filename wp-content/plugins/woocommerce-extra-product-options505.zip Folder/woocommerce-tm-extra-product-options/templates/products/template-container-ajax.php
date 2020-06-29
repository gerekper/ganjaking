<?php
/**
 * The template for displaying the product html via ajax
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

$attributes      = $product_list[ $product_id ];
$current_product = wc_get_product( $product_id );
include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/' . $template . '.php' );
