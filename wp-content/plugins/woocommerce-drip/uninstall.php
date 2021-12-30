<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete WooCommerce Drip Integration Settings
delete_option( 'woocommerce_wcdrip_settings' );

// Anyo!