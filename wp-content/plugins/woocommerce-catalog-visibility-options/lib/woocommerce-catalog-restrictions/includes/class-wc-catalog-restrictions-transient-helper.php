<?php

class WC_Catalog_Restrictions_Transient_Helper {
	private static $queued = false;

	public static function queue_delete_transients() {
		if ( self::$queued ) {
			return;
		}
		add_action( 'shutdown', [ __CLASS__, '_delete_transients_on_shutdown' ], 9999 );
		self::$queued = true;
	}

	/**
	 * Delete transients on shutdown.
	 */
	public static function _delete_transients_on_shutdown() {
		global $wpdb;

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_related%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_loop%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_product_loop%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_product_query%'" );

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_twccr%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_twccr%'" );

		if ( WC_Catalog_Restrictions::instance()->use_db_filter_cache ) {
			$table = $wpdb->prefix . 'wc_cvo_cache';
			$wpdb->query( "DELETE FROM $table" );
		}

		wp_cache_flush();
	}

}
