<?php
if ( ! ( ( defined( 'WP_UNINSTALL_PLUGIN' ) && WP_UNINSTALL_PLUGIN ) || defined( 'WPB_RS_DEPRECATED_UNINSTALL' ) ) ) {
	exit();
}

/**
 * @var wpdb $wpdb
 */
global $wpdb;

if ( is_a( $wpdb, 'wpdb' ) ) {
	$post_ids = $wpdb->get_var( 'SELECT GROUP_CONCAT( CONCAT_WS( ",", ID) )  FROM ' . $wpdb->posts . ' WHERE `post_type` LIKE "rswp-shortcode"' );

	$post_ids = explode( ',', $post_ids );
	foreach ( $post_ids as $id ) {
		wp_delete_post( $id );
	}
}

$shortcodes = get_posts( array(
	'posts_per_page' => - 1,
	'post_type'      => 'rswp-shortcode',
	'post_status'    => 'any',
) );

delete_option( 'rswp_just_activated' );
delete_option( 'wpb_plugin_rich-snippets-wordpress-plugin_version' );
delete_option( 'rswp-settings' );

