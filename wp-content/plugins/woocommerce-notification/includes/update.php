<?php
/**
 * VillaTheme_Plugin_Updater
 */

// no direct access allowed
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * The main challange here is defining a dynamic dl link
 * for $updade_plugins_transient->response[ $plugin_slug ]->package;
 */


if ( ! class_exists( 'Plugin_Upgrader' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}


if ( ! class_exists( 'VillaTheme_Plugin_Updater' ) ) {

	class VillaTheme_Plugin_Updater extends Plugin_Upgrader {

		/**
		 * Plugin directory and main file name (e.g myplugin/myplugin.php)
		 *
		 * @var string
		 */
		public $plugin_slug;

		/**
		 * Plugin slug
		 *
		 * @var string
		 */
		public $slug;

		/**
		 * Set plugin info and add essential hooks
		 *
		 * @param string $plugin_slug      The name of directory and main file name of plugin
		 * @param string $slug             Then slug name of plugin (optional)
		 * @param string $setting_page_url URL of Setting page
		 */
		protected $setting_page_url;

		public function __construct( $plugin_slug, $slug = '', $setting_page_url = '#' ) {

			parent::__construct();

			$this->plugin_slug      = $plugin_slug;
			$this->setting_page_url = $setting_page_url;

			add_action( 'admin_init', array( $this, 'plugin_update_rows' ), 12 );
			// a custom hook that fires on update.php page while upgrading the packages
			add_action( "update-custom_{$this->slug}-upgrade", array( $this, 'custom_upgrade_plugin' ) );
		}

		/**
		 * Fires on the page wp-admin/update.php?{$this->slug}-upgrade page
		 *
		 * @return void
		 */
		function custom_upgrade_plugin() {
			$plugin = isset( $_REQUEST['plugin'] ) ? trim( $_REQUEST['plugin'] ) : '';

			if ( ! current_user_can( 'update_plugins' ) ) {
				wp_die( __( 'You do not have sufficient permissions to update plugins for this site.' ) );
			}

			check_admin_referer( 'upgrade-plugin_' . $plugin );

			wp_enqueue_script( 'updates' );

			require_once( ABSPATH . 'wp-admin/admin-header.php' );

			$nonce = 'upgrade-plugin_' . $plugin;
			$url   = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin );

			if ( $this->update_plugin() ) {
				do_action( $plugin . "_updated" );
			}

			// return to lugins page link
			echo '<a href="' . self_admin_url( 'plugins.php' ) . '" title="' . esc_attr__( 'Go to plugins page' ) . '" target="_parent">' . __( 'Return to Plugins page' ) . '</a>';

			include( ABSPATH . 'wp-admin/admin-footer.php' );
		}


		/**
		 * Initialize the upgrade strings.
		 *
		 * @since 2.8.0
		 */
		public function upgrade_strings() {

			parent::upgrade_strings();

			$this->strings['no_package']            = sprintf(
				__( 'Please (re)activate your license in %sMaster Slider > setting page%s. Valid license is required in order to update this plugin.' ),
				'<a href="' . admin_url( 'admin.php?page=' . $this->slug . '-setting' ) . '">', '</a>'
			);
			$this->strings['downloading_package']   = __( 'Downloading package ...' );
			$this->strings['download_item_package'] = __( 'Downloading package ...' );
		}

		/**
		 * Upgrade a plugin.
		 *
		 * @param string $plugin The basename path to the main plugin file.
		 *
		 * @return bool|WP_Error True if the upgrade was successful, false or a {@see WP_Error} object otherwise.
		 */
		public function update_plugin() {
			/**
			 * Initialize the WP_Filesystem
			 */
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			$plugin = $this->plugin_slug;

			$this->init();
			$this->upgrade_strings();

			$current = get_site_transient( 'update_plugins' );
			if ( ! isset( $current->response[ $plugin ] ) ) {
				$this->skin->before();
				$this->skin->set_result( false );
				$this->skin->error( 'up_to_date' );
				$this->skin->after();

				return false;
			}

			// Get the URL to the zip file
			$r = $current->response[ $plugin ];

			add_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ), 10, 2 );
			add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );
			//'source_selection' => array($this, 'source_selection'), //there's a trac ticket to move up the directory for zip's which are made a bit differently, useful for non-.org plugins.

			$this->run( array(
				'package'           => $r->package,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'plugin' => $plugin,
					'type'   => 'plugin',
					'action' => 'update',
				),
			) );

			// Cleanup our hooks, in case something else does a upgrade on this connection.
			remove_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ) );
			remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );

			if ( ! $this->result || is_wp_error( $this->result ) ) {
				return $this->result;
			}

			// Force refresh of plugin update information
			delete_site_transient( 'update_plugins' );
			wp_cache_delete( 'plugins', 'plugins' );

			return true;
		}

		/**
		 * Add hooks for modifying the plugin row context
		 */
		public function plugin_update_rows() {


			remove_action( "after_plugin_row_{$this->plugin_slug}", 'wp_plugin_update_row', 10 );
			add_action( "after_plugin_row_{$this->plugin_slug}", array( $this, 'plugin_update_row' ), 10, 2 );
		}

		/**
		 * Override the plugin row context
		 *
		 * @param  string $file        The plugin file path
		 * @param  array  $plugin_data Plugin information
		 *
		 * @return void
		 */
		public function plugin_update_row( $file, $plugin_data ) {

			$current = get_site_transient( 'update_plugins' );


			if ( ! isset( $current->response[ $file ] ) ) {
				return false;
			}


			$r = $current->response[ $file ];

			$plugins_allowedtags = array(
				'a'       => array(
					'href'  => array(),
					'title' => array()
				),
				'abbr'    => array(
					'title' => array()
				),
				'acronym' => array(
					'title' => array()
				),
				'code'    => array(),
				'em'      => array(),
				'strong'  => array()
			);

			$plugin_name   = wp_kses( $plugin_data['Name'], $plugins_allowedtags );
			$details_url   = $r->url;
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

			if ( is_network_admin() || ! is_multisite() ) {


				echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="notice inline notice-warning notice-alt"><p>';

				printf( __( 'New version of %1$s available. You can download or <a href="%2$s" class="thickbox" title="%3$s">view version %4$s</a>. Please make sure that you fill your purchased code in <a href="%5$s">Setting page</a>.' ),
					$plugin_name, esc_url( $details_url ), esc_attr( $plugin_name ), $r->new_version, $this->setting_page_url
				);

				do_action( "in_plugin_update_message-{$file}", $plugin_data, $r );

				echo '</p></div></td></tr>';
			}
		}
	}
}
