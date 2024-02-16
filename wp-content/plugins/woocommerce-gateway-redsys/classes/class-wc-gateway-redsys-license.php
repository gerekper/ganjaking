<?php
/**
 * Class WC_Gateway_Redsys_License
 *
 * @package WooCommerce Redsys Gateway
 * @since 24.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	/**
	 * Licensing class for License Manager requests
	 *
	 * @version 0.0.1
	 */
class WC_Gateway_Redsys_License {

	private $api_url                  = '';
	private $api_data                 = array();
	private $slug                     = '';
	private $name                     = '';
	private $version                  = '';
	private $wp_override              = false;
	private $prefix                   = '';
	private $hide_menu_after_activate = true;

	/**
	 * Class constructor.
	 *
	 * @uses trailingslashit()
	 * @uses plugin_basename()
	 * @uses wp_spaces_regexp()
	 * @uses init()
	 *
	 * @param string $_api_url     The URL pointing to the custom API endpoint.
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array  $_api_data    Optional data to send with API calls.
	 */
	public function __construct( $_api_url, $_plugin_file, $_api_data = null ) {
		$this->api_data    = $_api_data;
		$this->api_url     = trailingslashit( $_api_url );
		$this->slug        = plugin_basename( $_plugin_file );
		$this->plugin_file = $_plugin_file;
		$this->name        = basename( $_plugin_file, '.php' );
		$this->version     = $_api_data['version'];
		$this->wp_override = isset( $_api_data['wp_override'] ) ? (bool) $_api_data['wp_override'] : false;
		$this->item_name   = ! empty( $_api_data['item_name'] ) ? $_api_data['item_name'] : '';
		$this->menu_slug   = ! empty( $_api_data['menu_slug'] ) ? $_api_data['menu_slug'] : '';
		$this->menu_title  = ! empty( $_api_data['menu_title'] ) ? $_api_data['menu_title'] : '';
		$this->prefix      = ! empty( $_api_data['prefix'] ) ? $_api_data['prefix'] : '';

		// Set up hooks.
		$this->init();
	}
	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_action()
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'admin_menu', array( $this, 'license_menu' ), 10 );
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 9999, 1 );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 9999, 3 );
		add_action( "in_plugin_update_message-{$this->slug}", array( $this, 'in_plugin_update_message' ) );
		add_filter( 'upgrader_pre_install', array( $this, 'upgrader_pre_install' ), 10, 2 );
	}
	/**
	 * Render Admin Notice with Activation Link
	 */
	public function admin_notice() {

		$license = get_option( $this->prefix . '_license_key' );

		if ( ! empty( $license ) ) {
			return;
		}

		$plugin_data = get_plugin_data( $this->plugin_file );

		$plugin_title = ! empty( $plugin_data['Name'] ) ? $plugin_data['Name'] : '';

			ob_start(); ?>
				<div id="message" class="error">
				<p><strong><?php echo esc_html( $plugin_title ); ?> v<?php echo esc_html( $this->version ); ?></strong><?php esc_html_e( 'plugin almost ready. You must enter valid', 'woocoomerce-redsys' ); ?> <a href="<?php echo get_admin_url() . 'admin.php?page=' . $this->menu_slug; ?>"><?php esc_html_e( 'License Key', 'woocoomerce-redsys' ); ?></a> <?php esc_html_e( 'for it to work.', 'woocoomerce-redsys' ); ?></p>
				</div>
				<?php
				$new_content = ob_get_contents();

				ob_end_clean();

				echo $new_content;
	}
	/**
	 * Clear transients
	 *
	 * @param mixed $return return value.
	 * @param mixed $plugin plugin data.
	 * @return mixed
	 */
	public function upgrader_pre_install( $return, $plugin ) {
		if ( is_wp_error( $return ) ) { // Bypass.
			return $return;
		}

		if ( isset( $plugin['plugin'] ) && $this->slug === $plugin['plugin'] ) {
			delete_site_transient( md5( $this->slug . 'plugin_update_info' ) );
		}

		return $return;
	}
	/**
	 * Add submenu to Plugins with license settings
	 *
	 * @uses get_option()
	 * @uses add_plugins_page()
	 */
	public function license_menu() {
		// There is the checking options for activating
		// for handler to show|hide activation menu after
		// complete the activation ( variable $add_menu ).
		$license = get_option( $this->prefix . '_license_key' );
		$status  = get_option( $this->prefix . '_license_status' );
		add_submenu_page(
			'woocommerce',
			$this->menu_title,
			$this->menu_title,
			'manage_options',
			$this->menu_slug,
			array( $this, 'license_page' )
		);
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}
	/**
	 * Render HTML for License options page
	 */
	public function license_page() {
		$license = get_option( $this->prefix . '_license_key' );
		$license = empty( $license ) ? '' : $license;

		$license = ! empty( $_POST[ "{$this->prefix}_license_key" ] ) ? $_POST[ "{$this->prefix}_license_key" ] : $license;

		$message = $this->activate_license();

		$status = get_option( $this->prefix . '_license_status' );

		?>
			<div class="wrap">
			<h2>
			<?php
			// translators: %s: plugin name.
			printf( esc_html__( '%s License Activation', 'woocommerce-redsys' ), $this->menu_title );
			?>
				</h2>
	
			<?php if ( ! empty( $message ) ) { ?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
			}

			if ( ! empty( $license ) && ! empty( $status ) && 'valid' === $status ) {
				?>
				<div class="updated">
					<p><?php esc_html_e( 'License is Active' ); ?></p>
				</div>
			<?php } ?>
	
			<form method="post" action="">
				<input type="hidden" name="page" value="<?php echo $this->menu_slug; ?>">
				<?php wp_nonce_field( "{$this->prefix}_license_activation", md5( "{$this->prefix}_license_activation" . $this->menu_slug . get_current_user() ) ); ?>
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
						<?php esc_html_e( 'License Key' ); ?>
						</th>
						<td>
							<input id="license_key" name="<?php echo $this->prefix; ?>_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="license_key"><?php _e( 'Enter your license key' ); ?></label>
						</td>
					</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Activate License' ) ); ?>
	
			</form>
			<?php
	}
	/**
	 * Activate license process
	 * request to the marketplace
	 */
	public function activate_license() {
		// listen for our activate button to be clicked.
		if ( ! empty( $_POST[ "{$this->prefix}_license_key" ] ) ) {

			// run a quick security check.
			if ( ! check_admin_referer( "{$this->prefix}_license_activation", md5( "{$this->prefix}_license_activation" . $this->menu_slug . get_current_user() ) ) ) {
				return 'Error #10010: Wrong nonce'; // get out if we didn't click the Activate button.
			}

			// retrieve the license from the database.
			$license = $_POST[ "{$this->prefix}_license_key" ];
			update_option( $this->prefix . '_license_key', $license );

			$url    = get_site_url( get_current_blog_id() );
			$domain = strtolower( rawurlencode( rtrim( $url, '/' ) ) );

			// data to send in our API request.
			$api_params = array(
				'action'    => 'activate_license',
				'license'   => $license,
				'item_name' => rawurlencode( $this->item_name ), // the name of our product in EDD.
				'url'       => home_url(),
				'blog_id'   => get_current_blog_id(),
				'site_url'  => $url,
				'domain'    => $domain,
			);

			$this->api_url = add_query_arg( 'wc-api', 'lm-license-api', $this->api_url );

			$args = array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'sslverify'   => false,
				'headers'     => array(),
				'body'        => $api_params,
				'cookies'     => array(),
			);

			// Call the custom API Without SSL checking.
			$response = wp_remote_post( $this->api_url, $args );

			if ( is_wp_error( $response ) ) {
				// With SSL checking.
				$args['sslverify'] = true;
				$response          = wp_remote_post( $this->api_url, $args );

				if ( is_wp_error( $response ) ) {
					$message = 'Error #10020: ' . $response->get_error_message();
				}
			}

			// Can set debug mode by $_GET "activation_debug" by "true".
			if ( isset( $_GET['activation_debug'] ) && 'true' == $_GET['activation_debug'] ) {
				var_dump( $args );
				var_dump( $response );
				exit;
			}

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$message = 'Error #10030: Something went wrong';
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( ! isset( $license_data->activated ) || false === $license_data->activated ) {
				$message = ! empty( $license_data->error ) ? $license_data->error : 'Error #10040: Can not connect to the server!';
			}

			// If error was triggered.
			if ( ! empty( $message ) ) {
				return $message;
			}

			// If error not triggered update license option and redirect after activation.
			update_option( $this->prefix . '_license_status', $license_data->license );
			update_option( $this->prefix . '_license_salt', $license_data->salt );

			// if $this->hide_menu_after_activate == false redirect to current page.
			if ( ! $this->hide_menu_after_activate ) {
				$redirect = admin_url( 'plugins.php?page=' . $this->menu_slug );
			} else {
				$redirect = $this->successful_activation_redirect();
			}

			wp_redirect( $redirect );
			exit();

		}

		return '';
	}
	/**
	 * Redirect after successful activation
	 *
	 * @return string
	 */
	public function successful_activation_redirect() {
		return admin_url() . 'plugins.php';
	}
	/**
	 * Check for Updates by request to the marketplace
	 * and modify the update array.
	 *
	 * @param array $transient plugin update array build by WordPress.
	 * @return stdClass modified plugin update array.
	 */
	public function check_update( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// if response for current product isn't empty check for override.
		if ( ! empty( $transient->response ) && ! empty( $transient->response[ $this->slug ] ) && false === $this->wp_override ) {
			return $transient;
		}

		$license       = get_option( $this->prefix . '_license_key' );
		$salt          = get_option( $this->prefix . '_license_salt' );
		$plugin_update = false;

		$version_info = $this->api_request(
			'plugin_latest_version',
			array(
				'license'         => $license,
				'item_name'       => $this->item_name,
				'slug'            => $this->slug,
				'current_version' => $this->version,
				'salt'            => $salt,
			)
		);

		$version = $version_info->new_version;
		if ( version_compare( REDSYS_VERSION, $version, '<' ) ) {
			$plugin_update = true;
			$plugin_id     = $version_info->id;
			$plugin_slug   = $version_info->slug;
			$plugin        = $version_info->plugin;
			$url           = $version_info->url;
			$package_url   = $version_info->package;
		}

		if ( $plugin_update ) {
			$transient->response[ $plugin ] = (object) array(
				'id'          => $plugin_id,
				'slug'        => $plugin_slug,
				'plugin'      => $plugin,
				'new_version' => $version,
				'url'         => $url,
				'package'     => $package_url,
			);
		}
		return $transient;
	}
	/**
	 * Updates information on the "View version x.x details" popup with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed  $_data   Plugin data.
	 * @param string $_action The type of information being requested from the Plugin Install API.
	 * @param object $_args   Plugin API arguments.
	 * @return object $_data  Plugin data.
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		// by default $data = false (from WordPress).

		if ( 'plugin_information' !== $_action ) {
			return $_data;
		}

		$slug = explode( '/', $this->slug );
		$slug = $slug[0];

		if ( ! isset( $_args->slug ) || ( $_args->slug != $slug ) ) {
			return $_data;
		}

		$license = get_option( $this->prefix . '_license_key' );
		$salt    = get_option( $this->prefix . '_license_salt' );
		$to_send = array(
			'license' => $license,
			'salt'    => $salt,
			'slug'    => $this->slug,
			'is_ssl'  => is_ssl(),
			'fields'  => array(
				'banners' => false, // These will be supported soon hopefully.
				'reviews' => false,
			),
		);

		$cache_key = 'api_request_' . substr( md5( serialize( $this->item_name ) ), 0, 15 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize

		// Get the transient where we store the api request for this plugin for 24 hours.
		$api_request_transient = get_site_transient( $cache_key );

		// If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
		if ( empty( $api_request_transient ) ) {

			$api_response = $this->api_request( 'plugin_information', $to_send );
			if ( ! empty( $api_response->sections ) ) {
				$api_response->sections = (array) $api_response->sections;
			}

			// Expires in 1 day.
			set_site_transient( $cache_key, $api_response, DAY_IN_SECONDS );

			$_data = $api_response;
		} else {
			$_data = $api_request_transient;
		}

		return $_data;
	}
	/**
	 * Function for major updates
	 *
	 * @param array $args plugin data.
	 * @return void
	 */
	public function in_plugin_update_message( $args ) {

		$license = get_option( $this->prefix . '_license_key' );
		$salt    = get_option( $this->prefix . '_license_salt' );

		$transient_name         = md5( $this->slug . 'plugin_update_info' );
		$transient_version_info = get_site_transient( $transient_name );
		if ( empty( $transient_version_info ) ) {
			$version_info                          = $this->api_request(
				'plugin_latest_version',
				array(
					'license'         => $license,
					'slug'            => $this->slug,
					'current_version' => $this->version,
					'salt'            => $salt,
				)
			);
			$this->update_requested[ $this->slug ] = $version_info;
			set_site_transient( $transient_name, $version_info, 12 * HOUR_IN_SECONDS );
		} else {
			$version_info = $transient_version_info;
		}

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
			// show update version block if new version > then current.
			if ( version_compare( $this->version, $version_info->new_version, '<' ) && ! empty( $version_info->is_major ) ) {

				$upgrade_notice = '<span class="' . esc_attr( $this->name ) . '_plugin_upgrade_notice"> ';

				if ( ! empty( $version_info->major_log ) ) {
					$upgrade_notice .= $version_info->major_log;
				} else {
					$upgrade_notice .= "{$version_info->new_version} is a major update, and we highly recommend creating a full backup of your site before updating. ";
				}

				$upgrade_notice .= '</span>';

				echo '<style type="text/css">
						.' . esc_attr( $this->name ) . '_plugin_upgrade_notice {
							font-weight: 400;
							color: #fff;
							background: #d53221;
							padding: 1em;
							margin: 9px 0;
							display: block;
							box-sizing: border-box;
							-webkit-box-sizing: border-box;
							-moz-box-sizing: border-box;
						}
						.' . esc_attr( $this->name ) . '_plugin_upgrade_notice:before {
							content: "\f348";
							display: inline-block;
							font: 400 18px/1 dashicons;
							speak: none;
							margin: 0 8px 0 -2px;
							-webkit-font-smoothing: antialiased;
							-moz-osx-font-smoothing: grayscale;
							vertical-align: top;
						}
					</style>' . wp_kses_post( $upgrade_notice );
			}
		}
	}
	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array  $args  arguments.
	 * @param string $url   url.
	 * @return array $array arguments.
	 */
	public function http_request_args( $args, $url ) {
		// If it is an https request and we are performing a package download, disable ssl verification.
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'action=package_download' ) ) {
			$args['sslverify'] = false;
		}
		return $args;
	}
	/**
	 * Extends the download URL with parameters needed for the API call.
	 *
	 * @uses get_site_url()
	 * @uses get_current_blog_id()
	 * @uses add_query_arg()
	 *
	 * @param string $download_url The download URL.
	 * @param array  $data         Plugin data.
	 * @return string
	 */
	private function extend_download_url( $download_url, $data ) {

		$url    = get_site_url( get_current_blog_id() );
		$domain = strtolower( rawurlencode( rtrim( $url, '/' ) ) );
		$salt   = get_option( $this->prefix . '_license_salt' );

		$api_params = array(
			'action'    => 'get_last_version',
			'license'   => ! empty( $data['license'] ) ? $data['license'] : '',
			'item_name' => rawurlencode( $this->item_name ),
			'blog_id'   => get_current_blog_id(),
			'site_url'  => rawurlencode( $url ),
			'domain'    => rawurlencode( $domain ),
			'slug'      => rawurlencode( $data['slug'] ),
			'salt'      => $salt,
		);

		$download_url = add_query_arg( $api_params, $download_url );

		return $download_url;
	}
	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 * @uses extend_download_url()
	 *
	 * @param string $_action The requested action.
	 * @param array  $_data   Parameters for the API action.
	 * @return false|object
	 */
	private function api_request( $_action, $_data ) {

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug ) {
			return false;
		}

		if ( $this->api_url == trailingslashit( home_url() ) ) {
			return false; // Don't allow a plugin to ping itself
		}

		$url    = get_site_url( get_current_blog_id() );
		$domain = strtolower( rawurlencode( rtrim( $url, '/' ) ) );

		$api_params = array(
			'action'       => $_action,
			'license'      => ! empty( $data['license'] ) ? $data['license'] : '',
			'salt'         => ! empty( $data['salt'] ) ? $data['salt'] : '',
			'item_name'    => rawurlencode( $this->item_name ),
			'item_version' => ! empty( $data['current_version'] ) ? $data['current_version'] : '',
			'blog_id'      => get_current_blog_id(),
			'site_url'     => $url,
			'domain'       => $domain,
			'slug'         => $data['slug'],
		);

		$this->api_url = add_query_arg( 'wc-api', 'upgrade-api', $this->api_url );
		$request       = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		$request = json_decode( wp_remote_retrieve_body( $request ) );

		if ( ! empty( $request->package ) ) {
			$request->package = $this->extend_download_url( $request->package, $data );
		}

		if ( ! empty( $request->download_link ) ) {
			$request->download_link = $this->extend_download_url( $request->download_link, $data );
		}

		if ( 'plugin_information' == $_action ) {
			if ( $request && isset( $request->sections ) ) {
				$request->sections = maybe_unserialize( $request->sections );
			} else {
				$request = new WP_Error(
					'plugins_api_failed',
					sprintf(
					/* translators: %s: support forums URL */
						__( 'An unexpected error occurred. Something may be wrong with ' . $this->api_url . ' or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
						__( 'https://wordpress.org/support/' )
					),
					wp_remote_retrieve_body( $request )
				);
			}
		}

		return $request;
	}

	// end class.
}

