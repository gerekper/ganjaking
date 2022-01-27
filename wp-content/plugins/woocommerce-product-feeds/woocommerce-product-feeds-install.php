<?php

defined( 'ABSPATH' ) || exit;

/**
 * Create database table to cache the Google product taxonomy.
 */
function woocommerce_gpf_install() {
	global $wpdb;

	$db_version      = $wpdb->db_version();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name = $wpdb->prefix . 'woocommerce_gpf_google_taxonomy';
	$sql        = "CREATE TABLE $table_name (
	            taxonomy_term text,
	            search_term text,
	            locale varchar(5),
                KEY locale_index (locale)
			) $charset_collate";
	dbDelta( $sql );

	/**
	 * @TODO : https://core.trac.wordpress.org/ticket/49364
	 */
	if ( version_compare( $db_version, '8.0.17', '<' ) ) {
		$int_def = 'bigint(20)';
	} else {
		$int_def = 'bigint';
	}

	$sql = 'CREATE TABLE `' . $wpdb->prefix . 'wc_gpf_render_cache` (
	  `id` ' . $int_def . ' unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  `post_id` ' . $int_def . " unsigned NOT NULL,
	  `name` varchar(32) NOT NULL,
	  `value` LONGTEXT NOT NULL,
	  UNIQUE KEY composite_cache_idx (`post_id`, `name`)
	) $charset_collate";

	dbDelta( $sql );

	flush_rewrite_rules();

	// Upgrade old tables on plugin deactivation / activation.
	$wpdb->query( "ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" );

	// Set default settings if there are none.
	$settings = get_option( 'woocommerce_gpf_config' );
	if ( false === $settings ) {
		$settings                       = array(
			'product_fields'      => array(
				'title'                   => 'on',
				'availability_instock'    => 'on',
				'availability_backorder'  => 'on',
				'availability_outofstock' => 'on',
				'brand'                   => 'on',
				'mpn'                     => 'on',
				'product_type'            => 'on',
				'google_product_category' => 'on',
				'size_system'             => 'on',
			),
			'product_defaults'    => array(
				'availability_instock'    => 'in stock',
				'availability_backorder'  => 'in stock',
				'availability_outofstock' => 'out of stock',
			),
			'product_prepopulate' => array(
				'title'       => 'field:product_title',
				'description' => 'description:fullvar',
			),
		);
		$settings['include_variations'] = 'on';
		$settings['send_item_group_id'] = 'on';
		add_option( 'woocommerce_gpf_config', $settings, '', 'yes' );
	}

	// Other updates to apply, but only if this is a genuinely fresh install.
	// Otherwise DB upgrade should take care of things
	if ( get_option( 'woocommerce_gpf_db_version' ) === false ) {
		// Set a debug key if none available already.
		if ( get_option( 'woocommerce_gpf_debug_key' ) === false ) {
			update_option( 'woocommerce_gpf_debug_key', wp_generate_uuid4() );
		}
		// If we have no active feeds
		if ( get_option( 'woocommerce_gpf_feed_configs' ) === false ) {
			update_option(
				'woocommerce_gpf_feed_configs',
				[
					substr( wp_hash( microtime() ), 0, 16 ) =>
						[
							'type' => 'google',
							'name' => __( 'Google merchant centre product feed', 'woocommerce_gpf' ),
						],
				]
			);
		}
		// Set the currently installed DB version
		update_option( 'woocommerce_gpf_db_version', WOOCOMMERCE_GPF_DB_VERSION );
	}

	if ( get_option( 'woocommerce_gpf_install_ts' ) === false ) {
		update_option( 'woocommerce_gpf_install_ts', time(), false );
	}

	// Flag that rewrite rules will need flushing.
	set_site_transient( 'woocommerce_gpf_rewrite_flush_required', '1' );
}

