<?php

defined( 'ABSPATH' ) || exit;

/**
 * Create database table to cache the Google product taxonomy.
 */
function woocommerce_gpf_install() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'woocommerce_gpf_google_taxonomy';

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE $table_name (
	            taxonomy_term text,
	            search_term text
			) $charset_collate";
	dbDelta( $sql );

	$sql = 'CREATE TABLE `' . $wpdb->prefix . "wc_gpf_render_cache` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  `post_id` bigint(20) unsigned NOT NULL,
	  `name` varchar(32) NOT NULL,
	  `value` LONGTEXT NOT NULL,
	  UNIQUE KEY composite_cache_idx (`post_id`, `name`)
	) $charset_collate";
	dbDelta( $sql );

	flush_rewrite_rules();

	// Upgrade old tables on plugin deactivation / activation.
	$wpdb->query( "ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" );

	update_option( 'woocommerce_gpf_db_version', WOOCOMMERCE_GPF_DB_VERSION );

	// Set default settings if there are none.
	$settings = get_option( 'woocommerce_gpf_config' );
	if ( false === $settings ) {
		$settings                       = array(
			'product_fields'      => array(
				'title'                   => 'on',
				'availability'            => 'on',
				'brand'                   => 'on',
				'mpn'                     => 'on',
				'product_type'            => 'on',
				'google_product_category' => 'on',
				'size_system'             => 'on',
			),
			'product_defaults'    => array(
				'availability' => 'in stock',
			),
			'product_prepopulate' => array(
				'title'       => 'field:product_title',
				'description' => 'description:fullvar',
			),
			'gpf_enabled_feeds'   => array( 'google' => 'on' ),
		);
		$settings['include_variations'] = 'on';
		$settings['send_item_group_id'] = '';
		add_option( 'woocommerce_gpf_config', $settings, '', 'yes' );
	}
	if ( get_option( 'woocommerce_gpf_debug_key' ) === false ) {
		update_option( 'woocommerce_gpf_debug_key', wp_generate_uuid4() );
	}
}

