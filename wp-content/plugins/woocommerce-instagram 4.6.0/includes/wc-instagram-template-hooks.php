<?php
/**
 * Template hooks
 *
 * @package WC_Instagram/Templates
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue frontend scripts.
 *
 * @since 2.0.0
 */
add_action( 'wp_enqueue_scripts', 'wc_instagram_enqueue_scripts' );

/**
 * After Single Product div.
 *
 * @see wc_instagram_product_hashtag()
 *
 * @since 2.0.0
 */
add_action( 'woocommerce_after_single_product', 'wc_instagram_product_hashtag' );
