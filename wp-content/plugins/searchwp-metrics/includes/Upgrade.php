<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Upgrade
 *
 * @package SearchWP_Metrics
 */
class Upgrade {
	/**
	 * @var string Active plugin version
	 *
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * @var mixed|void The last version that was active
	 *
	 * @since 1.0.0
	 */
	public $last_version;

	/**
	 * @var string Charset for the database
	 *
	 * @since 1.0.0
	 */
	private $charset = 'utf8';

	/**
	 * @var string COLLATE SQL (when utf8mb4)
	 *
	 * @since 1.0.0
	 */
	private $collate_sql = '';

	/**
	 * Constructor
	 *
	 * @param bool|string $version string Plugin version being activated
	 *
	 * @since 1.0
	 */
	public function __construct( $version = false ) {

		global $wpdb;

		// WordPress 4.2 added support for utf8mb4
		if ( $wpdb->has_cap( 'utf8mb4' ) ) {
			$this->charset      = 'utf8mb4';
			$this->collate_sql  = ' COLLATE utf8mb4_unicode_ci ';
		}

		if ( ! empty( $version ) ) {
			$this->version      = $version;
			$this->last_version = get_option( SEARCHWP_METRICS_PREFIX . 'version' );

			if ( false === $this->last_version ) {
				$this->last_version = 0;
			}

			if ( ! $this->tables_exist() ) {
				$this->install();
			}

			if ( version_compare( $this->last_version, $this->version, '<' ) ) {
				if ( version_compare( $this->last_version, '0.1.0', '<' ) ) {
					add_option( SEARCHWP_METRICS_PREFIX . 'version', $this->version, '','no' );

					// If this is a fresh install it means that the indexer can support utf8mb4
					if ( 'utf8mb4' === $this->charset ) {
						add_option( SEARCHWP_METRICS_PREFIX . 'utf8mb4', true, '', 'no' );
					}
				} else {
					$this->upgrade();
					update_option( SEARCHWP_METRICS_PREFIX . 'version', $this->version, 'no' );
				}
			}
		}

	}

	/**
	 * Determines whether database tables exist.
	 *
	 * @since 1.0.0
	 */
	function tables_exist() {
		global $wpdb;

		$metrics = new \SearchWP_Metrics();

		$tables_exist = true;

		$tables = array(
			$metrics->get_table_name( 'clicks' ),
			$metrics->get_table_name( 'ids' ),
			$metrics->get_table_name( 'queries' ),
			$metrics->get_table_name( 'searches' ),
		);

		foreach ( $tables as $table ) {
			$table_sql = $wpdb->get_results( "SHOW TABLES LIKE '{$table}'" , ARRAY_N );
			if ( empty( $table_sql ) ) {
				$tables_exist = false;
				break;
			}
		}

		return $tables_exist;
	}


	/**
	 * Installation procedure; create database tables
	 *
	 * @since 1.0.0
	 */
	private function install() {
		$this->create_tables();
	}

	/**
	 * Create custom database tables
	 */
	private function create_tables() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$metrics = new \SearchWP_Metrics();

		// Clicks table
		$clicks_table_name = $metrics->get_table_name( 'clicks' );
		$sql = "
			CREATE TABLE $clicks_table_name (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time the click happened',
				`hash` bigint(20) DEFAULT NULL COMMENT 'From searches table, public hash of search that triggered click',
				`position` int(9) unsigned NOT NULL COMMENT 'Position in SERP',
				`post_id` bigint(20) unsigned DEFAULT NULL,
				PRIMARY KEY (id),
					KEY hash (hash),
					KEY position (position)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		dbDelta( $sql );

		// IDS table
		$ids_table_name = $metrics->get_table_name( 'ids' );
		$sql = "
			CREATE TABLE $ids_table_name (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`value` char(32) NOT NULL DEFAULT '',
				`type` varchar(20) DEFAULT 'hash',
				PRIMARY KEY (id),
					UNIQUE KEY `keyunique` (`value`),
					KEY hash (type)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		dbDelta( $sql );

		// Queries table
		$queries_table_name = $metrics->get_table_name( 'queries' );

		// If utf8mb4 collation is supported, add it
		$varchar_collate = '';
		if ( 'utf8mb4' === $this->charset ) {
			// Normally it's utfmb4_unicode_ci but that is not strict enough for UNIQUE keys
			$varchar_collate = ' COLLATE utf8mb4_bin ';
		}

		$sql = "
			CREATE TABLE $queries_table_name (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`query` varchar(191) CHARACTER SET utf8mb4 {$varchar_collate} NOT NULL DEFAULT '' COMMENT 'The search query itself',
				PRIMARY KEY (id),
					UNIQUE KEY query (query)
			) DEFAULT CHARSET=" . $this->charset . $varchar_collate;
		dbDelta( $sql );

		// Searches table
		$searches_table_name = $metrics->get_table_name( 'searches' );
		$sql = "
			CREATE TABLE $searches_table_name (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`query` bigint(20) unsigned NOT NULL COMMENT 'ID of search query stored in table',
				`engine` varchar(191) NOT NULL DEFAULT 'default' COMMENT 'Engine used for search',
				`tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of search',
				`hits` int(9) unsigned NOT NULL COMMENT 'How many hits were found',
				`hash` bigint(20) unsigned NOT NULL COMMENT 'Public representation of each search, allows linking subsequent events (e.g. click) to this search',
				`uid` bigint(20) unsigned DEFAULT NULL COMMENT 'Anonymous user ID',
				PRIMARY KEY (id),
					KEY engine (engine),
					KEY query (query),
					KEY hits (hits),
					KEY hash (hash),
					KEY uid (uid)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		dbDelta( $sql );

		// Meta table
		$this->create_meta_table();
	}

	function create_meta_table() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$metrics = new \SearchWP_Metrics();

		$meta_table_name = $metrics->get_table_name( 'meta' );
		$sql = "
			CREATE TABLE $meta_table_name (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`hashid` bigint(20) NOT NULL,
				`uid` bigint(20) DEFAULT NULL,
				`object` varchar(80) DEFAULT NULL,
				`object_id` bigint(20) unsigned DEFAULT NULL,
				`meta_key` varchar(191),
				`meta_value` longtext,
				PRIMARY KEY (id),
					KEY object (object),
					KEY object_id (object_id),
					KEY hashid (hashid),
					KEY uid (uid),
					KEY meta_key (meta_key)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		dbDelta( $sql );
	}

	/**
	 * Upgrade routine
	 */
	function upgrade() {
		global $wpdb;

		// $busy = get_option( SEARCHWP_METRICS_PREFIX . 'doing_upgrade' );

		// if ( ! empty( $busy ) ) {
		// 	// There's already an upgrade running.
		// 	return;
		// }

		// // Set our flag that an upgrade is running.
		// update_option( SEARCHWP_METRICS_PREFIX . 'doing_upgrade', true, 'no' );

		$metrics = new \SearchWP_Metrics();

		// Improve performance with additional index.
		if ( version_compare( $this->last_version, '1.0.8', '<' ) ) {
			$ids_table_name = $metrics->get_table_name( 'ids' );
			$wpdb->query( "ALTER TABLE {$ids_table_name} ADD INDEX `key` (`value`)" );
		}

		if ( version_compare( $this->last_version, '1.2.5', '<' ) ) {
			$this->create_meta_table();

			$blocklists = get_option( $metrics->get_db_prefix() . 'blacklists' );

			if ( ! empty( $blocklists ) ) {
				update_option( $metrics->get_db_prefix() . 'blocklists', $blocklists );
			}
		}

		if ( version_compare( $this->last_version, '1.3.0', '<' ) ) {
			$table = $metrics->get_table_name( 'meta' );
			$wpdb->query( "DROP TABLE {$table}" );
			$this->create_meta_table();
		}

		// update_option( SEARCHWP_METRICS_PREFIX . 'doing_upgrade', false, 'no' );
	}
}
