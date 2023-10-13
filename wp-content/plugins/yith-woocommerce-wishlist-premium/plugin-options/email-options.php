<?php
/**
 * Email options
 *
 * @package YITH\GiftCards\PluginOptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

return array(
	'email' => array(
		'yith_wcwl_email_settings' => array(
			'type'        => 'custom_tab',
			'action'      => 'yith_wcwl_email_settings',
			'title'       => __( 'Emails', 'yith-woocommerce-wishlist' ),
			'description' => __( 'Manage and customize the emails sent to users about wishlists.', 'yith-woocommerce-wishlist' ),
		),
	),
);
