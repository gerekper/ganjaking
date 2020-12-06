<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Main migration class
 */
class GM_Migration {

	/**
	 * Main migration list
	 *
	 * @var array DB updates and options that need to be run per version
	 */
	private static $migrate_version_points = array(
		// '2.1.0' => [ 'type' => 'ask' ],
	);

	/**
	 * Identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $identifier = 'gm_migrate_job';

	/**
	 * Cron_hook_identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $cron_hook_identifier;

	/**
	 * Cron_interval_identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $cron_interval_identifier;

	public $db_version     = '';
	public $migration_type = 'ask';

	public $sucsess_versions = array();


	public function __construct() {

		$this->cron_hook_identifier     = $this->identifier . '_cron';
		$this->cron_interval_identifier = $this->identifier . '_cron_interval';

		// If crane theme don't have "DB version" option
		if ( ! get_option( GROOVY_MENU_DB_VER_OPTION ) ) {
			update_option( GROOVY_MENU_DB_VER_OPTION, GROOVY_MENU_VERSION );

			return null;
		}

		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			//call our function when initiated from JavaScript
			add_action( 'wp_ajax_gm_ajax_start_migrate', array( $this, 'gm_ajax_start_migrate' ) );

			add_action( 'wp_ajax_gm_dismissed_migration_notice_info', array(
				$this,
				'dismissed_migration_notice_info'
			) );

		}

		if ( $this->get_cron_job_marker() ) {
			add_filter( 'cron_schedules', array( $this, 'schedule_cron_add_interval' ) );
			add_action( $this->cron_hook_identifier, array( $this, 'cron_migrate_job' ) );
			add_action( 'init', array( $this, 'schedule_event' ), 100 );
		}

		add_action( 'init', array( $this, 'migrate_plugin' ) );
		add_action( 'admin_init', array( $this, 'migrate_debug' ) );

	}

	/**
	 * Start migrate debug info page
	 */
	public function migrate_debug() {
		// Load debug data.
		if ( class_exists( '\GroovyMenu\DebugPage' ) ) {
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'migrate_debug.php';
			new GM_MigrationDebug( self::$migrate_version_points, $this->identifier );
		}
	}

	/**
	 * Add log migrate data
	 */
	public function add_migrate_debug_log( $log_data = array() ) {
		$migration_log = get_option( GROOVY_MENU_DB_VER_OPTION . '__log_' . $this->db_version );
		if ( empty( $migration_log ) || ! is_array( $migration_log ) ) {
			$migration_log = array();
		}

		if ( is_string( $log_data ) ) {
			$log_data = array( $log_data );
		}

		$migration_log[] = array_merge( array( date( 'Y-m-d H:i:s' ) ), $log_data );
		update_option( GROOVY_MENU_DB_VER_OPTION . '__log_' . $this->db_version, $migration_log, false );
	}

	/**
	 * Called via AJAX. Start migration process
	 */
	public function gm_ajax_start_migrate() {

		if ( ! $this->get_next_queue() ) {
			$this->set_cron_job_marker( false );
			wp_die( wp_json_encode( array(
				'code'    => 'none',
				'message' => esc_html__( 'You already have the latest version. No update required.', 'groovy-menu' )
			) ) );
		}

		$this->update_dismissed_info( false );

		$this->set_cron_job_marker( true );

		$output = array(
			'message' => '<p><strong>' .
			             esc_html__( 'Groovy menu data update:', 'groovy-menu' ) . '</strong> ' .
			             esc_html__( 'Updating start in the background job.', 'groovy-menu' ) .
			             '</p>',
			'code'    => 'background'
		);
		wp_die( wp_json_encode( $output ) );
	}

	/**
	 * @param boolean $flag
	 */
	public function set_cron_job_marker( $flag ) {
		$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
		if ( ! is_array( $migration_report ) || empty( $migration_report ) ) {
			$migration_report = array( 'cron_job' => $flag );
		} else {
			$migration_report['cron_job'] = $flag;
		}

		update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );
	}

	/**
	 * @return bool
	 */
	public function get_cron_job_marker() {
		$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );

		return isset( $migration_report['cron_job'] ) ? $migration_report['cron_job'] : false;
	}

	/**
	 * Restart the background process if not already running
	 */
	public function cron_migrate_job() {

		if ( $this->is_process_running() ) {
			// Background process already running.
			exit;
		}

		if ( ! $this->get_next_queue() ) {
			// No data to process.
			$this->clear_scheduled_event();
			exit;
		}

		$this->do_migrate_process();

		exit;
	}


	protected function do_migrate_process() {
		$this->lock_process();

		$next_version    = $this->get_next_queue();
		$version_points  = self::$migrate_version_points;
		$migrate_options = isset( $version_points[ $next_version ] ) ? $version_points[ $next_version ] : null;

		if ( isset( $migrate_options['type'] ) && 'ask' === $migrate_options['type'] ) {
			// Callback function must return true on success
			$migration_proccess = $this->start( $next_version );
		}

		$this->unlock_process();

		if ( ! $this->get_next_queue() ) {
			$this->complete();
		}
	}


	/**
	 * Complete.
	 */
	protected function complete() {
		// Unschedule.
		$this->clear_scheduled_event();
	}


	/**
	 * Check jobs and return next migrate job
	 */
	protected function get_next_queue() {

		$db_version = get_option( GROOVY_MENU_DB_VER_OPTION );
		$db_report  = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );

		foreach ( self::$migrate_version_points as $version => $migrate_options ) {
			if ( version_compare( $version, $db_version, '>' ) && empty( $db_report[ $version ] ) ) {
				return $version;
			}
		}

		return false;
	}


	/**
	 * Schedule fallback event.
	 */
	public function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) && $this->get_next_queue() ) {
			wp_schedule_event( time(), $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}


	/**
	 * Schedule cron new interval
	 *
	 * @access public
	 *
	 * @param mixed $schedules Schedules.
	 *
	 * @return mixed
	 */
	public function schedule_cron_add_interval( $schedules ) {
		$interval = apply_filters( $this->cron_interval_identifier, 1 );

		// Adds every 1 minute to the existing schedules.
		$schedules[ $this->cron_interval_identifier ] = array(
			'interval' => MINUTE_IN_SECONDS * $interval,
			'display'  => sprintf( esc_html__( 'Every %d minute', 'groovy-menu' ), $interval ),
		);

		return $schedules;
	}


	/**
	 * Lock process
	 *
	 * Lock the process so that multiple instances can't run simultaneously.
	 * Override if applicable, but the duration should be greater than that
	 * defined in the time_exceeded() method.
	 */
	protected function lock_process() {

		$lock_timer = 60; // 1 min
		$lock_timer = apply_filters( $this->identifier . '_queue_lock_time', $lock_timer );

		set_site_transient( $this->identifier . '_process_lock', time(), $lock_timer );
	}

	/**
	 * Unlock process
	 *
	 * Unlock the process so that other instances can spawn.
	 *
	 * @return $this
	 */
	protected function unlock_process() {
		delete_site_transient( $this->identifier . '_process_lock' );

		return $this;
	}

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	protected function is_process_running() {
		if ( get_site_transient( $this->identifier . '_process_lock' ) ) {
			// Process already running.
			return true;
		}

		return false;
	}


	/**
	 * Clear scheduled event
	 */
	protected function clear_scheduled_event() {
		$timestamp = wp_next_scheduled( $this->cron_hook_identifier );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $this->cron_hook_identifier );
		}

		$this->set_cron_job_marker( false );
		$this->unlock_process();
		$this->update_dismissed_info( false );
	}


	public function migrate_plugin() {

		if ( function_exists( 'wp_doing_ajax' ) && ! wp_doing_ajax() && version_compare( GROOVY_MENU_VERSION, get_option( GROOVY_MENU_DB_VER_OPTION ), '>' ) && get_option( GROOVY_MENU_DB_VER_OPTION ) && ! defined( 'GM_DOING_UPGRADE_JOB' ) ) {

			$need_notice        = false;
			$migration_proccess = false;

			foreach ( self::$migrate_version_points as $version => $migrate_options ) {

				if ( version_compare( get_option( GROOVY_MENU_DB_VER_OPTION ), $version, '<' ) ) {

					switch ( $migrate_options['type'] ) {
						case 'now':

							$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
							if ( ! is_array( $migration_report ) || empty( $migration_report[ $version ] ) || ! $migration_report[ $version ] ) {
								$migration_now = $this->start( $version, $migrate_options['type'] );
							}
							break;

						case 'ask':
							$need_notice = true;
							break;

					}

				}

			}

			if ( $migration_proccess ) {
				$this->show_success_notice();
			}

			if ( $need_notice && $this->get_next_queue() && ( ! defined( 'GM_DOING_UPGRADE_JOB' ) || ! GM_DOING_UPGRADE_JOB ) ) {
				$this->show_notice();
			}

		}

	}

	/**
	 * @param string $version
	 *
	 * @param string $type
	 *
	 * @return mixed null|boolean
	 */
	public function start( $version, $type = 'ask' ) {
		if ( empty( $version ) || ! is_string( $version ) ) {
			return null;
		}

		$this->migration_type = $type;
		$this->db_version     = $version;
		$version_str          = str_replace( '.', '_', $this->db_version );

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			$file_path = $path = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, ABSPATH . '/wp-admin/includes/file.php' );
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				WP_Filesystem();
			}
		}
		if ( empty( $wp_filesystem ) ) {
			return null;
		}

		$migration_file = __DIR__ . DIRECTORY_SEPARATOR . 'migrate__v' . $version_str . '.php';
		// check for existence.
		if ( $wp_filesystem->exists( $migration_file ) ) {
			include_once $migration_file;
			$class_name = 'GM_migrate__v' . $version_str;
			if ( ! class_exists( $class_name ) ) {
				return null;
			}

			$this->add_migrate_debug_log( sprintf( esc_html__( 'Start migration DB version: %s', 'groovy-menu' ), $version ) );

			$instance = new $class_name();

			$this->custom_iniset();

			if ( ! defined( 'GM_DOING_UPGRADE_JOB' ) ) {
				define( 'GM_DOING_UPGRADE_JOB', true );
			}

			$result = $instance->migrate();
			if ( $result ) {
				$this->sucsess_versions[] = $this->db_version;
			}

			return $result;
		}

		return null;
	}

	/**
	 * Set max PHP server params for migrate process
	 */
	public function custom_iniset() {
		set_time_limit( 1800 ); // 30 min
	}


	public function show_notice() {

		if ( $this->is_process_running() || $this->get_cron_job_marker() ) {

			if ( ! $this->get_dismissed_info() ) {
				add_action( 'admin_notices', [ $this, 'process_notice' ], 50, 1 );
			}

		} else {
			add_action( 'admin_notices', [ $this, 'needed_notice' ], 50 );
		}

	}


	public function needed_notice() {
		$output_escaped = '<div class="notice notice-warning gm-theme-migrate__notice-wrapper"><p><strong>' .
		                  esc_html__( 'Groovy menu data update:', 'groovy-menu' ) . '</strong> ' .
		                  esc_html__( 'We need to update your plugin database to the latest version of template.', 'groovy-menu' ) .
		                  '<br>' .
		                  '<button class="button gm-theme-migrate__button">' . esc_html__( 'Update groovy-menu DB Data', 'groovy-menu' ) . '</button>' .
		                  '</p></div>';

		echo $output_escaped;

	}


	public function process_notice() {
		$output_escaped = '<div class="notice notice-info is-dismissible gm-theme-migrate__notice-info"><p><strong>' .
		                  esc_html__( 'Groovy menu data update:', 'groovy-menu' ) . '</strong> ' .
		                  esc_html__( 'Updating still in the background job.', 'groovy-menu' ) .
		                  '</p></div>';


		echo $output_escaped;
	}

	public function success() {

		if ( 'now' === $this->migration_type ) {
			$this->migration_type = 'ask';
			$this->add_migrate_debug_log( sprintf( esc_html__( 'Automatic background migration: compleate. Version: %s', 'groovy-menu' ), $this->db_version ) );

		}

		$this->update_db_version( $this->db_version );
		$this->do_migrate_process();

	}

	public function show_success_notice() {
		add_action( 'admin_notices', [ $this, 'success_notice' ], 50, 1 );
	}


	/**
	 * AJAX handler to store the state of dismissible notices.
	 */
	function dismissed_migration_notice_info() {
		$this->update_dismissed_info( true );
	}

	/**
	 * @param bool $flag
	 */
	public function update_dismissed_info( $flag ) {

		if ( ! is_bool( $flag ) ) {
			return;
		}

		$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
		if ( empty( $migration_report ) || ! is_array( $migration_report ) ) {
			$migration_report = array();
		}
		$migration_report['dismissed_info'] = $flag;

		update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );

	}

	/**
	 * @return bool|mixed
	 */
	public function get_dismissed_info() {

		$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
		if ( empty( $migration_report ) || ! is_array( $migration_report ) || ! isset( $migration_report['dismissed_info'] ) ) {
			return false;
		}

		return $migration_report['dismissed_info'];

	}

	public function success_notice() {
		$output_escaped = '<div class="notice notice-success"><h4><strong>' .
		                  esc_html__( 'Groovy menu data update:', 'groovy-menu' ) . '</strong> ' .
		                  '</h4><p>' .
		                  sprintf( esc_html__( 'Update DB version %s compleate.', 'groovy-menu' ), implode( ' &amp; ', $this->sucsess_versions ) ) .
		                  '</p></div>';

		echo $output_escaped;

	}

	/**
	 * @param string $version migrate DB version
	 */
	public function update_db_version( $version = '' ) {
		$version = $version ? : $this->db_version;

		if ( $version ) {

			$this->update_db_version__report( $version );

			update_option( GROOVY_MENU_DB_VER_OPTION, $version );

			$this->add_migrate_debug_log( sprintf( esc_html__( 'New DB version: %s', 'groovy-menu' ), $version ) );

		} else {
			$this->add_migrate_debug_log( esc_html__( 'ERROR. Migration complete, but DB version is not set!', 'groovy-menu' ) );
		}
	}

	/**
	 * @param string $version migrate DB version
	 */
	public function update_db_version__report( $version ) {

		if ( empty( $version ) ) {
			return;
		}

		$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
		if ( empty( $migration_report ) || ! is_array( $migration_report ) ) {
			$migration_report = array();
		}
		$migration_report[ $version ] = 'done';

		update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );

	}


} // GM_Migration

new GM_Migration();


/**
 * Force update theme DB version. !!!  Be careful
 */
function gm_force_update_db_version() {
	if ( ! defined( 'WP_DEBUG' ) && ! WP_DEBUG ) {
		return;
	}

	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	if ( ! empty( $_GET['gm-groovy-menu-db-version'] ) && ! empty( $_GET['gm-force-update'] ) ) { // @codingStandardsIgnoreLine
		$migration = new GM_Migration();
		$version   = esc_attr( wp_unslash( $_GET['gm-groovy-menu-db-version'] ) ); // @codingStandardsIgnoreLine
		$migration->update_db_version( $version );

		gm_debug_message( sprintf( esc_html__( 'FORCE WRITE. New DB version: %s', 'groovy-menu' ), $version ) );

		if ( ! empty( $_GET['gm-groovy-menu-clear-reports'] ) ) {
			update_option( GROOVY_MENU_DB_VER_OPTION . '__report', array() );
			gm_debug_message( esc_html__( 'FORCE WRITE. Clear all version reports', 'groovy-menu' ) );
		}

		$redirect_to = network_admin_url( 'admin.php?page=groovy_menu_settings' );

		wp_safe_redirect( $redirect_to );
		exit();

	}

	if ( ! empty( $_GET['gm-update-version-data'] ) ) { // @codingStandardsIgnoreLine

		var_dump( get_option( GROOVY_MENU_DB_VER_OPTION ) );

		var_dump( get_option( GROOVY_MENU_DB_VER_OPTION . '__report' ) );

		exit();

	}

}

add_action( 'admin_init', 'gm_force_update_db_version' );
