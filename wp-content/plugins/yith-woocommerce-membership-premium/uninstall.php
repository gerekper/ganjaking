<?php
/**
 * YITH WooCommerce Membership Premium Uninstall
 *
 * @since 1.3.3
 */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

wp_clear_scheduled_hook( 'yith_wcmbs_check_expiring_membership' );
wp_clear_scheduled_hook( 'yith_wcmbs_check_expired_membership' );
wp_clear_scheduled_hook( 'yith_wcmbs_check_credits_in_membership' );
