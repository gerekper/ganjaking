<?php  if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Mute Screamer
 *
 * PHPIDS for Wordpress
 */

/**
 * Mute Screamer upgrader class. Install and updates default_filter.xml
 * and Converter.php from phpids.org
 */

// For code coverage
if ( ! class_exists( 'WP_Upgrader' ) ) {
    require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
}

if ( ! class_exists( 'HMWP_MS_Upgrader' ) ) {
	class HMWP_MS_Upgrader extends WP_Upgrader {
		/**
		 * Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->init();
		}

		/**
		 * Handle the upgrade
		 *
		 * @param array the files to upgrade
		 * @return bool true on success, false on failure
		 */
		public function upgrade( $files = array() ) {
			global $wp_filesystem;

			// Connect to the Filesystem first.
			$res = $this->fs_connect( array( WP_CONTENT_DIR, WP_PLUGIN_DIR ) );
			if ( ! $res ) {
				$this->skin->footer();
				return false;
			}

			// Maintenance mode
			$this->maintenance_mode( true );

			$upgrade_folder = $wp_filesystem->wp_content_dir() . 'upgrade/';
			$hmwp_ms_folder    = $wp_filesystem->wp_plugins_dir() . 'mute-screamer/libraries/IDS/';

			// Only check to see if the Dir exists upon creation failure. Less I/O this way.
			if ( ! $wp_filesystem->mkdir( $upgrade_folder, FS_CHMOD_DIR ) && ! $wp_filesystem->is_dir( $upgrade_folder ) ) {
				show_message( new WP_Error( 'mkdir_failed', __( 'Could not create directory.', 'mute-screamer' ), $upgrade_folder ) );
				$this->maintenance_mode( false );
				return false;
			}

			// Clean up contents of upgrade directory beforehand.
			$upgrade_files = $wp_filesystem->dirlist( $upgrade_folder );
			if ( ! empty( $upgrade_files ) ) {
				foreach( $upgrade_files as $file )
					$wp_filesystem->delete( $upgrade_folder . $file['name'], true );
			}

			// Save files into upgrade folder, copy into place
			foreach ( $files as $key => $val ) {
				show_message( sprintf( __( 'Copying %s into place...', 'mute-screamer' ), esc_html( $key ) ) );
				$new_file = $upgrade_folder . $key;
				$wp_filesystem->put_contents( $new_file, $val['body'], FS_CHMOD_FILE );

				// Copy files into place
				if ( ! $wp_filesystem->copy( $new_file, $hmwp_ms_folder . $key, true ) ) {
					$wp_filesystem->delete( $upgrade_folder, true );
					show_message( new WP_Error( 'copy_failed', __( 'Could not copy files.', 'mute-screamer' ) ) );
					$this->maintenance_mode( false );
					return false;
				}
				$wp_filesystem->chmod( $hmwp_ms_folder . $key, FS_CHMOD_FILE );

				// Delete files from upgrade folder
				$wp_filesystem->delete( $new_file, true );
			}

			$this->maintenance_mode( false );

			show_message( __( 'Mute Screamer updated successfully', 'mute-screamer' ) );
			show_message( '<a target="_parent" href="' . esc_url( admin_url() ) . '">' . __( 'Go to Dashboard', 'mute-screamer' ) . '</a>' );
			return true;
		}

		/**
		 * Flush output to browser
		 *
		 * @return void
		 */
		private function flush_output() {
			wp_ob_end_flush_all();
			flush();
		}
	}
}
