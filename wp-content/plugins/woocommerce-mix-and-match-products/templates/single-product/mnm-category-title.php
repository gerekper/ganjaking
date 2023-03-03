<?php
/**
 * Mix and Match Item Category Title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-category-title.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   2.4.0
 * @version 2.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param WP_Term object $category
 */
woocommerce_template_loop_category_title( $category );