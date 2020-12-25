<?php
/**
 * class-woocommerce-product-search-controller.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller to handle table creation.
 */
class WooCommerce_Product_Search_Controller {

	/**
	 * @var integer maximum number of characters stored in our key table
	 */
	const MAX_KEY_LENGTH = 100;

	/**
	 * @var integer maximum number of characters stored in our query table
	 */
	const MAX_QUERY_LENGTH = 250;

	/**
	 * @var integer maximum number of characters stored in our URI table
	 */
	const MAX_URI_LENGTH = 2048;

	/**
	 * @var integer maximum number of characters stored in our user agent table
	 */
	const MAX_USER_AGENT_LENGTH = 255;

	/**
	 * @var integer default max_allowed_packet with MySQL 5.7
	 */
	const MYSQL_57_MAX_ALLOWED_PACKET_DEFAULT = 4194304;

	/**
	 * @var integer minimum max_allowed_packet with MySQL 5.7
	 */
	const MYSQL_57_MAX_ALLOWED_PACKET_MIN = 1024;

	/**
	 * @var integer seconds in a day
	 */
	const SECONDS_PER_DAY = 86400;

	/**
	 * @var integer reduces the object limit proportionally
	 */
	const OBJECT_LIMIT_FACTOR = 4;

	/**
	 * @var integer minimum object limit for AUTO mode
	 */
	const MIN_OBJECT_LIMIT = 1000;

	/**
	 * @var string controller's cache group
	 */
	const CACHE_GROUP = 'ixwpsctrl';

	/**
	 * Existing tables.
	 *
	 * @var array
	 */
	private static $tables = null;

	/**
	 * Returns the name of the requested table appropriately prefixed.
	 *
	 * @param string $name unprefixed name of the table
	 *
	 * @return string prefixed table name
	 */
	public static function get_tablename( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'wps_' . $name;
	}

	/**
	 * Whether the table exists.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public static function table_exists( $name ) {

		global $wpdb;
		$exists = false;
		if ( self::$tables === null ) {
			self::$tables = get_option( 'woocommerce_product_search_plugin_tables', array() );
		}
		if ( in_array( $name, self::$tables ) ) {
			$exists = true;
		} else {
			$table = self::get_tablename( $name );
			$exists = $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table;
			if ( $exists ) {
				self::$tables[] = $name;
				update_option( 'woocommerce_product_search_plugin_tables', self::$tables, true );
			}
		}
		return $exists;
	}

	/**
	 * Obtain the value of max_allowed_packet.
	 *
	 * @since 3.0.0
	 *
	 * @return int value
	 */
	public static function get_max_allowed_packet() {

		global $wpdb;
		$value = null;
		$query = "SHOW variables LIKE 'max_allowed_packet'";
		$results = $wpdb->get_results( $query );
		if ( is_array( $results ) ) {
			$variable = array_shift( $results );
			if ( isset( $variable->Value ) ) {
				$value = intval( $variable->Value );
			}
		}

		if ( $value === null ) {
			$value = self::MYSQL_57_MAX_ALLOWED_PACKET_DEFAULT;
		}

		if ( $value < self::MYSQL_57_MAX_ALLOWED_PACKET_MIN ) {
			$value = self::MYSQL_57_MAX_ALLOWED_PACKET_MIN;
		}
		return $value;
	}

	/**
	 * Returns the object limit.
	 *
	 * @return int object limit, 0 for unlimited
	 */
	public static function get_object_limit() {

		global $wpdb;
		$limit = 0;
		$wps_object_limit = strtoupper( trim( WPS_OBJECT_LIMIT ) );
		if ( $wps_object_limit === 'AUTO' || $wps_object_limit === 'AUTOREPORT' ) {

			$factor = self::OBJECT_LIMIT_FACTOR;
			if ( defined( 'WPS_OBJECT_LIMIT_FACTOR' ) && is_numeric( WPS_OBJECT_LIMIT_FACTOR ) ) {
				$factor = intval( WPS_OBJECT_LIMIT_FACTOR );
				if ( $factor <= 0 ) {
					$factor = self::OBJECT_LIMIT_FACTOR;
				}
			}

			$limit = wp_cache_get( 'object_limit', self::CACHE_GROUP );
			if ( $limit === false ) {
				$max_allowed_packet = self::get_max_allowed_packet();
				$max_id_size = $wpdb->get_var( "SELECT LENGTH(MAX(ID)) FROM $wpdb->posts" );
				if ( $max_id_size !== null ) {
					$max_id_size = intval( $max_id_size );
					if ( $max_id_size <= 0 ) {
						$max_id_size = null;
					}
				}
				if ( $max_id_size === null ) {
					$max_id_size = floor( strlen( '' . PHP_INT_MAX ) / 1.62 );
				}

				$limit = floor( $max_allowed_packet / ( $factor * $max_id_size ) );
				if ( $limit < self::MIN_OBJECT_LIMIT ) {
					if ( $factor > self::OBJECT_LIMIT_FACTOR ) {
						$limit = min( self::MIN_OBJECT_LIMIT, floor( $max_allowed_packet / ( self::MIN_OBJECT_LIMIT * $max_id_size ) ) );
					}
				}
				if ( $wps_object_limit === 'AUTOREPORT' ) {
					wps_log_info( sprintf( 'Object limit established at %d.', $limit ) );
				}

				$cached = wp_cache_set( 'object_limit', $limit, self::CACHE_GROUP, self::SECONDS_PER_DAY );
			}
		} else {
			if ( is_numeric( WPS_OBJECT_LIMIT ) ) {
				$limit = intval( WPS_OBJECT_LIMIT );
				if ( $limit < 0 ) {
					$limit = 0;
				}
			} else {
				$limit = 0;
			}
		}
		return $limit;
	}

	/**
	 * Completely remove and rebuild the index structures and data.
	 */
	public static function rebuild() {
		wps_log_info( 'Rebuilding.' );
		WooCommerce_Product_Search_Worker::stop();
		self::cleanup_index();
		self::setup();
	}

	/**
	 * Create tables and initial data. Starts the index worker.
	 *
	 * @return boolean true if all went well
	 */
	public static function setup() {
		global $wpdb;

		$result = true;

		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$queries = array();

		$key_table = self::get_tablename( 'key' );
		$tables[] = $key_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$key_table'" ) != $key_table ) {
			$queries[] =
				"CREATE TABLE $key_table (
				key_id        BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
				`key`         VARCHAR(" . intval( self::MAX_KEY_LENGTH ) . ") NOT NULL,
				PRIMARY KEY   (key_id),
				INDEX `key`   (`key`(10))
			) $charset_collate;";

		}
		$index_table = self::get_tablename( 'index' );
		$tables[] = $index_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$index_table'" ) != $index_table ) {
			$queries[] =
				"CREATE TABLE $index_table (
				index_id       BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				key_id         BIGINT(20) UNSIGNED NOT NULL,
				object_id      BIGINT(20) UNSIGNED NOT NULL,
				object_type_id BIGINT(20) UNSIGNED NOT NULL,
				count          INT UNSIGNED DEFAULT 1,
				modified       DATETIME DEFAULT NULL,
				PRIMARY KEY    (index_id),
				INDEX          key_id (key_id),
				INDEX          object_id (object_id),
				INDEX          object_type_id (object_type_id)
			) $charset_collate;";
		}
		$object_type_table = self::get_tablename( 'object_type' );
		$tables[] = $object_type_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_type_table'" ) != $object_type_table ) {
			$queries[] =
				"CREATE TABLE $object_type_table (
				object_type_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				object_type    VARCHAR(100) DEFAULT NULL,
				context        VARCHAR(100) DEFAULT NULL,
				context_table  VARCHAR(100) DEFAULT NULL,
				context_column VARCHAR(100) DEFAULT NULL,
				context_key    VARCHAR(100) DEFAULT NULL,
				PRIMARY KEY    (object_type_id),
				INDEX          object_type (object_type(10)),
				INDEX          context (context(10)),
				INDEX          context_table (context_table(10)),
				INDEX          context_column (context_column(10)),
				INDEX          context_key (context_key(10))
			) $charset_collate;";
		}

		$object_term_table = self::get_tablename( 'object_term' );
		$tables[] = $object_term_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_term_table'" ) != $object_term_table ) {
			$queries[] =
				"CREATE TABLE $object_term_table (
				object_term_id   BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				object_id        BIGINT(20) UNSIGNED NOT NULL,
				parent_object_id BIGINT(20) UNSIGNED DEFAULT NULL,
				term_id          BIGINT(20) UNSIGNED NOT NULL,
				parent_term_id   BIGINT(20) UNSIGNED DEFAULT NULL,
				object_type      VARCHAR(100) DEFAULT NULL,
				taxonomy         VARCHAR(100) DEFAULT NULL,
				inherit          TINYINT DEFAULT NULL,
				modified         DATETIME DEFAULT NULL,
				PRIMARY KEY      (object_term_id),
				INDEX            object_id (object_id),
				INDEX            parent_object_id (parent_object_id),
				INDEX            term_id (term_id),
				INDEX            parent_term_id (parent_term_id),
				INDEX            object_type (object_type(10)),
				INDEX            taxonomy (taxonomy(10))
			) $charset_collate;";
		}

		$query_table = self::get_tablename( 'query' );
		$tables[] = $query_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$query_table'" ) != $query_table ) {
			$queries[] =
				"CREATE TABLE $query_table (
				query_id    BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				query       VARCHAR(" . intval( self::MAX_QUERY_LENGTH ) . ") NOT NULL,
				PRIMARY KEY (query_id),
				INDEX       query (query(20))
			) $charset_collate;";
		}

		$uri_table = self::get_tablename( 'uri' );
		$tables[] = $uri_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$uri_table'" ) != $uri_table ) {
			$queries[] =
				"CREATE TABLE $uri_table (
				uri_id      BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				uri         VARCHAR(" . intval( self::MAX_URI_LENGTH ) . ") NOT NULL,
				PRIMARY KEY (uri_id),
				INDEX       uri (uri(64))
			) $charset_collate;";
		}

		$user_agent_table = self::get_tablename( 'user_agent' );
		$tables[] = $user_agent_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$user_agent_table'" ) != $user_agent_table ) {
			$queries[] =
				"CREATE TABLE $user_agent_table (
				user_agent_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_agent    VARCHAR(" . intval( self::MAX_USER_AGENT_LENGTH ) . ") NOT NULL,
				PRIMARY KEY   (user_agent_id),
				INDEX         user_agent (user_agent(32))
			) $charset_collate;";
		}

		$hit_table = self::get_tablename( 'hit' );
		$tables[] = $hit_table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$hit_table'" ) != $hit_table ) {
			$queries[] =
				"CREATE TABLE $hit_table (
				hit_id        BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				date          DATE DEFAULT NULL,
				datetime      DATETIME DEFAULT NULL,
				ip            VARBINARY(16) DEFAULT NULL,
				src_uri_id    BIGINT(20) UNSIGNED DEFAULT NULL,
				dest_uri_id   BIGINT(20) UNSIGNED DEFAULT NULL,
				user_id       BIGINT(20) UNSIGNED DEFAULT NULL,
				query_id      BIGINT(20) UNSIGNED DEFAULT NULL,
				user_agent_id BIGINT(20) UNSIGNED DEFAULT NULL,
				count         INT UNSIGNED DEFAULT 0,
				PRIMARY KEY   (hit_id),
				INDEX         date (date),
				INDEX         datetime (datetime),
				INDEX         ip (ip),
				INDEX         src (src_uri_id),
				INDEX         dest (dest_uri_id),
				INDEX         user (user_id),
				INDEX         query (query_id),
				INDEX         ua (user_agent_id),
				INDEX         count (count)
			) $charset_collate;";
		}

		foreach ( $queries as $query ) {
			if ( $wpdb->query( $query ) === false ) {
				wps_log_error( 'Failed to execute database query: ' . $query );
			}
		}

		foreach( $tables as $table ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
				wps_log_error( sprintf( 'The table %s is missing.', $table ) );
				$result = false;
			}
		}

		$object_types = array(
			array(
				'object_type' => 'product',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => null,
				'context_key' => null
			),
			array(
				'object_type' => 'product',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_id',
				'context_key' => null
			),
			array(
				'object_type' => 'product',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_title',
				'context_key' => null
			),
			array(
				'object_type' => 'product',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_excerpt',
				'context_key' => null
			),
			array(
				'object_type' => 'product',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_content',
				'context_key' => null
			),
			array(
				'object_type' => 'product',
				'context' => 'sku',
				'context_table' => 'postmeta',
				'context_column' => 'meta_key',
				'context_key' => '_sku'
			),
			array(
				'object_type' => 'product',
				'context' => 'tag',
				'context_table' => 'term_taxonomy',
				'context_column' => 'taxonomy',
				'context_key' => 'product_tag'
			),
			array(
				'object_type' => 'product',
				'context' => 'category',
				'context_table' => 'term_taxonomy',
				'context_column' => 'taxonomy',
				'context_key' => 'product_cat'
			),

			array(
				'object_type' => 'product_variation',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => null,
				'context_key' => null
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_id',
				'context_key' => null
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_title',
				'context_key' => null
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_excerpt',
				'context_key' => null
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'product',
				'context_table' => 'posts',
				'context_column' => 'post_content',
				'context_key' => null
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'sku',
				'context_table' => 'postmeta',
				'context_column' => 'meta_key',
				'context_key' => '_sku'
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'tag',
				'context_table' => 'term_taxonomy',
				'context_column' => 'taxonomy',
				'context_key' => 'product_tag'
			),
			array(
				'object_type' => 'product_variation',
				'context' => 'category',
				'context_table' => 'term_taxonomy',
				'context_column' => 'taxonomy',
				'context_key' => 'product_cat'
			)

		);

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_type_table'" ) === $object_type_table ) {
			foreach( $object_types as $object_type ) {
				$fields       = array();
				$placeholders = array();
				$values       = array();
				foreach( $object_type as $field => $value ) {
					if ( $value !== null ) {
						$fields[]       = $field;
						$placeholders[] = '%s';
						$values[]       = $value;
					}
				}
				$conditions = array();
				for ( $i = 0; $i < count( $fields ); $i++ ) {
					$conditions[] = sprintf( '%s = %s', $fields[$i], $placeholders[$i] );
				}
				if ( count( $conditions ) > 0 ) {
					$where = ' WHERE ' . implode( ' AND ', $conditions );
					$object_type_id = $wpdb->get_var( $wpdb->prepare(
						"SELECT object_type_id FROM $object_type_table $where", $values
					) );
					if ( !$object_type_id ) {
						$query = $wpdb->prepare(
							sprintf(
								"INSERT INTO $object_type_table (%s) VALUES (%s)",
								implode( ',', $fields ),
								implode( ',', $placeholders )
							),
							$values
						);
						if ( $wpdb->query( $query ) === false ) {
							$result = false;
							wps_log_error( 'Failed to execute database query: ' . $query );
						}
					}
				}
			}
		}

		if (
			( $wpdb->get_var( "SHOW TABLES LIKE '$key_table'" ) === $key_table ) &&
			( $wpdb->get_var( "SHOW TABLES LIKE '$index_table'" ) === $index_table ) &&
			( $wpdb->get_var( "SHOW TABLES LIKE '$object_type_table'" ) === $object_type_table ) &&
			( $wpdb->get_var( "SHOW TABLES LIKE '$object_term_table'" ) === $object_term_table )
		) {
			WooCommerce_Product_Search_Worker::start();

		}

		$indexer = new WooCommerce_Product_Search_Indexer();
		$indexer->process_term_weights();

		if ( $result ) {
			update_option( 'woocommerce_product_search_db_version', WOO_PS_PLUGIN_VERSION, true );
		}

		self::cleanup_cache();

		return $result;
	}

	/**
	 * Procedures to update from a previous version.
	 */
	public static function update( $previous_version ) {
		global $wpdb;
		if ( version_compare( $previous_version, '2.7.0' ) < 0 ) {
			$object_types = array(

				array(
					'object_type' => 'product_variation',
					'context' => 'product',
					'context_table' => 'posts',
					'context_column' => null,
					'context_key' => null
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'product',
					'context_table' => 'posts',
					'context_column' => 'post_id',
					'context_key' => null
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'product',
					'context_table' => 'posts',
					'context_column' => 'post_title',
					'context_key' => null
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'product',
					'context_table' => 'posts',
					'context_column' => 'post_excerpt',
					'context_key' => null
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'product',
					'context_table' => 'posts',
					'context_column' => 'post_content',
					'context_key' => null
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'sku',
					'context_table' => 'postmeta',
					'context_column' => 'meta_key',
					'context_key' => '_sku'
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'tag',
					'context_table' => 'term_taxonomy',
					'context_column' => 'taxonomy',
					'context_key' => 'product_tag'
				),
				array(
					'object_type' => 'product_variation',
					'context' => 'category',
					'context_table' => 'term_taxonomy',
					'context_column' => 'taxonomy',
					'context_key' => 'product_cat'
				)

			);

			$object_type_table = self::get_tablename( 'object_type' );
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_type_table'" ) === $object_type_table ) {
				foreach( $object_types as $object_type ) {
					$fields       = array();
					$placeholders = array();
					$values       = array();
					foreach( $object_type as $field => $value ) {
						if ( $value !== null ) {
							$fields[]       = $field;
							$placeholders[] = '%s';
							$values[]       = $value;
						}
					}
					$conditions = array();
					for ( $i = 0; $i < count( $fields ); $i++ ) {
						$conditions[] = sprintf( '%s = %s', $fields[$i], $placeholders[$i] );
					}
					if ( count( $conditions ) > 0 ) {
						$where = ' WHERE ' . implode( ' AND ', $conditions );
						$object_type_id = $wpdb->get_var( $wpdb->prepare(
							"SELECT object_type_id FROM $object_type_table $where", $values
						) );
						if ( !$object_type_id ) {
							$query = $wpdb->prepare(
								sprintf(
									"INSERT INTO $object_type_table (%s) VALUES (%s)",
									implode( ',', $fields ),
									implode( ',', $placeholders )
								),
								$values
							);
							if ( $wpdb->query( $query ) === false ) {
								wps_log_error( 'Failed to execute database query: ' . $query );
							}
						}
					}
				}
			}
		}

		if ( !WooCommerce_Product_Search::needs_db_update() ) {
			self::update_db();
		}

		self::cleanup_cache();
	}

	/**
	 * Database update.
	 *
	 * @since 3.0.0
	 *
	 * @return boolean update successful
	 */
	public static function update_db() {

		global $wpdb;

		$success = true;

		self::cleanup_cache();

		$object_term_table = self::get_tablename( 'object_term' );

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_term_table'" ) !== $object_term_table ) {
			$charset_collate = '';
			if ( ! empty( $wpdb->charset ) ) {
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}

			$query =
				"CREATE TABLE $object_term_table (
				object_term_id   BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				object_id        BIGINT(20) UNSIGNED NOT NULL,
				parent_object_id BIGINT(20) UNSIGNED DEFAULT NULL,
				term_id          BIGINT(20) UNSIGNED NOT NULL,
				parent_term_id   BIGINT(20) UNSIGNED DEFAULT NULL,
				object_type      VARCHAR(100) DEFAULT NULL,
				taxonomy         VARCHAR(100) DEFAULT NULL,
				inherit          TINYINT DEFAULT NULL,
				modified         DATETIME DEFAULT NULL,
				PRIMARY KEY      (object_term_id),
				INDEX            object_id (object_id),
				INDEX            parent_object_id (parent_object_id),
				INDEX            term_id (term_id),
				INDEX            parent_term_id (parent_term_id),
				INDEX            object_type (object_type(10)),
				INDEX            taxonomy (taxonomy(10))
			) $charset_collate;";

			if ( $wpdb->query( $query ) === false ) {
				wps_log_error( 'Failed to execute database query: ' . $query );
			}

			delete_option( 'woocommerce_product_search_plugin_tables' );

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_term_table'" ) === $object_term_table ) {

				update_option( 'woocommerce_product_search_db_version', WOO_PS_PLUGIN_VERSION, true );

				$indexer = new WooCommerce_Product_Search_Indexer();
				$indexer->process_term_weights();
				$indexer->preprocess_terms();
				$indexer->process_terms();

				WooCommerce_Product_Search_Worker::stop();
				WooCommerce_Product_Search_Worker::start();

			} else {
				$success = false;
				wps_log_error( sprintf( 'The table %s is missing.', $object_term_table ) );
			}
		}

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$object_term_table'" ) === $object_term_table ) {

			update_option( 'woocommerce_product_search_db_version', WOO_PS_PLUGIN_VERSION, true );
			wps_log_info( 'Database updated.' );
		} else {
			wps_log_error( "Failed to update the database." );
		}

		return $success;
	}

	/**
	 * Reset certain cache entries.
	 *
	 * @since 3.1.0
	 */
	private static function cleanup_cache() {

		if ( function_exists( 'wp_cache_delete' ) ) {
			wp_cache_delete( 'woocommerce-product-search', 'options' );
			wp_cache_delete( 'woocommerce_product_search_db_version', 'options' );
			wp_cache_delete( 'woocommerce_product_search_plugin_tables', 'options' );
			wp_cache_delete( 'woocommerce_product_search_plugin_version', 'options' );
		}
	}

	/**
	 * Remove all tables related to the index.
	 */
	public static function cleanup_index() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'key' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'index' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'object_type' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'object_term' ) );
	}

	/**
	 * Remove all tables related to the stats.
	 */
	public static function cleanup_stats() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'query' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'uri' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'user_agent' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'hit' ) );
	}

	/**
	 * Cleanup process - will remove ALL tables and data if $drop is true.
	 *
	 * @param boolean $drop whether to delete data and tables
	 */
	public static function cleanup( $drop = false ) {
		if ( $drop ) {
			self::cleanup_index();
			self::cleanup_stats();
		}
	}

}
