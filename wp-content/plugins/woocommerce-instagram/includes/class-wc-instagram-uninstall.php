<?php
/**
 * Uninstall and deactivation actions.
 *
 * @package WC_Instagram
 * @since   4.1.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Uninstall.
 */
class WC_Instagram_Uninstall {

	/**
	 * Plugin deactivation.
	 *
	 * @since 4.1.1
	 */
	public static function deactivate() {
		WC_Instagram_Actions::clear( 'generate_catalogs' );
		WC_Instagram_Post_Types::unregister_post_types();

		flush_rewrite_rules();
	}

	/**
	 * Plugin uninstall.
	 *
	 * @since 4.1.1
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function uninstall() {
		global $wpdb;

		/*
		 * Only remove ALL the plugin data if WC_REMOVE_ALL_DATA constant is set to true in the wp-config.php file.
		 * This is to prevent data loss when deleting the plugin from the backend
		 * and to ensure only the site owner can perform this action.
		 */
		if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
			// Delete the plugin settings.
			delete_option( 'wc_instagram_settings' );

			// Delete options and transients.
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%wc_instagram_%';" );

			// Delete products metas.
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_instagram_hashtag%';" );

			// Delete posts + data.
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'wc_instagram_catalog' );" );
			$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

			// Clear any cached data that has been removed.
			wp_cache_flush();
		} else {
			include_once 'wc-instagram-functions.php';

			// Only delete credentials.
			wc_instagram_disconnect();
		}
	}
}
