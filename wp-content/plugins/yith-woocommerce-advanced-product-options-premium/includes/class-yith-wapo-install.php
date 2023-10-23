<?php
/**
 * WAPO Install Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Install' ) ) {

	/**
	 *  YITH_WAPO Install Class
	 */
	class YITH_WAPO_Install {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO_Instance
		 */
		public static $instance;

		/**
		 * The updates to fire.
		 *
		 * @var callable[][]
		 */
		private $db_updates = array(
			'3.0.0' => array(
				'yith_wapo_update_300_migrate_db',
			),
			'3.2.0' => array(
				'yith_wapo_update_320_migrate_conditional_logic',
			),
			'4.0.0' => array(
				'yith_wapo_update_400_migrate_db',
			),
		);

		/**
		 * Callbacks to be fired soon, instead of being scheduled.
		 *
		 * @var callable[]
		 */
		private $soon_callbacks = array();

		/**
		 * The version option.
		 */
		const VERSION_OPTION = 'yith_wapo_version_option';

		/**
		 * The version option.
		 */
		const DB_VERSION_OPTION = 'yith_wapo_db_version_option';

		/**
		 * The update scheduled option.
		 */
		const DB_UPDATE_SCHEDULED_OPTION = 'yith_wapo_db_update_scheduled_for';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO_Instance
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'check_version' ), 5 );
			add_action( 'yith_wapo_run_update_callback', array( $this, 'run_update_callback' ), 10, 2 );
		}



		/**
		 * Check the plugin version and run the updater is required.
		 * This check is done on all requests and runs if the versions do not match.
		 */
		public function check_version() {

			$panel_version = ! get_option( 'yith_wapo_v2' ) || 'no' === get_option( 'yith_wapo_v2' ) ? 'old_panel' : 'new_panel';

			$current_db_version = get_option( self::DB_VERSION_OPTION, null );

			// Check the option to see if he is in the first version (old panel) or second version (new panel).
			if ( $this->needs_db_update() && 'old_panel' === $panel_version && ! $current_db_version ) {
				$this->install();
				update_option( self::DB_VERSION_OPTION, YITH_WAPO_DB_VERSION );
			}

			if ( get_option( 'yith_wapo_remove_del_column', 0 ) == 0 ) {
				$this->remove_del_column_from_db();
			}

			if ( $this->needs_db_update() ) { // Update for version 2.0 and more than 3.0 version.
				$this->update();

				update_option( self::DB_VERSION_OPTION, YITH_WAPO_DB_VERSION );
			}

			// Migration for version 3.2.0 conditional logic.
		}

		/**
		 * Get list of DB update callbacks.
		 *
		 * @return array
		 */
		public function get_db_update_callbacks() {
			return $this->db_updates;
		}

		/**
		 * Return true if the callback needs to be fired soon, instead of being scheduled.
		 *
		 * @param string $callback The callback name.
		 *
		 * @return bool
		 */
		private function is_soon_callback( $callback ) {
			return in_array( $callback, $this->soon_callbacks, true );
		}

		/**
		 * The DB needs to be updated?
		 *
		 * @return bool
		 */
		public function needs_db_update() {
			$current_db_version = get_option( self::DB_VERSION_OPTION, null );

			if ( is_null( $current_db_version ) ) {
				return true;
			} elseif ( version_compare( $current_db_version, YITH_WAPO_DB_VERSION, '<' ) ) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Start the migration
		 */
		public function install() {

			// Check if we are not already running this routine.
			if ( 'yes' === get_transient( 'yith_wapo_migrating_from_v1_to_V2' ) ) {
				return;
			}

			set_transient( 'yith_wapo_migrating_from_v1_to_V2', 'yes', MINUTE_IN_SECONDS * 10 );

			if ( ! defined( 'YITH_WAPO_UPDATING' ) ) {
				define( 'YITH_WAPO_UPDATING', true );
			}

			$this->create_tables(); // Tenemos que borrar las tablas de la v2 si existen, y si no crearlas.

			$this->maybe_update_db_version();

			delete_transient( 'yith_wapo_migrating_from_v1_to_V2' );

			do_action( 'yith_wapo_migrated' );
		}

		/**
		 * Maybe update db
		 */
		private function maybe_update_db_version() {
			if ( $this->needs_db_update() ) {
				$this->update();
			}
		}

		/**
		 * DB Check
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function create_tables() {

			global $wpdb;

			$groups_table_name = $wpdb->prefix . 'yith_wapo_groups';
			$types_table_name  = $wpdb->prefix . 'yith_wapo_types';

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$groups_table_name'" ) === $groups_table_name ) { //phpcs:ignore

				// we create the imported column if not already created in the old groups table.
				$row_group = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$wpdb->prefix}yith_wapo_groups' AND column_name = 'imported'" ); //phpcs:ignore

				if ( empty( $row_group ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}yith_wapo_groups ADD imported INT(0) NOT NULL DEFAULT 0" ); //phpcs:ignore
				}
			}

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$types_table_name'" ) === $types_table_name ) { //phpcs:ignore

				// we create the imported column if not already created in the old addons table.
				$row_addons = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$wpdb->prefix}yith_wapo_types' AND column_name = 'imported'" ); //phpcs:ignore

				if ( empty( $row_addons ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}yith_wapo_types ADD imported INT(0) NOT NULL DEFAULT 0" ); //phpcs:ignore
				}
			}

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			$wpdb->hide_errors();
			$wpdb->suppress_errors( true );
			$wpdb->show_errors( false );

			$charset_collate = $wpdb->get_charset_collate();

				$blocks_table_name = $wpdb->prefix . 'yith_wapo_blocks';

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$blocks_table_name'" ) === $blocks_table_name ) {

				// if table exist, create a backup and DROP it.
				if ( ! isset( $_REQUEST['wapo_action'] ) ) { // Avoid to execute the copy of addons if it's executed from DEBUG Panel.
					$wpdb->query( "CREATE TABLE {$wpdb->prefix}yith_wapo_blocks_backup LIKE {$wpdb->prefix}yith_wapo_blocks" );
					$wpdb->query( "INSERT INTO {$wpdb->prefix}yith_wapo_blocks_backup SELECT * FROM {$wpdb->prefix}yith_wapo_blocks" );
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}yith_wapo_blocks_backup DROP COLUMN IF EXISTS deleted" );
				}
				$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}yith_wapo_blocks" );

			}

				$addons_table_name = $wpdb->prefix . 'yith_wapo_addons';

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$addons_table_name'" ) === $addons_table_name ) {

				// if table exist, create a backup and DROP it.
				if ( ! isset( $_REQUEST['wapo_action'] ) ) { // Avoid to execute the copy of addons if it's executed from DEBUG Panel
					$wpdb->query( "CREATE TABLE {$wpdb->prefix}yith_wapo_addons_backup LIKE {$wpdb->prefix}yith_wapo_addons" );
					$wpdb->query( "INSERT INTO {$wpdb->prefix}yith_wapo_addons_backup SELECT * FROM {$wpdb->prefix}yith_wapo_addons" );
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}yith_wapo_addons_backup DROP COLUMN IF EXISTS deleted" );
				}
				$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}yith_wapo_addons" );

			}

			$sql_blocks = "CREATE TABLE {$wpdb->prefix}yith_wapo_blocks (
						id					INT(3) NOT NULL AUTO_INCREMENT,
						user_id				BIGINT(20),
						vendor_id			BIGINT(20),
						settings			LONGTEXT,
						priority			DECIMAL(9,5),
						visibility			INT(1),
						creation_date		TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						last_update			TIMESTAMP,
						name                varchar(255) NOT NULL,
                        product_association varchar(255),
                        exclude_products    tinyint(1) NOT NULL,
				        user_association    varchar(255),
				        exclude_users       tinyint(1) NOT NULL,
						PRIMARY KEY (id)
					) $charset_collate;";

			$sql_addons = "CREATE TABLE {$wpdb->prefix}yith_wapo_addons (
						id					INT(4) NOT NULL AUTO_INCREMENT,
						block_id			INT(3),
						settings			LONGTEXT,
						options				LONGTEXT,
						priority			DECIMAL(9,5),
						visibility			INT(1),
						creation_date		TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						last_update			TIMESTAMP,
						PRIMARY KEY (id)
					) $charset_collate;";

            $sql_associations = "CREATE TABLE {$wpdb->prefix}yith_wapo_blocks_assoc (
						rule_id bigint(20) NOT NULL,
                        object varchar(255) NOT NULL,
                        type varchar(50) NOT NULL,
                        KEY `type` (`type`),
                        KEY `object` (`object`)
					) $charset_collate;";

			dbDelta( $sql_blocks );
			dbDelta( $sql_addons );
			dbDelta( $sql_associations );
		}


		/**
		 * Push all needed DB updates to the queue for processing.
		 */
		private function update() {

			$current_db_version   = get_option( self::DB_VERSION_OPTION );
			$loop                 = 0;
			$is_already_scheduled = get_option( self::DB_UPDATE_SCHEDULED_OPTION, '' ) === YITH_WAPO_DB_VERSION;

			$args = array();

			if ( ! $is_already_scheduled ) {

				foreach ( $this->get_db_update_callbacks() as $version => $update_callbacks ) {
					if ( version_compare( $current_db_version, $version, '<' ) ) {
						foreach ( $update_callbacks as $update_callback ) {
							if ( $this->is_soon_callback( $update_callback ) ) {
								$this->run_update_callback( $update_callback, $args );
							} else {
								WC()->queue()->schedule_single(
									time() + $loop,
									'yith_wapo_run_update_callback',
									array(
										'update_callback' => $update_callback,
										'args' => $args,
									),
									'yith-wapo-db-updates'
								);
								$loop ++;
							}
						}
					}
				}
				update_option( self::DB_UPDATE_SCHEDULED_OPTION, YITH_WAPO_DB_VERSION );
			}
		}

		/**
		 * Run an update callback when triggered by ActionScheduler.
		 *
		 * @param string $callback Callback name.
		 */
		public function run_update_callback( $callback, $args = array() ) {
			include_once YITH_WAPO_INCLUDES_PATH . '/functions.yith-wapo-update.php';

			if ( is_callable( $callback ) ) {
				self::run_update_callback_start( $callback );
				$result = (bool) call_user_func( $callback, $args );
				self::run_update_callback_end( $callback, $result, $args );
			}
		}

		/**
		 * Triggered when a callback will run.
		 *
		 * @param string $callback Callback name.
		 */
		protected function run_update_callback_start( $callback ) {
			if ( ! defined( 'YITH_WAPO_UPDATING' ) ) {
				define( 'YITH_WAPO_UPDATING', true );
			}
		}

		/**
		 * Triggered when a callback has ran.
		 *
		 * @param string $callback Callback name.
		 * @param bool   $result   Return value from callback. Non-false need to run again.
		 */
		protected function run_update_callback_end( $callback, $result, $args ) {
			if ( $result ) {

				if ( 'yith_wapo_update_400_migrate_db' === $callback ) {
					$offset_sum     = apply_filters( 'yith_wapo_aux_db_migration_offset_sum', 30 );
					$current_offset = isset( $args['offset'] ) ? $args['offset'] : 0;
					$args['offset'] = $current_offset + $offset_sum;
				}

				WC()->queue()->add(
					'yith_wapo_run_update_callback',
					array(
						'update_callback' => $callback,
						'args' => $args,
					),
					'yith-wapo-db-updates'
				);
			}
		}

		/**
		 * DB Check
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function remove_del_column_from_db() {

			global $wpdb;

			$blocks_table_name = $wpdb->prefix . 'yith_wapo_blocks';
			$column_name       = 'deleted';

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$blocks_table_name'" ) === $blocks_table_name ) {

				$check_deleted_column = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ',
						DB_NAME,
						$blocks_table_name,
						$column_name
					)
				);

				if ( ! empty( $check_deleted_column ) ) {

					$wpdb->delete( $blocks_table_name, array( 'deleted' => 1 ) );
					$wpdb->query( "ALTER TABLE {$blocks_table_name} DROP COLUMN IF EXISTS deleted" );
				}
			}

			$addons_table_name = $wpdb->prefix . 'yith_wapo_addons';

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$addons_table_name'" ) === $addons_table_name ) {

				$check_deleted_column = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ',
						DB_NAME,
						$addons_table_name,
						$column_name
					)
				);

				if ( ! empty( $check_deleted_column ) ) {

					$wpdb->delete( $addons_table_name, array( 'deleted' => 1 ) );
					$wpdb->query( "ALTER TABLE {$addons_table_name} DROP COLUMN IF EXISTS deleted" );
				}
			}

			update_option( 'yith_wapo_remove_del_column', 1 );

		}



	}
}




/**
 * Unique access to instance of YITH_WAPO_Install class
 *
 * @return YITH_WAPO_Install
 */
function YITH_WAPO_Install() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Install::get_instance();
}
