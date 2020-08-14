<?php
/**
 * Color settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

return array(
	'premium' => array(
		'landing' => array(
			'type' => 'custom_tab',
			'action' => 'yith_wcwl_premium_tab',
			'hide_sidebar' => true
		)
	)
);