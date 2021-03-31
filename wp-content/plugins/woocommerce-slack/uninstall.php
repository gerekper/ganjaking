<?php

// Stuff to do on the uninstall / deletion of the plugin
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Delete WC Slack Settings
delete_option( 'woocommerce_wcslack_settings' );

// Anyo!
