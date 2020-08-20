<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooThemes Updater - Plugins/Themes Updater Class
 *
 * The WooThemes Updater - plugins/theme updater class.
 *
 * @package WordPress
 * @subpackage WooThemes Updater
 * @category Core
 * @author WooThemes
 * @since 1.5.0
 */
class WooThemes_Updater_Update_Checker {
	/**
	 * URL of endpoint to check for product/changelog info
	 * @var string
	 */
	private $api_url = 'https://woocommerce.com/wc-api/woothemes-installer-api';

	/**
	 * URL of endpoint to check for updates
	 * @var string
	 */
	private $update_check_url = 'https://woocommerce.com/wc-api/update-check';

	/**
	 * Array of plugins info
	 * @var array
	 */
	private $plugins; // 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version

	/**
	 * Array of themes info
	 * @var array
	 */
	private $themes; // 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version

	/**
	 * Array of errors during update checks
	 * @var array
	 */
	private $errors = null;

	/**
	 * Plugin version
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct ( $plugins, $themes ) {
		global $woothemes_updater;
		$this->version = $woothemes_updater->version;
		$this->plugins = $plugins;
		$this->themes = $themes;
		$this->init();
	} // End __construct()

	/**
	 * Initialise the update check process.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function init () {
		// Check For Updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'plugin_update_check' ), 20, 1 );
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'theme_update_check' ), 20, 1 );

		// Check For Plugin Information
		add_filter( 'plugins_api', array( $this, 'plugin_information' ), 20, 3 );

		add_action( 'upgrader_process_complete', array( $this, 'after_update' ), 10, 2 );

		// Clear the cache when a force update is done via WP
		if ( isset( $_GET['force-check'] ) && 1 == $_GET['force-check'] ) {
			$this->clean_cache();
		}
	} // End init()

	/**
	 * Called the WordPress update process finishes and used
	 * to clear WooCommerce Helper cache when a plugin or theme
	 * is updated to avoid showing updates for already updated
	 * products.
	 *
	 * @param WP_Upgrader $upgrader_object
	 * @param array $options
	 * @return void
	 */
	public function after_update( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && in_array( $options['type'], array( 'plugin', 'theme' ) ) )  {
			$this->clean_cache();
		}
	}

	/**
	 * Clear cache by deleting the transient used to store
	 * information about installed Woo themes and plugins.
	 *
	 * @return void
	 */
	public function clean_cache() {
		delete_transient( 'woothemes_helper_updates' );
	}

	/**
	 * Make a call to WooCommerce.com and fetch update info for all products and put in transient for 30min
	 * @return bool|array
	 */
	public function fetch_remote_update_data() {
		global $woothemes_updater;
		$plugins_to_fetch_updates_for = array();

		// Loop through all WooCommerce plugins/extensions
		foreach ( $this->plugins as $plugin ) {
			// $plugin - 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version
			// Always fetch all plugins data in one call, we loop to append the url
			$plugin[] = esc_url( home_url( '/' ) );
			$plugins_to_fetch_updates_for[] = $plugin;
		}

		$themes_to_check_updates_for = array();
		// Loop through all WooCommerce themes
		foreach ( $this->themes as $theme ) {
			// $theme - 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version
			// Always fetch all theme data in one call, we loop to append the url
			$theme[0] = str_replace( '/style.css', '', $theme[0] );
			$theme[] = esc_url( home_url( '/' ) );
			$themes_to_check_updates_for[] = $theme;
		}

		$helper_update_info = array( plugin_basename( $woothemes_updater->file ), $this->version );

		// Make sure we have data to check for updates
		if ( empty( $plugins_to_fetch_updates_for ) && empty( $helper_update_info ) && empty( $themes_to_check_updates_for ) ) {
			return false;
		}

		$args = array();
		if ( ! empty( $plugins_to_fetch_updates_for ) ) {
			$args['plugins'] = $plugins_to_fetch_updates_for;
		}
		if ( ! empty( $themes_to_check_updates_for ) ) {
			$args['themes'] = $themes_to_check_updates_for;
		}
		if ( ! empty( $helper_update_info ) ) {
			$args['helper'] = $helper_update_info;
		}

		// We store the update data in a cache for 5 minutes, to avoid multiple calls to WooCommerce.com as
		// this transient filter fires multiple times when checking for updates in WP. Cache can be cleared
		// by using the check for updates button on the core updates page in WP
		if ( FALSE == $response = get_transient( 'woothemes_helper_updates' ) ) {
			$response = $this->request( json_encode( $args ), 'updates' );
			set_transient( 'woothemes_helper_updates', $response, 5 * MINUTE_IN_SECONDS );
		}
		return $response;
	} // End fetch_remote_update_data()


	/**
	 * Inject plugin updates into update_plugins transient
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  object $transient
	 * @return object $transient
	 */
	public function plugin_update_check ( $transient ) {
		$response = $this->fetch_remote_update_data();

		if ( FALSE == $response ) {
			return $transient;
		}

		// Set plugin update info into transient
		if ( isset( $response->plugins ) ) {
			$activated_products = get_option( 'woothemes-updater-activated', array() );
			foreach ( $response->plugins as $plugin_key => $plugin ) {
				if ( isset( $plugin->no_update ) ) {
					if ( isset( $plugin->license_expiry_date ) ) {
						$activated_products[ $plugin_key ][3] = $plugin->license_expiry_date;
					}
					$transient->no_update[ $plugin_key ] = $plugin;

					// Make sure we have a slug, and that the value reflects the directory name for each plugin only.
					if ( isset( $transient->no_update[$plugin_key]->slug ) ) {
						$transient->no_update[$plugin_key]->slug = dirname( $transient->no_update[$plugin_key]->slug );
					} else {
						$transient->no_update[$plugin_key]->slug = dirname( $plugin_key );
					}
				// Deactivate a product
				} elseif ( isset( $plugin->deactivate ) ) {
					$this->errors[] = $plugin->deactivate;
					global $woothemes_updater;
					$woothemes_updater->admin->deactivate_product( $plugin_key, true );
				// If there is an error returned, log that no update is available.
				} elseif ( isset( $plugin->error ) ) {
					$this->errors[] = $plugin->error;
					$transient->no_update[ $plugin_key ] = $plugin;
				// If there is a new version, check the license expiry date and update it locally.
				} elseif ( isset( $plugin->new_version ) && ! empty( $plugin->new_version ) ) {
					if ( isset( $plugin->license_expiry_date ) ) {
						$activated_products[ $plugin_key ][3] = $plugin->license_expiry_date;
						unset( $plugin->license_expiry_date );
					}
					$transient->response[ $plugin_key ] = $plugin;
				} else {
					if ( isset( $plugin->license_expiry_date ) ) {
						$activated_products[ $plugin_key ][3] = $plugin->license_expiry_date;
					}
					$transient->no_update[ $plugin_key ] = $plugin;
				}

				// Make sure we have a slug, and that the value reflects the directory name for each plugin only.
				if ( isset( $transient->response[$plugin_key]->slug ) ) {
					$transient->response[$plugin_key]->slug = dirname( $transient->response[$plugin_key]->slug );
				} else {
					if ( '' != $plugin_key && isset( $transient->response[$plugin_key] ) ) {
						$transient->response[$plugin_key]->slug = dirname( $plugin_key );
					}
				}
			}

			update_option( 'woothemes-updater-activated', $activated_products );
		}

		// Set WooCommerce Helper update info into transient
		if ( isset( $response->helper ) ) {
			foreach ( $response->helper as $plugin_key => $plugin ) {
				if ( isset( $plugin->no_update ) ) {
					$transient->no_update[ $plugin_key ] = $plugin;
				} elseif ( isset( $plugin->error ) ) {
					$this->errors[] = $plugin->error;
					$transient->no_update[ $plugin_key ] = $plugin;
				} elseif ( isset( $plugin->new_version ) && ! empty( $plugin->new_version ) ) {
					$transient->response[ $plugin_key ] = $plugin;
				} else {
					$transient->no_update[ $plugin_key ] = $plugin;
				}
			}
		}

		// Check if we must output error messages
		if ( count( $this->errors ) > 0 ) {
			add_action( 'admin_notices', array( $this, 'error_notices') );
		}

		return $transient;
	} // End plugin_update_check()

	/**
	 * Inject theme updates into update_themes transient
	 *
	 * @access public
	 * @since  1.5.0
	 * @param  object $transient
	 * @return object $transient
	 */
	public function theme_update_check( $transient ) {
		$response = $this->fetch_remote_update_data();

		if ( FALSE == $response ) {
			return $transient;
		}

		if ( isset( $response->themes ) ) {
			$activated_products = get_option( 'woothemes-updater-activated', array() );
			foreach ( $response->themes as $theme_key => $theme ) {
				if ( isset( $theme->new_version ) ) {
					if ( isset( $theme->license_expiry_date ) ) {
						$activated_products[ $theme_key . '/style.css' ][3] = $theme->license_expiry_date;
					}
					$transient->response[ $theme_key ]['new_version'] = $theme->new_version;
		        	$transient->response[ $theme_key ]['url'] = 'http://woocommerce.com/';
		        	$transient->response[ $theme_key ]['package'] = $theme->package;
				} elseif ( isset( $theme->error ) ) {
					$this->errors[] = $theme->error;
				} elseif ( isset( $theme->deactivate ) ) {
					$this->errors[] = $theme->deactivate;
					global $woothemes_updater;
					$woothemes_updater->admin->deactivate_product( $theme_key . '/style.css', true );
				} else {
					if ( isset( $theme->license_expiry_date ) ) {
						$activated_products[ $theme_key . '/style.css' ][3] = $theme->license_expiry_date;
					}
				}
			}
			update_option( 'woothemes-updater-activated', $activated_products );
		}

		// Check if we must output error messages
		if ( count( $this->errors ) > 0 ) {
			add_action( 'admin_notices', array( $this, 'error_notices') );
		}

		return $transient;
	} // End theme_update_check()

	/**
	 * Display an error notice
	 * @param  strin $message The message
	 * @return void
	 */
	public function error_notices () {
		if ( isset( $this->errors ) && count( $this->errors ) ) {
			$messages = array();
			foreach ( $this->errors as $error ) {
				$messages[] = '<p>' . $error . '</p>';
			}
			echo '<div id="message" class="error">' . implode( '', $messages ) . '</div>';
			$this->errors = null;
		}
	} // End error_notices()

	/**
	 * Check for the plugin's data against the remote server.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return object $response
	 */
	public function plugin_information ( $false, $action, $args ) {
		$transient = get_site_transient( 'update_plugins' );
		$found = false;
		$found_plugin = array();

		// Make a slug is set
		if ( ! isset( $args->slug ) ) {
			return $false;
		}

		// Loop through all woo plugins
		foreach ( $this->plugins as $plugin ) {
			// $plugin - 0=file, 1=product_id, 2=file_id, 3=license_hash
			$plugin_slug = dirname( $plugin[0] );

			// Check if this plugins API is about one of the woo plugins
			if ( ( $args->slug == $plugin_slug || $args->slug == $plugin[0] ) && isset( $transient->checked[ $plugin[0] ] ) ) {
				$found = true;
				$found_plugin = $plugin;
			}
		}

		// If the plugin info is not about any of our plugins, bail!
		if ( ! $found ) {
			return $false;
		}

		// POST data to send to your API
		$args = array(
			'request' => 'plugininformation',
			'plugin_name' => $found_plugin[0],
			'version' => $transient->checked[ $found_plugin[0] ],
			'product_id' => $found_plugin[1],
			'file_id' => $found_plugin[2],
			'license_hash' => $found_plugin[3],
			'url' => esc_url( home_url( '/' ) )
		);

		// Send request for detailed information
		$response = $this->request( $args );

		$response->sections = (array)$response->sections;

		// Make sure we have the changelog set, if not try to populate via changelog file
		if ( ! isset( $response->sections['changelog'] ) ) {
			$changelog_url = '';
			if ( isset( $response->changelog_url ) ) {
				$changelog_url = esc_url( $response->changelog_url );
			} else {
				$slug = explode( '/', $args['plugin_name'] );
				if ( isset( $slug[0] ) ) {
					$slug = sanitize_title( $slug[0] );
					$changelog_url = 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $slug . '/changelog.txt';
				}
			}
			if ( '' != $changelog_url ) {
				$changelog_content = wp_remote_get( $changelog_url );
				if ( ! is_wp_error( $changelog_content ) ) {
					$changelog_content = wp_remote_retrieve_body( $changelog_content );
					$changelog_lines = explode( "\n", $changelog_content );
					$changelog_html = '';
					foreach ( $changelog_lines as $line ) {
						$changelog_html .= $this->parse_changelog_line_to_html( $line );
					}
					$response->sections['changelog'] = $changelog_html;
				} else {
					$response->sections['changelog'] = "<p>Sorry, there was a problem fetching the changelog details: " . $changelog_content->get_error_message() . "</p>";
				}
			} else {
				$response->sections['changelog'] = "<p>Sorry, there was a problem fetching the changelog details, please try again.</p>";
			}
		}

		$response->compatibility = (array)$response->compatibility;
		$response->tags = (array)$response->tags;
		$response->contributors = (array)$response->contributors;

		if ( count( $response->compatibility ) > 0 ) {
			foreach ( $response->compatibility as $k => $v ) {
				$response->compatibility[$k] = (array)$v;
			}
		}

		// Set a nice banner if one not provided
		if ( ! isset( $response->banners ) ) {
			$response->banners['low'] = '//woothemess3.s3.amazonaws.com/wp-updater-api/official-wc-extension-1544.png';
			$response->banners['high'] = '//woothemess3.s3.amazonaws.com/wp-updater-api/official-wc-extension-1544.png';
		}

		return $response;
	} // End plugin_information()

	/**
	 * Generic request helper.
	 *
	 * @access private
	 * @since  1.0.0
	 * @param  array $args
	 * @return object $response or boolean false
	 */
	protected function request ( $args, $api = 'info' ) {
		// Send request
		$request = wp_remote_post( ( $api == 'info' ) ? $this->api_url : $this->update_check_url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'headers' => array( 'user-agent' => 'WooThemesUpdater/' . $this->version ),
			'body' => $args,
			'sslverify' => false
			) );
		// Make sure the request was successful
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			trigger_error( __( 'An unexpected error occurred. Something may be wrong with WooCommerce.com or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://support.woothemes.com/hc/en-us">help center</a>.', 'woothemes-updater' ) . ' ' . __( '(WordPress could not establish a secure connection to WooCommerce.com. Please contact your server administrator.)', 'woothemes-updater' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
			return false;
		}
		// Read server response, which should be an object
		if ( $request !== '' ) {
			$response = json_decode( wp_remote_retrieve_body( $request ) );
		} else {
			$response = false;
		}

		if ( is_object( $response ) && isset( $response->payload ) ) {
			return $response->payload;
		} else {
			// Unexpected response
			return false;
		}
	} // End request()

	/**
	 * Parse changelog lines and convert to html
	 *
	 * @since  1.5.0
	 * @param  string $text plain text string
	 * @return string html version of the plain text string
	 */
	public function parse_changelog_line_to_html( $text ) {
		// Skip heading
		if ( '***' == substr( $text, 0, 3 ) ) {
			return '';
		}

		// Check for date and version
		if ( '20' == substr( $text, 0, 2 ) ) {
			return '<h4>' . $text . '</h4>';
		}

		// Check if listitem
		if ( ' * ' == substr( $text, 0, 3 ) || '* ' == substr( $text, 0, 2 ) ) {
			return '<li>' . trim( $text, ' * ' ) . '</li>';
		}

		return $text;
	} // End parse_changelog_line_to_html()

} // End Class
?>
