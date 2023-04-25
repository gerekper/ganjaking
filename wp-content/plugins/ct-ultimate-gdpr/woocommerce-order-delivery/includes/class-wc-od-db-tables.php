<?php
/**
 * Handles the plugin database tables.
 *
 * @package WC_OD
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for handling the plugin database tables.
 */
class WC_OD_DB_Tables {

	/**
	 * Set up the database tables.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );
	}

	/**
	 * Gets the schema for the database tables.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = ( $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '' );

		return "
CREATE TABLE {$wpdb->prefix}wc_od_time_frames (
  time_frame_id BIGINT UNSIGNED NOT NULL auto_increment,
  time_frame_title TEXT NOT NULL,
  time_frame_from varchar(20) NOT NULL DEFAULT '',
  time_frame_to varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY  (time_frame_id)
) $collate;
CREATE TABLE {$wpdb->prefix}wc_od_time_framemeta (
  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
  time_frame_id BIGINT UNSIGNED NOT NULL,
  meta_key varchar(255) default NULL,
  meta_value longtext NULL,
  PRIMARY KEY  (meta_id),
  KEY time_frame_id (time_frame_id),
  KEY meta_key (meta_key(32))
) $collate;
		";
	}

	/**
	 * Registers the database tables.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function register_tables() {
		global $wpdb;

		// List of custom tables without prefixes.
		$tables = array(
			'time_frames'    => 'wc_od_time_frames',
			'time_framemeta' => 'wc_od_time_framemeta',
		);

		foreach ( $tables as $name => $table ) {
			$wpdb->$name    = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
	}

	/**
	 * Gets a list of the database tables.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @return array
	 */
	public static function get_tables() {
		global $wpdb;

		return array(
			"{$wpdb->prefix}wc_od_time_frames",
			"{$wpdb->prefix}wc_od_time_framemeta",
		);
	}

	/**
	 * Drops the tables from the database.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = self::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}
}
