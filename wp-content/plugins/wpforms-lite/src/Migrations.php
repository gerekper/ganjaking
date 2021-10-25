<?php

namespace WPForms;

/**
 * Class Migrations handles both Lite and Pro plugin upgrade routines.
 *
 * @since 1.5.9
 */
class Migrations {

	/**
	 * WP option name to store the migration version.
	 *
	 * @since 1.5.9
	 */
	const OPTION_NAME = 'wpforms_version_lite';

	/**
	 * Have we migrated?
	 *
	 * @since 1.5.9
	 *
	 * @var bool
	 */
	private $is_migrated = false;

	/**
	 * Class init.
	 *
	 * @since 1.5.9
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * General hooks.
	 *
	 * @since 1.5.9
	 */
	private function hooks() {

		add_action( 'wpforms_loaded', array( $this, 'maybe_migrate' ), -9999 );
		add_action( 'wpforms_loaded', array( $this, 'update_version' ), -9998 );
	}

	/**
	 * Run the migration if needed.
	 *
	 * @since 1.5.9
	 */
	public function maybe_migrate() {

		if ( ! is_admin() ) {
			return;
		}

		// Retrieve the last known version.
		$version = get_option( self::OPTION_NAME );

		if ( empty( $version ) ) {
			$version = '0.0.1';
		}

		$this->migrate( $version );
	}

	/**
	 * Run the migrations for a specific version.
	 *
	 * @since 1.5.9
	 *
	 * @param string $version Version to run the migrations for.
	 */
	private function migrate( $version ) {

		if ( version_compare( $version, '1.5.9', '<' ) ) {
			$this->v159_upgrade();
		}
	}

	/**
	 * If upgrade has occurred, update version options in database.
	 *
	 * @since 1.5.9
	 */
	public function update_version() {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! $this->is_migrated ) {
			return;
		}

		update_option( self::OPTION_NAME, WPFORMS_VERSION );
	}

	/**
	 * Do all the required migrations for WPForms v1.5.9.
	 *
	 * @since 1.5.9
	 */
	private function v159_upgrade() {

		$meta = wpforms()->get( 'tasks_meta' );

		// Create the table if it doesn't exist.
		if ( $meta && ! $meta->table_exists() ) {
			$meta->create_table();
		}

		$this->is_migrated = true;
	}
}
