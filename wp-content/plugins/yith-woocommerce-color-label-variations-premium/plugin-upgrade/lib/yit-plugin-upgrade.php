<?php
/**
 * This file belongs to the YIT Framework.
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author  YITH
 * @package YITH License & Upgrade Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Plugin_Upgrade' ) ) {
	/**
	 * YIT Upgrade
	 * Notify and Update plugin
	 *
	 * @class       YITH_Plugin_Upgrade
	 * @since       1.0
	 * @author      YITH
	 * @package     YITH
	 * @see         WP_Updater Class
	 */
	class YITH_Plugin_Upgrade {

		/**
		 * XML notifier update
		 *
		 * @var string
		 */
		protected $remote_url = 'https://update.yithemes.com/plugin-xml.php';

		/**
		 * The api server url
		 *
		 * @var string
		 */
		protected $package_url = 'https://licence.yithemes.com/api/download/';

		/**
		 * The registered plugins
		 *
		 * @var array
		 */
		protected $plugins = array();

		/**
		 * Current plugin upgrading
		 *
		 * @var string
		 */
		protected $plugin_upgrading = '';

		/**
		 * The main instance
		 *
		 * @var YITH_Plugin_Upgrade
		 */
		protected static $instance;

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return YITH_Plugin_Upgrade
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Construct
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function __construct() {
			add_action( 'pre_auto_update', array( $this, 'catch_plugin_upgrading' ), 10, 3 );
			add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
			add_filter( 'plugin_auto_update_setting_html', array( $this, 'hide_auto_update_multisite' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20 );

			if ( defined( 'YIT_LICENCE_DEBUG' ) && YIT_LICENCE_DEBUG ) {
				$this->package_url = defined( 'YIT_LICENCE_UPGRADE_LOCALHOST' ) ? YIT_LICENCE_UPGRADE_LOCALHOST : 'https://staging-licenceyithemes.kinsta.cloud/api/download/';
				add_filter( 'block_local_requests', '__return_false' );
			}

			add_action( 'wp_ajax_yith_plugin_fw_get_premium_changelog', array( $this, 'show_changelog_for_premium_plugins' ) );

			add_action( 'load-plugins.php', array( $this, 'remove_wp_plugin_update_row' ), 25 );
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			add_filter( 'pre_update_site_option_auto_update_plugins', array( $this, 'avoid_auto_update_bulk' ), 10, 4 );

			// on plugin deactivated refresh update plugins transient
			add_action( 'deactivated_plugin', array( $this, 'force_regenerate_update_transient_on_deactivated' ), 10, 1 );

			/* Fix Details url on update core page */
			add_action( 'self_admin_url', array( $this, 'details_plugin_url_in_update_core_page' ), 10, 3 );

			// Fix slug not defined in update core page and prevent update plugins from update core if a plugin not enabled in all network
			add_filter( 'site_transient_update_plugins', array( $this, 'filter_site_transient_update_plugins' ) );
		}

		/**
		 * Show changelog for premium plugins
		 *
		 * @since 3.0.14
		 */
		public function show_changelog_for_premium_plugins() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			if ( isset( $_GET['plugin'] ) && isset( $_GET['section'] ) && 'changelog' === $_GET['section'] ) {
				$plugin_init = sanitize_text_field( $_GET['plugin'] );
				if ( isset( $this->plugins[ $plugin_init ] ) ) {
					// This is YITH Premium Plugin.
					if ( ! empty( $this->plugins[ $plugin_init ]['info']['changelog'] ) ) {
						$plugin_name = $this->plugins[ $plugin_init ]['info']['Name'];
						$changelog   = $this->plugins[ $plugin_init ]['info']['changelog'];
						$template    = YIT_CORE_PLUGIN_TEMPLATE_PATH . '/upgrade/changelog.php';
						if ( file_exists( $template ) ) {
							include $template;
						}
						die();
					}
					$error    = esc_html__( 'An unexpected error occurred, please try again later. Thanks!', 'yith-plugin-upgrade-fw' );
					$template = YIT_CORE_PLUGIN_TEMPLATE_PATH . '/upgrade/error.php';
					if ( file_exists( $template ) ) {
						include $template;
					} else {
						echo "<p>$error</p>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					die();
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		}

		/**
		 * Main plugin Instance
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param string $plugin_slug The plugin slug.
		 * @param string $plugin_init The plugin init file.
		 * @return void
		 */
		public function register( $plugin_slug, $plugin_init ) {

			if ( ! function_exists( 'get_plugins' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugins     = get_plugins();
			$plugin_info = $plugins[ $plugin_init ];

			$this->plugins[ $plugin_init ] = array(
				'info' => $plugin_info,
				'slug' => $plugin_slug,
			);
			$plugin                        = $this->plugins[ $plugin_init ];
			$transient                     = 'yith_register_' . md5( $plugin_slug );
			if ( apply_filters( 'yith_register_delete_transient', false ) ) {
				delete_transient( $transient );
			}
			$info = get_transient( $transient );
			if ( false === $info || apply_filters( 'yith_register_delete_transient', false ) ) {
				$xml        = $this->get_remote_url( $plugin );
				$remote_xml = wp_remote_get( $xml );

				$error = false;
				if ( ! is_wp_error( $remote_xml ) && isset( $remote_xml['response']['code'] ) && 200 === intval( $remote_xml['response']['code'] ) ) {
					$plugin_remote_info = function_exists( 'simplexml_load_string' ) ? @simplexml_load_string( $remote_xml['body'] ) : false;
					if ( $plugin_remote_info ) {
						$info['Latest']    = (string) $plugin_remote_info->latest;
						$info['changelog'] = (string) $plugin_remote_info->changelog;

						set_transient( $transient, $info, DAY_IN_SECONDS );
					} else {
						$error = true;
						error_log( sprintf( 'SimpleXML error in %s:%s [plugin slug: %s]', __FILE__, __FUNCTION__, $plugin_slug ) );
					}
				} else {
					$error = true;
				}

				if ( $error ) {
					// If error, set empty value in the transient to prevent multiple requests.
					$info = array(
						'Latest'    => '',
						'changelog' => '',
					);
					set_transient( $transient, $info, HOUR_IN_SECONDS );
				}
			}

			$this->plugins[ $plugin_init ]['info']['Latest']    = $info['Latest'];
			$this->plugins[ $plugin_init ]['info']['changelog'] = $info['changelog'];
		}

		/**
		 * Enqueue admin scripts
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function admin_enqueue_scripts() {
			global $pagenow;

			if ( 'plugins.php' === $pagenow && defined( 'YIT_CORE_PLUGIN_URL' ) ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_style( 'yit-upgrader', YIT_CORE_PLUGIN_URL . '/assets/css/yit-upgrader.css' );
				wp_enqueue_script( 'yith-update-plugins', YIT_CORE_PLUGIN_URL . '/assets/js/yith-update-plugins' . $suffix . '.js', array( 'jquery' ), false, true );

				$update_plugins_localized = array(
					'ajax_nonce' => wp_create_nonce( 'updates' ),
					'ajaxurl'    => admin_url( 'admin-ajax.php', 'relative' ),
					'l10n'       => array(
						/* translators: %s: Plugin name and version */
						'updating' => _x( 'Updating %s...', 'plugin-fw', 'yith-plugin-upgrade-fw' ), // No ellipsis.
						/* translators: %s: Plugin name and version */
						'updated'  => _x( '%s updated!', 'plugin-fw', 'yith-plugin-upgrade-fw' ),
						/* translators: %s: Plugin name and version */
						'failed'   => _x( '%s update failed', 'plugin-fw', 'yith-plugin-upgrade-fw' ),
					),
				);

				wp_localize_script( 'yith-update-plugins', 'yith_plugin_fw', $update_plugins_localized );
			}
		}

		/**
		 * Hide auto update on multisite for plugin. The update will be available only on single blog with active licence
		 *
		 * @since  4.1.0
		 * @author Francesco Licandro
		 * @param string $html The HTML of the plugin's auto-update column content, including
		 *                            toggle auto-update action links and time to next update.
		 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
		 * @return string
		 */
		public function hide_auto_update_multisite( $html, $plugin_file ) {
			if ( is_multisite() && ! empty( $this->plugins[ $plugin_file ] ) ) {
				return '';
			}

			return $html;
		}

		/**
		 * Catch YITH plugin upgrading
		 *
		 * @since  4.1.0
		 * @author Francesco Licandro
		 * @param string $type The type of update being checked: 'core', 'theme', 'plugin', or 'translation'.
		 * @param object $item The update offer.
		 * @param string $context The filesystem context (a path) against which filesystem access and status
		 *                        should be checked.
		 * @return void
		 */
		public function catch_plugin_upgrading( $type, $item, $context ) {

			if ( 'plugin' !== $type ) {
				return;
			}

			if ( ! empty( $item->plugin ) ) {
				$this->plugin_upgrading = $item->plugin;
			}
		}

		/**
		 * Get current plugin upgrading
		 *
		 * @since  4.1.0
		 * @author Francesco Licandro
		 * @param WP_Upgrader $upgrader WP_Upgrader instance.
		 * @return mixed
		 */
		public function get_plugin_upgrading( $upgrader ) {

			$plugin = $this->plugin_upgrading;
			if ( empty( $plugin ) ) {
				/* === WordPress 4.9 or greater Support === */
				$is_bulk        = $upgrader->skin instanceof Bulk_Plugin_Upgrader_Skin;
				$is_bulk_ajax   = $upgrader->skin instanceof WP_Ajax_Upgrader_Skin;
				/* === WP-CLI Support === */
				$is_wp_cli      = $upgrader->skin instanceof WP_CLI\UpgraderSkin;
				/* === ManageWP Support === */
				$is_manageWP 	= $upgrader->skin instanceof MWP_Updater_TraceableUpdaterSkin;

				if( $is_wp_cli || $is_manageWP ){
					$plugins = YITH_Plugin_Licence()->get_products();
					foreach( $plugins as $init => $info ){
						if( $upgrader->skin->plugin_info['Name'] == $info['Name'] ){
							$plugin = $init;
							break;
						}
					}
				}
				elseif ( ! $is_bulk && ! $is_bulk_ajax ) {
					// Bulk Action: Support for old WordPress Version.
					$plugin = isset( $upgrader->skin->plugin ) ? $upgrader->skin->plugin : false;
				} elseif ( $is_bulk_ajax ) {
					// Bulk Update for WordPress 4.9 or greater.
					if ( ! empty( $_POST['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
						$plugin = plugin_basename( sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
					}
				} else {
					// Bulk action upgrade.
					$action_url = wp_parse_url( $upgrader->skin->options['url'] );
					parse_str( rawurldecode( htmlspecialchars_decode( $action_url['query'] ) ), $output );
					$plugins = isset( $output['plugins'] ) ? $output['plugins'] : '';
					$plugins = explode( ',', $plugins );
					foreach ( $plugins as $plugin_init ) {
						$to_upgrade = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_init );
						if ( $to_upgrade['Name'] === $upgrader->skin->plugin_info['Name'] ) {
							$plugin = $plugin_init;
						}
					}
				}
			}

			return $plugin ? YITH_Plugin_Licence()->get_product( $plugin ) : array();
		}

		/**
		 * Retrive the zip package file
		 *
		 * @access public
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param bool        $reply Whether to bail without returning the package. Default false.
		 * @param string      $package The package file name.
		 * @param WP_Upgrader $upgrader WP_Upgrader instance.
		 * @return string
		 * @see    wp-admin/includes/class-wp-upgrader.php
		 */
		public function upgrader_pre_download( $reply, $package, $upgrader ) {

			// If class YITH_Plugin_Licence doesn't exists or plugin upgrading is not an YITH one, return.
			if ( ! function_exists( 'YITH_Plugin_Licence' ) ) {
				return $reply;
			}

			$plugin = $this->get_plugin_upgrading( $upgrader );
			if ( empty( $plugin ) ) {
				return false;
			}

			$licence    = YITH_Plugin_Licence()->get_licence();
			$product_id = $plugin['product_id'];

			if ( empty( $licence[ $product_id ] ) ) {
				return new WP_Error( 'license_not_valid', esc_html_x( 'You have to activate the plugin to benefit from automatic updates.', '[Update Plugin Message: License not enabled]', 'yith-plugin-upgrade-fw' ) );
			}

			$args = array(
				'email'       => $licence[ $product_id ]['email'],
				'licence_key' => $licence[ $product_id ]['licence_key'],
				'product_id'  => $product_id,
				'secret_key'  => $plugin['secret_key'],
				'instance'    => YITH_Plugin_Licence()->get_home_url(),
			);

			if ( ! preg_match( '!^(http|https|ftp)://!i', $package ) && file_exists( $package ) ) {
				// Local file or remote?
				return $package;
			}

			if ( empty( $package ) ) {
				return new WP_Error( 'no_package', $upgrader->strings['no_package'] );
			}

			$upgrader->skin->feedback( 'downloading_package', esc_html__( 'YITH Repository', 'yith-plugin-upgrade-fw' ) );

			$download_file = $this->download_url( $package, $args );

			// Regenerate update_plugins transient.
			yith_plugin_fw_force_regenerate_plugin_update_transient();

			if ( is_wp_error( $download_file ) ) {
				return new WP_Error( 'download_failed', $upgrader->strings['download_failed'], $download_file->get_error_message() );
			}

			return $download_file;
		}

		/**
		 * Retrieve the temp filename
		 *
		 * @access   protected
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param string $url The package url.
		 * @param string $body The post data fields.
		 * @param int    $timeout Execution timeout (default: 300).
		 * @return string | The temp filename
		 * @see      wp-admin/includes/class-wp-upgrader.php
		 */
		protected function download_url( $url, $body, $timeout = 300 ) {

			// WARNING: The file is not automatically deleted, The script must unlink() the file.
			if ( ! $url ) {
				return new WP_Error( 'http_no_url', esc_html__( 'Invalid URL Provided.', 'yith-plugin-upgrade-fw' ) );
			}

			$tmpfname = wp_tempnam( $url );

			$args = array(
				'timeout'  => $timeout,
				'stream'   => true,
				'filename' => $tmpfname,
				'body'     => $body,
			);

			if ( ! $tmpfname ) {
				return new WP_Error( 'http_no_file', esc_html__( 'Could not create Temporary file.', 'yith-plugin-upgrade-fw' ) );
			}

			$response = wp_safe_remote_get( $url, $args );

			if ( is_wp_error( $response ) ) {
				unlink( $tmpfname );

				return $response;
			}

			$response_code = intval( wp_remote_retrieve_response_code( $response ) );
			// Firstly we check if yithemes gives a 404 error. In this case the upgrade won't check on backup system.
			if ( 404 === $response_code ) {
				unlink( $tmpfname );

				return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );

			} elseif ( 200 !== $response_code ) {
				// If the error code is not 404 but neither a 200 then the upgrade will check on backup system.
				$body = array_merge(
					array(
						'wc-api'      => 'download-api',
						'request'     => 'download'
					),
					$body
				);
				$url = add_query_arg( $body, 'https://casper.yithemes.com' );
				unset( $args['body'] );

				$response = wp_safe_remote_get( $url, $args );

				if ( is_wp_error( $response ) || 200 !== intval( wp_remote_retrieve_response_code( $response ) ) ) {
					// If errors persists also on backup system then we throw an error.
					unlink( $tmpfname );

					return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
				}
			}

			$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

			if ( $content_md5 ) {
				$md5_check = verify_file_md5( $tmpfname, $content_md5 );
				if ( is_wp_error( $md5_check ) ) {
					unlink( $tmpfname );

					return $md5_check;
				}
			}

			return $tmpfname;
		}

		/**
		 * Delete the update plugins transient
		 *
		 * @since      1.0
		 * @author     Francesco Licandro
		 * @param string $plugin The plugin init file.
		 * @return void
		 * @deprecated From version 3.1.12
		 */
		public function force_regenerate_update_transient_on_deactivated( $plugin ) {
			if( isset( $this->plugins[ $plugin ] ) ) {
				delete_site_transient( 'update_plugins' );
			}
		}

		/**
		 * Delete the update plugins transient
		 *
		 * @since      1.0
		 * @author     Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @see        update_plugins transient and pre_set_site_transient_update_plugins hooks
		 * @deprecated From version 3.1.12
		 */
		public function force_regenerate_update_transient() {
			delete_site_transient( 'update_plugins' );
		}

		/**
		 * Check for plugins update
		 *
		 * If a new plugin version is available set it in the pre_set_site_transient_update_plugins hooks
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param mixed $transient update_plugins transient value.
		 * @param bool  $save Default: false. Set true to regenerate the update_transient plugins.
		 * @return mixed $transient | The new update_plugins transient value
		 * @see    update_plugins transient and pre_set_site_transient_update_plugins hooks
		 */
		public function check_update( $transient, $save = false ) {
			foreach ( $this->plugins as $init => $plugin ) {

				$plugin_slug    = $this->plugins[ $init ]['slug'];
				$update_data    = $this->is_update_available( $plugin );
				$is_activated   = YITH_Plugin_Licence()->check( $init );

				$item = array(
					'id'                => $init,
					'plugin'            => $init,
					'slug'              => $plugin_slug,
					'new_version'       => $plugin['info']['Version'],
					'url'               => '',
					'package'           => '',
					'icons'             => array(),
					'banners'           => array(),
					'banners_rtl'       => array(),
					'tested'            => '',
					'requires_php'      => '',
					'compatibility'     => new stdClass(),
				);

				// if not activated disable auto update
				if ( ! $is_activated ) {
					$item['auto-update-forced'] = false;
				}

				if ( ! empty( $update_data ) ) {

					$package      = $is_activated ? $this->package_url : '';
					$wp_version   = preg_replace( '/-.*$/', '', get_bloginfo( 'version' ) );

					if ( strpos( $wp_version, $update_data['tested_up_to'] ) !== false ) {
						$core_updates                = get_core_updates();
						$update_data['tested_up_to'] = false !== $core_updates && ! empty( $core_updates[0]->current ) ? $core_updates[0]->current : $wp_version;
					}

					// Merge default item with the plugin data.
					$item = array_merge(
						$item,
						array(
							'new_version' => (string) $update_data['latest'],
							'changelog'   => (string) $update_data['changelog'],
							'package'     => $package,
							'icons'       => ! empty( $update_data['icons'] ) ? (array) $update_data['icons'] : array(),
							'tested'      => $update_data['tested_up_to'],
						)
					);

					$transient->response[ $init ] = (object) $item;
				} else {
					// Adding the "mock" item to the `no_update` property is required
					// for the enable/disable auto-updates links to correctly appear in UI.
					$transient->no_update[ $init ] = (object) $item;
				}
			}

			if ( $save ) {
				set_site_transient( 'update_plugins', $transient );
			}

			return $transient;
		}

		/**
		 * Check if plugin update is available and return remote info
		 *
		 * @since  4.1.0
		 * @author Francesco Licandro
		 * @param array $plugin The plugin data.
		 * @return mixed
		 */
		protected function is_update_available( $plugin ) {

			$xml        = $this->get_remote_url( $plugin );
			$transient = 'yith_is_update_available_' . md5( $plugin['slug'] );

			$data = get_transient( $transient );

			if ( false === $data || apply_filters( 'yith_is_update_available_delete_transient', false ) ) {
				$remote_xml = wp_remote_get( $xml );

				if ( ! is_wp_error( $remote_xml ) && isset( $remote_xml['response']['code'] ) && 200 === intval( $remote_xml['response']['code'] ) ) {
					$data       = function_exists( 'simplexml_load_string' ) ? @simplexml_load_string( $remote_xml['body'] ) : false;
					$expiration = apply_filters( 'yith_is_update_available_transient_expiration_time', DAY_IN_SECONDS );

					$tested_up_to = (string) str_replace( '.x', '', $data->{'up-to'} );
					$tested_up_to = preg_replace( '/-.*$/', '', $tested_up_to );

					if( ! empty( $data ) ){
						$data = array(
							'latest'       => (string) $data->latest,
							'icons'        => (string) $data->icons,
							'sanitize'     => (string) $data->sanitize,
							'tested_up_to' => (string) $tested_up_to,
							'changelog'    => (string) $data->changelog,
						);
					}

					set_transient( $transient, $data, $expiration );
				}
			}

			if ( $data ) {
				$wrong_current_version_check = version_compare( $plugin['info']['Version'], $data['latest'], '>' );
				$update_available            = version_compare( $data['latest'], $plugin['info']['Version'], '>' );
				if ( ! empty( $data['icons'] ) && ! empty( $data['sanitize'] ) ) {
					$data['icons'] = call_user_func( (string) $data['sanitize'], (string) $data['icons']);
				}
				if ( $update_available || $wrong_current_version_check ) {
					return $data;
				}
			}

			return false;
		}

		/**
		 * Avoid active auto update bulk for plugin that has auto update disabled
		 *
		 * @since 4.1.0
		 * @author Francesco Licandro
		 * @param mixed  $value      New value of the network option.
		 * @param mixed  $old_value  Old value of the network option.
		 * @param string $option     Option name.
		 * @param int    $network_id ID of the network.
		 * @return mixed
		 */
		public function avoid_auto_update_bulk( $value, $old_value, $option, $network_id ) {

			$value = array_filter( $value, function( $p ) {
				return empty( $this->plugins[ $p ] ) || ( is_plugin_active( $p ) && YITH_Plugin_Licence()->check( $p ) );
			});

			return $value;
		}

		/**
		 * Add the plugin update row in plugin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @fire "in_theme_update_message-{$init}" action
		 * @see      after_plugin_row_{$init} action
		 * @see      wp_plugin_update_rows() in wp-single\wp-admin\includes\update.php
		 */
		public function plugin_update_row() {

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

			$current          = get_site_transient( 'update_plugins' );
			$init             = str_replace( 'after_plugin_row_', '', current_filter() );
			$update_now_class = apply_filters( 'yith_plugin_fw_update_now_class', '' );
			$update_now_class = trim( $update_now_class . ' yith-update-link update-link' );

			if ( ! isset( $current->response[ $init ] ) ) {
				return;
			}

			$r            	= $current->response[ $init ];
			$changelog_id 	= str_replace( array( '/', '.php', '.' ), array( '-', '', '-' ), $init );
			$details_url 	= $this->get_view_details_url( $init );

			// If is a multisite and cause the licence ar for blog check if for current blog the licence is active.
			$is_active     = is_multisite() ? YITH_Plugin_Licence()->check( $init ) : ! ! $r->package;
			$wp_list_table = _get_list_table( 'WP_MS_Themes_List_Table' );

			echo '<tr class="plugin-update-tr' . ( is_plugin_active( $init ) ? ' active' : '' ) . '"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">';

			echo '<div class="update-message notice inline notice-warning notice-alt"><p>';

			if ( ! current_user_can( 'update_plugins' ) ) {
				// translators: %1$s, %3$s are placeholders for the plugin name, %2$s the link to open changelog modal, %4$s is the new plugin version.
				printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a>.', 'yith-plugin-upgrade-fw' ), $this->plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->plugins[ $init ]['info']['Name'] ), $r->new_version );
			} elseif ( is_network_admin() ) {
				if( true === $this->is_enabled_in_all_blogs( $init ) ){
					printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a> or <a href="%5$s" class="%6$s" data-plugin="%7$s" data-slug="%8$s" data-name="%1$s">update now</a>.', 'yith-plugin-upgrade-fw' ), $this->plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->plugins[ $init ]['info']['Name'] ), $r->new_version, wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $init, 'upgrade-plugin_' . $init ), $update_now_class, $init, $this->plugins[ $init ]['slug'] );
				}

				else {
					// translators: %1$s, %3$s are placeholders for the plugin name, %2$s the link to open changelog modal, %4$s is the new plugin version.
					printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a>. <em>Make sure the plugin license has been activated on each site of the network to benefits from automatic updates.</em>', 'yith-plugin-upgrade-fw' ), $this->plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->plugins[ $init ]['info']['Name'] ), $r->new_version );
				}

			} elseif ( ! $is_active ) {
				// translators: %1$s, %3$s, %6$s are placeholders for the plugin name, %2$s the link to open changelog modal, %4$s is the new plugin version, %5$s is the link to activation page.
				printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this plugin, please <a href="%5$s" title="License activation">activate</a> your copy of %1$s.</em>', 'yith-plugin-upgrade-fw' ), $this->plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->plugins[ $init ]['info']['Name'] ), $r->new_version, YITH_Plugin_Licence()->get_licence_activation_page_url() );
			} else {
				printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a> or <a href="%5$s" class="%6$s" data-plugin="%7$s" data-slug="%8$s" data-name="%1$s">update now</a>.', 'yith-plugin-upgrade-fw' ), $this->plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->plugins[ $init ]['info']['Name'] ), $r->new_version, wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $init, 'upgrade-plugin_' . $init ), $update_now_class, $init, $this->plugins[ $init ]['slug'] );
			}

			if ( version_compare( $this->plugins[ $init ]['info']['Version'], $r->new_version, '>' ) ) {
				printf( __( '<br/><b>Please note:</b> You are using a higher version than the latest available one. </em>Please, make sure you\'ve downloaded the latest version of <em>%1$s</em> from the only <a href="https://yithemes.com" target="_blank">YITH official website</a>, specifically, from your <a href="https://yithemes.com/my-account/recent-downloads/" target="_blank">Downloads page</a>. This is the only way to be sure the version you are using is 100%% malware-free.', 'yith-plugin-upgrade-fw' ), $this->plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->plugins[ $init ]['info']['Name'] ), $r->new_version, YITH_Plugin_Licence()->get_licence_activation_page_url(), $this->plugins[ $init ]['info']['Name'] );
			}

			echo '</p>';

			/**
			 * Fires at the end of the update message container in each
			 * row of the themes list table.
			 * The dynamic portion of the hook name, `$theme_key`, refers to
			 * the theme slug as found in the WordPress.org themes repository.
			 *
			 * @since WordPress 3.1.0
			 */
			do_action( "in_theme_update_message-{$init}", $this->plugins[ $init ], $r->changelog, $changelog_id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			echo '</div></td></tr>';

			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Remove the standard plugin_update_row
		 * Remove the standard plugin_update_row and Add a custom plugin update row in plugin page.
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @fire "in_theme_update_message-{$init}" action
		 * @see      after_plugin_row_{$init} action
		 */
		public function remove_wp_plugin_update_row() {

			if ( empty( $this->plugins ) ) {
				return;
			}

			foreach ( $this->plugins as $init => $plugin ) {
				remove_action( "after_plugin_row_{$init}", 'wp_plugin_update_row', 10 );
				add_action( "after_plugin_row_{$init}", array( $this, 'plugin_update_row' ) );
			}
		}

		/**
		 * Changelog message
		 *
		 * @param array  $plugin The plugin data.
		 * @param string $changelog The plugin changelog.
		 * @param string $changelog_id The changelog ID.
		 * @param bool   $echo Echo or return.
		 * @return string
		 */
		public function in_theme_update_message( $plugin, $changelog, $changelog_id, $echo = true ) {
			$res = "<div id='{$changelog_id}' class='yit-plugin-changelog-wrapper'>
                    <div class='yit-plugin-changelog'>
                        <h2 class='yit-plugin-changelog-title'>{$plugin['info']['Name']} - Changelog</h2>
                        <p>{$changelog}</p>
                    </div>
                </div>";

			if ( $echo ) {
				echo $res; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				return $res;
			}
		}

		/**
		 * Retrieve the remote url with query string args
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $plugin_info The plugin info array.
		 * @return string the remote url
		 */
		public function get_remote_url( $plugin_info ) {

			$license               = false;
			$is_membership_license = false;
			$slug                  = isset( $plugin_info['slug'] ) ? $plugin_info['slug'] : false;

			if ( function_exists( 'YITH_Plugin_Licence' ) && false !== $slug ) {
				// Get license for YITH Plugins.
				$enabled_license = YITH_Plugin_Licence()->get_licence();

				if ( isset( $enabled_license[ $slug ]['activated'] ) && false !== $enabled_license[ $slug ]['activated'] ) {
					if ( isset( $enabled_license[ $slug ]['licence_key'] ) ) {
						$license = $enabled_license[ $slug ]['licence_key'];
					}

					if ( isset( $enabled_license[ $slug ]['is_membership'] ) ) {
						$is_membership_license = $enabled_license[ $slug ]['is_membership'];
					}
				}
			}

			$args = array(
				'plugin'                => $slug,
				'instance'              => function_exists( 'YITH_Plugin_Licence' ) ? md5( YITH_Plugin_Licence()->get_home_url() ) : md5( $_SERVER['SERVER_NAME'] ),
				'license'               => $license,
				'is_membership_license' => $is_membership_license,
				'server_ip'             => isset( $_SERVER['SERVER_NAME'] ) ? gethostbyname( $_SERVER['SERVER_NAME'] ) : '127.0.0.1',
				'version'               => isset( $plugin_info['info']['Version'] ) ? $plugin_info['info']['Version'] : '1.0.0',
				'locale'                => function_exists( 'get_locale' ) ? get_locale() : 'en_US'
		);

			$args = apply_filters( 'yith_get_remove_url_args', $args );

			return add_query_arg( $args, $this->remote_url );
		}

		/**
		 * Filter the View Details url in update core page
		 *
		 * @param string $url The complete URL including scheme and path.
		 * @param string $path Path relative to the URL. Blank string if no path is specified.
		 * @param string $scheme The scheme to use.
		 *
		 * @return string Admin URL link with optional path appended.
		 *
		 * @autor Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 4.1.15
		 *
		 */
		public function details_plugin_url_in_update_core_page( $url, $path, $scheme ){
			global $pagenow;

			//In plugins.php page use the filter after_plugin_row_{plugin_init} instead.
			if( 'plugins.php' !== $pagenow ){
				$query_args = array();
				parse_str ( parse_url( $url, PHP_URL_QUERY ), $query_args );

				if( ! empty( $query_args['plugin'] ) ){
					$product_id = $query_args['plugin'];
					$transient  = 'yith_update_core_plugins_list';
					$plugins    = get_transient( $transient );

					if( empty( $plugins ) || count( $plugins ) != count( YITH_Plugin_Licence()->get_products() ) ){
						$plugins = array_flip( wp_list_pluck( YITH_Plugin_Licence()->get_products(), 'product_id' ) );
						set_transient( $transient, $plugins, DAY_IN_SECONDS );
					}

					if( isset( $plugins[ $product_id ] ) ){
						$url = $this->get_view_details_url( $plugins[ $product_id ] );
					}
				}
			}

			return $url;
		}

		/**
		 * Get the view details url for premium plugins
		 *
		 * @param $init string the YITH premium plugin init file
		 *
		 * @return string view details url for YITH Premium plugins
		 *
		 * @autor Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 4.1.15
		 */
		public function get_view_details_url( $init ){
			$url = admin_url( 'admin-ajax.php?action=yith_plugin_fw_get_premium_changelog&tab=plugin-information&plugin=' . $init . '&section=changelog&TB_iframe=true&width=640&height=662' );
			return $url;
		}

		/**
		 * Remove the standard plugin_update_row
		 * Remove the standard plugin_update_row and Add a custom plugin update row in plugin page.
		 *
		 * @since 4.1.15
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return bool true is the plugin is enabled in all blogs, false otherwise.
		 * @fire "in_theme_update_message-{$init}" action
		 * @see      after_plugin_row_{$init} action
		 */
		public function is_enabled_in_all_blogs( $plugin_init ) {
			$enabled_in_all_blogs = false;
			if ( function_exists( 'YITH_Plugin_Licence' ) ) {
				$license_information  = is_multisite() ? YITH_Plugin_Licence()->get_global_license_transient() : YITH_Plugin_Licence()->get_licence();
				if( ! empty( $license_information ) ){
					$slug = ! empty( $this->plugins[ $plugin_init ]['slug'] ) ? $this->plugins[ $plugin_init ]['slug'] : '';
					if( ! empty( $slug ) && ! empty( $license_information[ $slug ] ) ){
						if( is_multisite() ){
							$enabled_in_all_blogs = true;
						}

						else {
							$enabled_in_all_blogs = ! empty( $license_information[ $slug ]['activated'] );
						}
					}
				}
			}
			return $enabled_in_all_blogs;
		}

		/**
		 * Fix the view details url in plugins.php page.
		 * Prevent to update the plugins in update-core page if not enabled in all networks
		 *
		 * @return mixed $update_plugins filtered transient value
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 4.1.15
		 * @see site_transient_update_plugins filter
		 */
		public function filter_site_transient_update_plugins( $update_plugins ) {
			global $pagenow;
			if ( 'plugins.php' === $pagenow || 'update-core.php' === $pagenow ) {
				$yith_plugins = array_keys( YITH_Plugin_Licence()->get_products() );
				foreach ( $yith_plugins as $init ) {

					if ( 'plugins.php' === $pagenow && isset( $update_plugins->response[ $init ]->slug ) ) {
						unset( $update_plugins->response[ $init ]->slug );
					}

					elseif ( 'update-core.php' === $pagenow && isset( $update_plugins->response[ $init ] ) && ! $this->is_enabled_in_all_blogs( $init ) ) {
						unset( $update_plugins->response[ $init ] );
					}
				}
			}
			return $update_plugins;
		}
	}
}

if ( ! function_exists( 'YITH_Plugin_Upgrade' ) ) {
	/**
	 * Main instance of plugin
	 *
	 * @since  1.0
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @return YITH_Plugin_Upgrade
	 */
	function YITH_Plugin_Upgrade() { // phpcs:ignore
		return YITH_Plugin_Upgrade::instance();
	}
}

YITH_Plugin_Upgrade();
