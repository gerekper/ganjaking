<?php

class WoocommerceProductFeedsDbManager {

	/**
	 * @var WoocommerceGpfCache
	 */
	protected $woocommerce_gpf_cache;

	/**
	 * @var WoocommerceProductFeedsFeedConfigRepository
	 */
	protected $feed_config_repository;

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $commmon;

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
	 * @param WoocommerceProductFeedsFeedConfigRepository $feed_config_repository
	 * @param WoocommerceGpfCommon $common
	 */
	public function __construct(
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceProductFeedsFeedConfigRepository $feed_config_repository,
		WoocommerceGpfCommon $common
	) {
		$this->cache                  = $woocommerce_gpf_cache;
		$this->feed_config_repository = $feed_config_repository;
		$this->common                 = $common;
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

	/**
	 * Upgrade the DB schema to v10.
	 *
	 * Sets up a feed config entry for each enabled feed.
	 */
	public function upgrade_db_to_10() {
		$feed_types        = $this->common->get_feed_types();
		$gpf_enabled_feeds = $this->settings['gpf_enabled_feeds'] ?? [];
		foreach ( array_keys( $gpf_enabled_feeds ) as $enabled_feed ) {
			$config = [
				'type' => $enabled_feed,
				'name' => isset( $feed_types[ $enabled_feed ]['name'] ) ?
					$feed_types[ $enabled_feed ]['name'] :
					"$enabled_feed feed",
			];
			$this->feed_config_repository->save( $config, $enabled_feed );
		}
		unset( $this->settings['gpf_enabled_feeds'] );
		update_option( 'woocommerce_gpf_config', $this->settings );
	}

	/**
	 * Upgrade the DB schema to v11.
	 *
	 * Migrate "availability" options.
	 */
	public function upgrade_db_to_11() {
		$legacy_availability = isset( $this->settings['product_defaults']['availability'] ) ?
			$this->settings['product_defaults']['availability'] :
			'in stock';

		$this->settings['product_fields']['availability_instock']    = 'on';
		$this->settings['product_fields']['availability_backorder']  = 'on';
		$this->settings['product_fields']['availability_outofstock'] = 'on';
		unset( $this->settings['product_fields']['availability'] );

		$this->settings['product_defaults']['availability_instock']    = $legacy_availability;
		$this->settings['product_defaults']['availability_backorder']  = $legacy_availability;
		$this->settings['product_defaults']['availability_outofstock'] = 'out of stock';
		unset( $this->settings['product_defaults']['availability'] );

		update_option( 'woocommerce_gpf_config', $this->settings );
	}

	/**
	 * Add locale to the woocommerce_gpf_google_taxonomy cache table. Drop existing cached data.
	 *
	 * @return void
	 */
	public function upgrade_db_to_12() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . 'woocommerce_gpf_google_taxonomy';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
	            taxonomy_term text,
	            search_term text,
	            locale varchar(5),
                KEY locale_index (locale)
			) $charset_collate";
		dbDelta( $sql );

		$sql = "DELETE FROM $table_name";
		$wpdb->query( $sql );
	}

	/**
	 * Set a value for woocommerce_gpf_install_ts.
	 *
	 * @return void
	 */
	public function upgrade_db_to_13() {
		if ( get_option( 'woocommerce_gpf_install_ts' ) === false ) {
			update_option( 'woocommerce_gpf_install_ts', time(), false );
		}
	}
}
