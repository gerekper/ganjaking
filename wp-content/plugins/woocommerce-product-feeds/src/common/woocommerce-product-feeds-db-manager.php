<?php

class WoocommerceProductFeedsDbManager {

	/**
	 * @var WoocommerceGpfCache
	 */
	protected $woocommerce_gpf_cache;

	/**
	 * @var array
	 */
	private $settings = array();

	/**
	 * @var WoocommerceGpfCache
	 */
	private $cache;

	/**
	 * WoocommerceProductFeedsDbManager constructor.
	 *
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 */
	public function __construct( WoocommerceGpfCache $woocommerce_gpf_cache ) {
		$this->cache = $woocommerce_gpf_cache;
	}

	/**
	 * Trigger the update checks on admin requests.
	 */
	public function initialise() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		$this->settings = get_option( 'woocommerce_gpf_config', array() );
		add_action( 'admin_init', array( $this, 'check_db' ), 12 );
	}

	/**
	 * Check the database version, and upgrade if required.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	public function check_db() {
		// TODO - do we need locking?
		$current_version = (int) get_option( 'woocommerce_gpf_db_version', 1 );
		if ( $current_version >= WOOCOMMERCE_GPF_DB_VERSION ) {
			return;
		}
		// Otherwise, check for, and run updates.
		foreach ( range( $current_version + 1, WOOCOMMERCE_GPF_DB_VERSION ) as $version ) {
			if ( is_callable( array( $this, 'upgrade_db_to_' . $version ) ) ) {
				$this->{'upgrade_db_to_' . $version}();
			}
			update_option( 'woocommerce_gpf_db_version', $version );
		}
	}

	/**
	 * Upgrade the DB schema to v2.
	 *
	 * Creates render cache table.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_2() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = 'CREATE TABLE `' . $wpdb->prefix . "wc_gpf_render_cache` (
		    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    `post_id` bigint(20) unsigned NOT NULL,
		    `name` varchar(32) NOT NULL,
		    `value` text NOT NULL,
		    UNIQUE KEY composite_cache_idx (`post_id`, `name`)
		) $charset_collate";
		dbDelta( $sql );
	}

	/**
	 * Upgrade the DB schema to v3.
	 *
	 * Update render cache table to support LONGTEXT values.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_3() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = 'CREATE TABLE `' . $wpdb->prefix . "wc_gpf_render_cache` (
		    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    `post_id` bigint(20) unsigned NOT NULL,
		    `name` varchar(32) NOT NULL,
		    `value` LONGTEXT NOT NULL,
		    UNIQUE KEY composite_cache_idx (`post_id`, `name`)
		) $charset_collate";
		dbDelta( $sql );
	}

	/**
	 * Upgrade the DB schema to v4.
	 *
	 * Set all feeds to active on upgraded installs.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_4() {
		if ( ! isset( $this->settings['gpf_enabled_feeds'] ) ) {
			$this->settings['gpf_enabled_feeds'] = array(
				'google'          => 'on',
				'googleinventory' => 'on',
				'bing'            => 'on',
			);
			update_option( 'woocommerce_gpf_config', $this->settings );
		}
	}

	/**
	 * Upgrade the DB schema to v5.
	 *
	 * Set the description setting to "varfull".
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_5() {
		if ( empty( $this->settings['product_prepopulate']['description'] ) ) {
			$this->settings['product_prepopulate']['description'] = 'description:varfull';
			update_option( 'woocommerce_gpf_config', $this->settings );
		}
	}

	/**
	 * Upgrade the DB schema to v6.
	 *
	 * Refresh the field list so new field prepopulation options become visible.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_6() {
		delete_transient( 'woocommerce_gpf_meta_prepopulate_options' );
	}

	/**
	 * Upgrade the DB schema to v7.
	 *
	 * Generate a unique site debug key.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_7() {
		update_option( 'woocommerce_gpf_debug_key', wp_generate_uuid4() );
	}

	/**
	 * Upgrade the DB schema to v8.
	 *
	 * No database changes, but ensure that the cache remains consistent.
	 */
	public function upgrade_db_to_8() {
		global $wpdb;

		$identifiers = [
			'wp_woocommerce_gpf_rebuild_all',
			'wp_woocommerce_gpf_rebuild_product',
			'wp_woocommerce_gpf_rebuild_term',
		];

		$table  = $wpdb->options;
		$column = 'option_name';
		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		// Establish if we have any queue jobs pending in the old wp-background-processing queues.
		$count = 0;
		foreach ( $identifiers as $identifier ) {
			$count += $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*)
				       FROM {$table}
				      WHERE {$column}
				       LIKE %s",
					$wpdb->esc_like( $identifier . '_batch_' ) . '%'
				)
			);
		}

		// If so, run a full cache rebuild just to be on the safe side.
		if ( $count ) {
			$this->cache->flush_all();
		}
	}

	/**
	 * Upgrade the DB schema to v9.
	 *
	 * Set the description setting to "varfull".
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function upgrade_db_to_9() {
		$this->settings['product_fields']['title'] = 'on';
		if ( empty( $this->settings['product_prepopulate']['title'] ) ) {
			$this->settings['product_prepopulate']['title'] = 'field:product_title';
		}
		update_option( 'woocommerce_gpf_config', $this->settings );
	}
}
