<?php

namespace WBCR\Factory_439;

use Exception;
use Wbcr_Factory439_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Often when updating plugins, you need to make some changes to the database.
 * This class automatically checks for plugin migrations and executes them when
 * updating.
 *
 * The class has a debug mode, to enable the debug mode add constants to your plugin:
 * define ('FACTORY_MIGRATIONS_DEBUG', true) - enables/disables debugging mode
 * define ('FACTORY_MIGRATIONS_FORCE_OLD_VERSION', '1.1.9') - sets previous version
 * for the plugin, if constant isn't set, then the previous version is taken from
 * the database.
 *
 * todo: get_option and get_site_option are used because some caching plugins caching options, which causes problems
 *
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @since         4.1.1
 */
class Migrations {

	protected $plugin;

	/**
	 * Migrations constructor.
	 *
	 * @param Wbcr_Factory439_Plugin $plugin
	 *
	 * @throws Exception
	 */
	public function __construct( Wbcr_Factory439_Plugin $plugin ) {

		$this->plugin = $plugin;
		$plugin_name  = $plugin->getPluginName();

		if ( ! file_exists( $this->plugin->get_paths()->migrations ) ) {
			throw new Exception( 'Starting with version 4.1.1 of the Core for Factory framework module, you must create a "migrations" folder in the root of your plugin to store the migration of the plugin.' );
		}

		if ( is_admin() ) {
			add_action( "admin_init", [ $this, "check_migrations" ] );

			add_action( "wbcr/factory/plugin_{$plugin_name}_activated", [ $this, 'activation_hook' ] );
			add_action( "wbcr/factory/admin_notices", [ $this, "debug_bar_notice" ], 10, 2 );
			add_action( "wbcr/factory/admin_notices", [ $this, "migration_error_notice" ], 10, 2 );
		}
	}

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.1.1
	 * @return mixed|void
	 */
	public function get_plugin_activated_time() {
		if ( $this->plugin->isNetworkActive() ) {
			return get_site_option( $this->plugin->getOptionName( 'plugin_activated' ), 0 );
		}

		return get_option( $this->plugin->getOptionName( 'plugin_activated' ), 0 );
	}

	/**
	 * Check if migration is necessary for plugin and if there are errors from previous migrations.
	 * In debug mode, migrations are not performed automatically.
	 */
	public function check_migrations() {
		if ( $this->is_migration_error() && isset( $_GET['wbcr_factory_fix_migration_error'] ) ) {
			$this->fix_migration_error();
			wp_redirect( remove_query_arg( 'wbcr_factory_fix_migration_error' ) );
			die();
		}

		if ( $this->is_debug() && isset( $_GET['wbcr_factory_test_migration'] ) ) {
			$this->make_migration();
			wp_redirect( remove_query_arg( 'wbcr_factory_test_migration' ) );
			die();
		}

		if ( $this->need_migration() && ! $this->is_debug() ) {
			$this->make_migration();
		}
	}

	/**
	 * Notification displays the errors of outstanding migrations to fix errors
	 * you need to follow the instructions in the notification and click
	 * "I fixed, confirm migration".
	 *
	 * What is it for. Migrations are performed in background and on some sites,
	 * due to php errors or for some other reason, migration may be
	 * interrupted, because of what plugin will work incorrectly, you may lose settings.
	 *
	 * When creating new migrations, developer will add error handlers,
	 * and framework will intercept them safely for user and display them
	 * in this notice.
	 *
	 * @param array  $notices
	 * @param static $plugin_name
	 *
	 * @return array
	 */
	public function migration_error_notice( $notices, $plugin_name ) {

		if ( $this->plugin->getPluginName() !== $plugin_name ) {
			return $notices;
		}

		if ( ! $this->is_migration_error() || ! current_user_can( 'update_plugins' ) ) {
			return $notices;
		}

		if ( $this->plugin->isNetworkActive() ) {
			$migration_error_text = get_site_option( $this->plugin->getOptionName( 'plugin_migration_error' ), '' );
		} else {
			$migration_error_text = get_option( $this->plugin->getOptionName( 'plugin_migration_error' ), '' );
		}

		$fix_migration_error_url = add_query_arg( 'wbcr_factory_fix_migration_error', 1 );

		$notice_text = $migration_error_text;
		$notice_text .= "<br><br><a href='{$fix_migration_error_url}' class='button button-default'>" . __( 'I fixed, confirm migration', 'wbcr_factory_439' ) . "</a>";

		$notices[] = [
			'id'              => 'migration_debug_bar',
			'type'            => 'error',
			'dismissible'     => false,
			'dismiss_expires' => 0,
			'text'            => '<p><b>' . $this->plugin->getPluginTitle() . ' ' . __( 'migration error', 'wbcr_factory_439' ) . '</b><br>' . $notice_text . '</p>'
		];

		return $notices;
	}

	/**
	 * Debug panel, display some information from the database. Also allows
	 * perform manual migrations to test new migrations.
	 *
	 * @param array  $notices
	 * @param string $plugin_name
	 *
	 * @return array
	 */
	public function debug_bar_notice( $notices, $plugin_name ) {

		if ( $this->plugin->getPluginName() !== $plugin_name ) {
			return $notices;
		}
		if ( ! $this->is_debug() || ! current_user_can( 'update_plugins' ) ) {
			return $notices;
		}

		$migrate_url = add_query_arg( 'wbcr_factory_test_migration', 1 );

		$notice_text = __( "Plugin activated:", "wbcr_factory_439" ) . ' ' . date( "Y-m-d H:i:s", $this->get_plugin_activated_time() ) . "<br>";

		$notice_text .= __( "Old plugin version (debug):", "wbcr_factory_439" ) . ' ' . $this->get_old_plugin_version() . "<br>";
		$notice_text .= __( "Current plugin version:", "wbcr_factory_439" ) . ' ' . $this->get_current_plugin_version() . "<br>";
		$notice_text .= __( "Need migration:", "wbcr_factory_439" ) . ' ' . ( $this->need_migration() ? "true" : "false" ) . "<br><br>";
		$notice_text .= "<a href='{$migrate_url}' class='button button-default'>" . __( "Migrate now", "wbcr_factory_439" ) . "</a><br>";

		$notices[] = [
			'id'              => 'migration_debug_bar',
			'type'            => 'warning',
			'dismissible'     => false,
			'dismiss_expires' => 0,
			'text'            => '<p><b style="color:red;">' . $this->plugin->getPluginTitle() . ' ' . __( 'migrations DEBUG bar', 'wbcr_factory_439' ) . '</b><br>' . $notice_text . '</p>'
		];

		return $notices;
	}

	/**
	 * Runs when plugin is activated. Checks if you need to migrate
	 * and if necessary it does it. Also adds a option when the plugin
	 * was activated for the first time.
	 */
	public function activation_hook() {
		/*if ( $this->need_migration() && ! $this->is_debug() ) {
			$this->make_migration();
		}*/

		// just time to know when the plugin was activated the first time
		$activated = $this->get_plugin_activated_time();

		if ( ! $activated ) {
			if ( $this->plugin->isNetworkActive() ) {
				update_site_option( $this->plugin->getOptionName( 'plugin_activated' ), time() );
				update_site_option( $this->plugin->getOptionName( 'plugin_version' ), $this->get_current_plugin_version() );
			} else {
				update_option( $this->plugin->getOptionName( 'plugin_activated' ), time() );
				update_option( $this->plugin->getOptionName( 'plugin_version' ), $this->get_current_plugin_version() );
			}
		}
	}

	/**
	 * Checks if debug mode of migrations from version x.x.x to x.x.y is enabled.
	 *
	 * @return bool
	 */
	protected function is_debug() {
		return defined( 'FACTORY_MIGRATIONS_DEBUG' ) && FACTORY_MIGRATIONS_DEBUG;
	}

	/**
	 * Gets previous version of plugin that plugin had before updating to the new version.
	 *
	 * @return string|null
	 */
	protected function get_old_plugin_version() {

		if ( $this->is_debug() && defined( 'FACTORY_MIGRATIONS_FORCE_OLD_VERSION' ) ) {
			return FACTORY_MIGRATIONS_FORCE_OLD_VERSION;
		}

		if ( $this->plugin->isNetworkActive() ) {
			$plugin_version = get_site_option( $this->plugin->getOptionName( 'plugin_version' ), null );
		} else {
			$plugin_version = get_option( $this->plugin->getOptionName( 'plugin_version' ), null );
		}

		if ( ! empty( $plugin_version ) ) {
			return $plugin_version;
		}

		# TODO: Remove after few releases
		# This block for compatibility code with old version of framework < 4.1.1
		#-------------------------------------------
		if ( $this->plugin->isNetworkActive() ) {
			$plugin_versions = get_site_option( 'factory_plugin_versions', [] );
		} else {
			$plugin_versions = get_option( 'factory_plugin_versions', [] );
		}

		$plugin_version = isset( $plugin_versions[ $this->plugin->getPluginName() ] ) ? $plugin_versions[ $this->plugin->getPluginName() ] : null;

		if ( ! empty( $plugin_version ) ) {
			$plugin_version = str_replace( [ 'free-', 'premium-', 'offline-' ], '', $plugin_version );
		}

		#-------------------------------------------

		return $plugin_version;
	}

	/**
	 * Gets the current version of plugin.
	 *
	 * @return string
	 */
	protected function get_current_plugin_version() {
		return $this->plugin->getPluginVersion();
	}

	/**
	 * Do I need migration for plugin? If previous migration was with a error, then
	 * method will always return false to prevent looping.
	 *
	 * @return mixed
	 */
	protected function need_migration() {
		if ( $this->is_migration_error() ) {
			return false;
		}

		return version_compare( $this->get_old_plugin_version(), $this->get_current_plugin_version(), '<' );
	}

	/**
	 * Are there errors from previous migrations?
	 *
	 * @return bool
	 */
	protected function is_migration_error() {
		if ( $this->plugin->isNetworkActive() ) {
			$error = get_site_option( $this->plugin->getOptionName( 'plugin_migration_error' ), false );
		} else {
			$error = get_option( $this->plugin->getOptionName( 'plugin_migration_error' ), false );
		}

		return $error !== false;
	}

	/**
	 * Remove an option in database, thereby fix errors of the previous migration.
	 */
	protected function fix_migration_error() {
		if ( $this->plugin->isNetworkActive() ) {
			delete_site_option( $this->plugin->getOptionName( 'plugin_migration_error' ) );

			return;
		}

		delete_option( $this->plugin->getOptionName( 'plugin_migration_error' ) );
	}

	/**
	 * Migrates the plugin from version x.x.x to x.x.y. Automatically searches for files
	 * migrations to the plugin's root directory and executes them. Default files
	 * migrations are stored in wp-content/plugins/plugin-name/migrations and have names
	 * 0x0x0x.php, which corresponds to the version x.x.x. Method executes those migration files
	 * versions of which are between the previous version of plugin and current one.
	 *
	 */
	protected function make_migration() {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$old_plugin_version = $this->get_old_plugin_version();
		$new_plugin_version = $this->get_current_plugin_version();

		if ( empty( $old_plugin_version ) ) {
			$this->update_plugin_version_in_db();
		}

		// converts versions like 0.0.0 to 000000
		$old_number = $this->get_version_number( $old_plugin_version );
		$new_number = $this->get_version_number( $new_plugin_version );

		try {

			$update_files = $this->plugin->get_paths()->migrations;
			$files        = $this->find_files( $update_files );

			if ( empty( $files ) ) {
				$this->update_plugin_version_in_db();

				return;
			}

			// finds updates that has intermediate version
			foreach ( (array) $files as $item ) {
				if ( ! preg_match( '/^\d+$/', $item['name'] ) ) {
					continue;
				}

				$item_number = intval( $item['name'] );

				if ( $item_number > $old_number && $item_number <= $new_number ) {
					$classes = $this->get_classes( $item['path'] );

					if ( count( $classes ) == 0 ) {
						continue;
					}

					foreach ( $classes as $path => $class_data ) {
						include_once( $path );
						$update_class = $class_data['name'];

						$update = new $update_class( $this->plugin );
						$update->install();
					}
				}
			}

			$this->update_plugin_version_in_db();
		} catch( Exception $e ) {
			if ( $this->plugin->isNetworkActive() ) {
				update_site_option( $this->plugin->getOptionName( 'plugin_migration_error' ), $e->getMessage() );

				return;
			}
			update_option( $this->plugin->getOptionName( 'plugin_migration_error' ), $e->getMessage() );
		}
	}

	/**
	 * Updates version of plugin in database. So that we can track which
	 * previous version of plugin was at the user, before he updated
	 * plugin.
	 */
	protected function update_plugin_version_in_db() {

		# TODO: Delete after few releases
		# This block for compatibility code with the old version of framework.
		# Cleans up old data, after the transition to new version of framework.
		#-------------------------------------------
		if ( $this->plugin->isNetworkActive() ) {
			$plugin_versions = get_site_option( 'factory_plugin_versions', [] );
		} else {
			$plugin_versions = get_option( 'factory_plugin_versions', [] );
		}

		if ( isset( $plugin_versions[ $this->plugin->getPluginName() ] ) ) {
			unset( $plugin_versions[ $this->plugin->getPluginName() ] );
		}

		if ( $this->plugin->isNetworkActive() ) {
			if ( empty( $plugin_versions ) ) {
				delete_site_option( 'factory_plugin_versions' );
			}
			update_site_option( 'factory_plugin_versions', $plugin_versions );
			update_site_option( $this->plugin->getOptionName( 'plugin_version' ), $this->get_current_plugin_version() );

			return;
		}

		if ( empty( $plugin_versions ) ) {
			delete_option( 'factory_plugin_versions' );
		}

		update_option( 'factory_plugin_versions', $plugin_versions );
		update_option( $this->plugin->getOptionName( 'plugin_version' ), $this->get_current_plugin_version() );
	}

	/**
	 * Converts string representation of the version to the numeric.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version   A string version to convert.
	 *
	 * @return integer
	 */
	protected function get_version_number( $version ) {
		preg_match( '/(\d+)\.(\d+)\.(\d+)/', $version, $matches );
		if ( count( $matches ) == 0 ) {
			return false;
		}

		$number = '';
		$number .= ( strlen( $matches[1] ) == 1 ) ? '0' . $matches[1] : $matches[1];
		$number .= ( strlen( $matches[2] ) == 1 ) ? '0' . $matches[2] : $matches[2];
		$number .= ( strlen( $matches[3] ) == 1 ) ? '0' . $matches[3] : $matches[3];

		return intval( $number );
	}

	/**
	 * Returns a list of files at a given path.
	 *
	 * @param string $path   path for search
	 */
	private function find_files( $path ) {
		return $this->find_file_or_folders( $path, true );
	}

	/**
	 * Returns a list of folders at a given path.
	 *
	 * @param string $path   path for search
	 */
	/*private function find_folders( $path ) {
		return $this->find_file_or_folders( $path, false );
	}*/

	/**
	 * Returns a list of files or folders at a given path.
	 *
	 * @param string $path    path for search
	 * @param bool   $files   files or folders?
	 */
	private function find_file_or_folders( $path, $areFiles = true ) {
		if ( ! is_dir( $path ) ) {
			return [];
		}

		$entries = scandir( $path );
		if ( empty( $entries ) ) {
			return [];
		}

		$files = [];
		foreach ( $entries as $entryName ) {
			if ( $entryName == '.' || $entryName == '..' ) {
				continue;
			}

			$filename = $path . '/' . $entryName;
			if ( ( $areFiles && is_file( $filename ) ) || ( ! $areFiles && is_dir( $filename ) ) ) {
				$files[] = [
					'path' => str_replace( "\\", "/", $filename ),
					'name' => $areFiles ? str_replace( '.php', '', $entryName ) : $entryName
				];
			}
		}

		return $files;
	}

	/**
	 * Gets php classes defined in a specified file.
	 *
	 * @param string $path
	 *
	 * @throws Exception
	 */
	private function get_classes( $path ) {

		$phpCode = file_get_contents( $path );

		$classes = [];

		if ( ! function_exists( 'token_get_all' ) ) {
			throw new Exception( __( 'There is no PHP Tokenizer extension installed on your server, you cannot use the token_get_all function.', 'wbcr_factory_439' ) );
		}

		$tokens = token_get_all( $phpCode );

		$count = count( $tokens );
		for ( $i = 2; $i < $count; $i ++ ) {
			if ( is_array( $tokens ) && $tokens[ $i - 2 ][0] == T_CLASS && $tokens[ $i - 1 ][0] == T_WHITESPACE && $tokens[ $i ][0] == T_STRING ) {

				$extends = null;
				if ( $tokens[ $i + 2 ][0] == T_EXTENDS && $tokens[ $i + 4 ][0] == T_STRING ) {
					$extends = $tokens[ $i + 4 ][1];
				}

				$class_name       = $tokens[ $i ][1];
				$classes[ $path ] = [
					'name'    => $class_name,
					'extends' => $extends
				];
			}
		}

		/**
		 * result example:
		 *
		 * $classes['/plugin/items/filename.php'] = array(
		 *      'name'      => 'PluginNameItem',
		 *      'extendes'  => 'PluginNameItemBase'
		 * )
		 */

		return $classes;
	}
}
