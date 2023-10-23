<?php
/**
 * Uninstall plugin
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.0.0
 */

// If uninstall not called from WordPress exit.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete pages created for this plugin.
wp_delete_post( get_option( 'yith-wrvp-page-id' ), true );
delete_option( 'yith-wrvp-page-id' );
