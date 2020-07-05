<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GM_MigrationDebug' ) ) {

	/**
	 * Class GM_MigrationDebug
	 */
	class GM_MigrationDebug {

		/**
		 * Main migration list
		 *
		 * @var array DB updates and options that need to be run per version
		 */
		protected $migrate_version_points = array();

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


		/**
		 * $migration_report
		 *
		 * @var mixed
		 * @access protected
		 */
		protected $migration_report = array();


		public function __construct( $migrate_version_points = array(), $identifier = '' ) {

			$this->migrate_version_points   = $migrate_version_points;
			$this->identifier               = $identifier;
			$this->cron_hook_identifier     = $this->identifier . '_cron';
			$this->cron_interval_identifier = $this->identifier . '_cron_interval';
			$migration_report               = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
			if ( ! empty( $migration_report ) && is_array( $migration_report ) ) {
				$this->migration_report = $migration_report;
			}

			if ( class_exists( '\GroovyMenu\DebugPage' ) ) {
				add_action( 'gm_inside_debug_page_section', array( $this, 'infoCurrentStatus' ), 20 );
				add_action( 'gm_inside_debug_page_section', array( $this, 'infoAllMigrationsStatus' ), 30 );

				if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() && is_admin() && current_user_can( 'install_plugins' ) ) {
					// call our function when initiated from JavaScript.
					add_action( 'wp_ajax_gm_migrate_log', array(
						$this,
						'gm_migrate_log',
					) );

					add_action( 'wp_ajax_gm_switch_migrate_cron_job', array(
						$this,
						'gm_switch_migrate_cron_job',
					) );

					add_action( 'wp_ajax_gm_switch_migrate_dismissed_info', array(
						$this,
						'gm_switch_migrate_dismissed_info',
					) );

					add_action( 'wp_ajax_gm_remove_migrate_db_version', array(
						$this,
						'gm_remove_migrate_db_version',
					) );

					add_action( 'wp_ajax_gm_switch_migrate_db_version', array(
						$this,
						'gm_switch_migrate_db_version',
					) );
				}
			}

		}

		public function gm_migrate_log() {

			$message     = '';
			$nonce_check = true;

			if (
				! isset( $_REQUEST['gm_nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['gm_nonce'] ) ), 'groovymenu_migrate_log' )
			) {
				$message     = 'Security check error. Try refresh that page, please.';
				$nonce_check = false;
			}

			if ( $nonce_check && isset( $_POST['version'] ) ) {
				$version = esc_attr( sanitize_text_field( wp_unslash( $_POST['version'] ) ) );

				$migration_log = get_option( GROOVY_MENU_DB_VER_OPTION . '__log_' . $version );

				if ( ! empty( $migration_log ) && is_array( $migration_log ) ) {
					$message = '';
					foreach ( $migration_log as $index => $data ) {
						$message .= '<div class="gm-debug-log-item">';
						$message .= '<span class="num">#' . esc_attr( $index ) . '</span>';
						$message .= implode( ' ; ', $data );
						$message .= '</div>';
					}
				}
			}

			if ( empty( $message ) ) {
				$message = 'Log empty';
			}

			$output = array( 'message' => $message );
			wp_die( wp_json_encode( $output ) );
		}

		/**
		 * Remove migration flag version
		 */
		public function gm_remove_migrate_db_version() {
			$migration_report = $this->migration_report;

			$message     = '';
			$nonce_check = true;

			if (
				! isset( $_REQUEST['gm_nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['gm_nonce'] ) ), 'groovymenu_migrate_log' )
			) {
				$message     = 'Security check error. Try refresh that page, please.';
				$nonce_check = false;
			}

			if ( $nonce_check && isset( $_POST['version'] ) ) {
				$version = esc_attr( sanitize_text_field( wp_unslash( $_POST['version'] ) ) );
				if ( isset( $this->migrate_version_points[ $version ] ) ) {
					if ( isset( $migration_report[ $version ] ) ) {
						unset( $migration_report[ $version ] );
						update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );
					}
				}
			}

			$output = array( 'message' => $message ? : 'done' );
			wp_die( wp_json_encode( $output ) );
		}

		public function gm_switch_migrate_db_version() {

			$message     = 'Error. Switch ignored';
			$nonce_check = true;

			if (
				! isset( $_REQUEST['gm_nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['gm_nonce'] ) ), 'groovymenu_migrate_log' )
			) {
				$message     = esc_html__( 'Security check error. Try refresh that page, please.', 'groovy-menu' );
				$nonce_check = false;
			}

			if ( $nonce_check && isset( $_POST['version'] ) && isset( $this->migrate_version_points[ esc_attr( sanitize_text_field( wp_unslash( $_POST['version'] ) ) ) ] ) ) {
				update_option( GROOVY_MENU_DB_VER_OPTION, esc_attr( sanitize_text_field( wp_unslash( $_POST['version'] ) ) ) );
			}

			$output = array( 'message' => $message );
			wp_die( wp_json_encode( $output ) );
		}

		public function gm_switch_migrate_cron_job() {
			$migration_report             = $this->migration_report;
			$migration_report['cron_job'] = false;

			update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );

			$output = array( 'message' => 'done' );
			wp_die( wp_json_encode( $output ) );
		}

		public function gm_switch_migrate_dismissed_info() {

			$migration_report = $this->migration_report;
			$new_status       = false;

			if ( isset( $migration_report['dismissed_info'] ) && is_bool( $migration_report['dismissed_info'] ) ) {
				$new_status = ! $migration_report['dismissed_info'];
			}

			$migration_report['dismissed_info'] = $new_status;

			update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );

			$output = array( 'message' => 'done' );
			wp_die( wp_json_encode( $output ) );
		}

		public function infoCurrentStatus() {

			$cron_job = esc_html__( 'Sleep (not active)', 'groovy-menu' );
			if ( isset( $this->migration_report['cron_job'] ) && $this->migration_report['cron_job'] ) {
				$cron_job  = '<b>' . esc_html__( 'On the run (active)', 'groovy-menu' ) . '</b>';
				$cron_job .= '<button class="gm-migrate-debug-action-btn" data-action="gm_switch_migrate_cron_job">' . esc_html__( 'Switch Off', 'groovy-menu' ) . '</button>';
			}

			$dismissed_info = esc_html__( 'Show', 'groovy-menu' );
			if ( isset( $this->migration_report['dismissed_info'] ) && $this->migration_report['dismissed_info'] ) {
				$dismissed_info = esc_html__( 'Hide', 'groovy-menu' );
			}

			$dismissed_info .= '<button class="gm-migrate-debug-action-btn" data-action="gm_switch_migrate_dismissed_info">' . esc_html__( 'Switch', 'groovy-menu' ) . '</button>';

			$statuses = array(
				'Migrate cron process'        => $cron_job,
				'Migration in process notice' => $dismissed_info,
			);

			$content = \GroovyMenu\DebugPage::get_instance()->addListWithActions( $statuses );

			\GroovyMenu\DebugPage::get_instance()->addSection(
				esc_html__( 'Status of migrations', 'groovy-menu' ),
				'',
				$content
			);

		}


		public function infoAllMigrationsStatus() {

			$migrations      = array();
			$current_version = get_option( GROOVY_MENU_DB_VER_OPTION );

			foreach ( $this->migrate_version_points as $index => $data ) {
				$completed = isset( $this->migration_report[ $index ] ) ? $this->migration_report[ $index ] : '';
				$value     = '<span class="gm-debug-status">n/a</span>';

				if ( $completed ) {
					$value = '<span class="gm-debug-status gm-debug-status--done">' . esc_html( $completed ) . '</span>';
						$value .= '<button class="gm-migrate-debug-action-btn" data-action="gm_remove_migrate_db_version" data-version="' . esc_attr( $index ) . '">' . esc_html__( 'Remove flag', 'groovy-menu' ) . '</button>';
				}

				$value .= '<button class="gm-migrate-debug-action-btn" data-action="gm_migrate_log" data-version="' . esc_attr( $index ) . '">' . esc_html__( 'Log', 'groovy-menu' ) . '</button>';

				if ( $current_version !== $index ) {
					$value .= '<button class="gm-migrate-debug-action-btn" data-action="gm_switch_migrate_db_version" data-version="' . esc_attr( $index ) . '">' . esc_html__( 'Set as current DB Version', 'groovy-menu' ) . '</button>';
				}

				$migrations[ $index ] = $value;
			}

			$content = \GroovyMenu\DebugPage::get_instance()->addList( $migrations );

			// nonce content.
			$content .= '<input type="hidden" id="_gm_nonce_migrations" name="_gm_nonce_migrations" value="' . wp_create_nonce( 'groovymenu_migrate_log' ) . '" />';

			$content .= '<div class="gm-debug-log-block-wrapper gm-debug-log-hidden" id="gm-debug-log-block">';
			$content .= '<div class="gm-debug-log-block-title">'. esc_html__( 'Log data for migrate version:', 'groovy-menu' ).' <span class="gm-debug-log-block-version"></span></div>';
			$content .= '<div class="gm-debug-log-block-content"></div>';
			$content .= '</div>';

			\GroovyMenu\DebugPage::get_instance()->addSection(
				esc_html__( 'Available migrations', 'groovy-menu' ),
				'',
				$content
			);

		}


	}
}
