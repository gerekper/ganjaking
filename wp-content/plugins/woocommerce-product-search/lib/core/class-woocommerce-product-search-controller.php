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
			( $wpdb->get_var( "SHOW TABLES LIKE '$object_type_table'" ) === $object_type_table )
		) {
			WooCommerce_Product_Search_Worker::start();
		}

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
	}

	/**
	 * Remove all tables related to the index.
	 */
	public static function cleanup_index() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'key' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'index' ) );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::get_tablename( 'object_type' ) );
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
