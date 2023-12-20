<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
// Uncomment this line for testing
// set_site_transient('update_plugins', null);

/**
 * @author John Turner
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows plugins to use their own update API.
 *
 */
class SeedProd_Updater {

	/**
	 * Plugin name.
	 *
	 * @var boolean|string
	 */
	public $plugin_name = false;

	/**
	 * Plugin slug.
	 *
	 * @var boolean|string
	 */
	public $plugin_slug = false;

	/**
	 * Plugin path.
	 *
	 * @var boolean|string
	 */
	public $plugin_path = false;

	/**
	 * URL of the plugin.
	 *
	 * @var boolean|string
	 */
	public $plugin_url = false;

	/**
	 * Remote URL for getting plugin updates.
	 *
	 * @var boolean|string
	 */
	public $remote_url = false;

	/**
	 * Version number of the plugin.
	 *
	 * @var boolean|integer
	 */
	public $version = false;

	/**
	 * License key for the plugin.
	 *
	 * @var boolean|string
	 */
	public $key = false;

	/**
	 * Holds the update data returned from the API.
	 *
	 * @var boolean|object
	 */
	public $update = false;

	/**
	 * Holds the plugin info details for the update.
	 *
	 * @var boolean|object
	 */
	public $info = false;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $config Array of updater config args.
	 */
	public function __construct( array $config ) {

		// Set class properties.
		$accepted_args = array(
			'plugin_name',
			'plugin_slug',
			'plugin_path',
			'plugin_url',
			'remote_url',
			'version',
			'key',
		);

		foreach ( $accepted_args as $arg ) {
			$this->$arg = $config[ $arg ];
		}

		// If the user cannot update plugins, stop processing here.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		// Load the updater hooks and filters.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins_filter' ) );
		//add_filter( 'set_site_transient_update_plugins', array( $this, 'set_site_transient_update_plugins' ) );
		//add_filter( 'transient_update_plugins', array( $this, 'transient_update_plugins' ) );
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
		add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
	}

	/**
	 * Infuse plugin update details when WordPress runs its update checker.
	 *
	 * @since 2.0.0
	 *
	 * @param object $value The WordPress update object.
	 *
	 * @return object $value Amended WordPress update object on success, default if object is empty.
	 */
	public function update_plugins_filter( $value ) {

		// If no update object exists, return early.
		if ( empty( $value ) ) {
			return $value;
		}

		// Run update check by pinging the external API. If it fails, return the default update object.
		if ( ! $this->update ) {
			$this->update = $this->perform_remote_request( 'get-plugin-update', array( 'slug' => $this->plugin_slug ) );
			if ( ! $this->update || ! empty( $this->update->error ) ) {
				$this->update = false;

				return $value;
			}
		}

		// Infuse the update object with our data if the version from the remote API is newer.
		if ( isset( $this->update->new_version ) && version_compare( $this->version, $this->update->new_version, '<' ) ) {
			// The $plugin_update object contains new_version, package, slug and last_update keys.
			$value->response[ $this->plugin_path ] = $this->update;
		} else {
			// No update is available.
			$item = (object) array(
				'id'            => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
				'slug'          => 'seedprod-coming-soon-pro-5',
				'plugin'        => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
				'new_version'   => SEEDPROD_PRO_VERSION,
				'url'           => '',
				'package'       => '',
				'icons'         => array(),
				'banners'       => array(),
				'banners_rtl'   => array(),
				'tested'        => '',
				'requires_php'  => '',
				'compatibility' => new stdClass(),
			);
			// Adding the "mock" item to the `no_update` property is required
			// for the enable/disable auto-updates links to correctly appear in UI.
			$value->no_update['seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php'] = $item;
		}

		// Return the update object.
		return $value;
	}

	/**
	 * Disables SSL verification to prevent download package failures.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args Array of request args.
	 * @param string $url  The URL to be pinged.
	 *
	 * @return array $args Amended array of request args.
	 */
	public function http_request_args( $args, $url ) {
		return $args;
	}

	/**
	 * Filters the plugins_api function to get our own custom plugin information
	 * from our private repo.
	 *
	 * @since 2.0.0
	 *
	 * @param object $api    The original plugins_api object.
	 * @param string $action The action sent by plugins_api.
	 * @param array  $args   Additional args to send to plugins_api.
	 *
	 * @return object $api   New stdClass with plugin information on success, default response on failure.
	 */
	public function plugins_api( $api, $action = '', $args = null ) {
		$plugin = ( 'plugin_information' === $action ) && isset( $args->slug ) && ( 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php' === $args->slug );

		// If our plugin matches the request, set our own plugin data, else return the default response.
		if ( $plugin ) {
			return $this->set_plugins_api( $api );
		} else {
			return $api;
		}
	}

	/**
	 * Pings a remote API to retrieve plugin information for WordPress to display.
	 *
	 * @since 2.0.0
	 *
	 * @param object $default_api The default API object.
	 *
	 * @return object $api        Return custom plugin information to plugins_api.
	 */
	public function set_plugins_api( $default_api ) {

		// Perform the remote request to retrieve our plugin information. If it fails, return the default object.
		if ( ! $this->info ) {
			$this->info = $this->perform_remote_request( 'get-plugin-info', array( 'slug' => $this->plugin_slug ) );
			if ( ! $this->info || ! empty( $this->info->error ) ) {
				$this->info = false;

				return $default_api;
			}
		}

		// Create a new stdClass object and populate it with our plugin information.
		$api                        = new stdClass();
		$api->name                  = isset( $this->info->name ) ? $this->info->name : '';
		$api->slug                  = isset( $this->info->slug ) ? $this->info->slug : '';
		$api->version               = isset( $this->info->version ) ? $this->info->version : '';
		$api->author                = isset( $this->info->author ) ? $this->info->author : '';
		$api->author_profile        = isset( $this->info->author_profile ) ? $this->info->author_profile : '';
		$api->requires              = isset( $this->info->requires ) ? $this->info->requires : '';
		$api->tested                = isset( $this->info->tested ) ? $this->info->tested : '';
		$api->last_updated          = isset( $this->info->last_updated ) ? $this->info->last_updated : '';
		$api->homepage              = isset( $this->info->homepage ) ? $this->info->homepage : '';
		$api->sections['changelog'] = isset( $this->info->changelog ) ? $this->info->changelog : '';
		$api->download_link         = isset( $this->info->download_link ) ? $this->info->download_link : '';
		$api->active_installs       = isset( $this->info->active_installs ) ? $this->info->active_installs : '';
		$api->banners               = isset( $this->info->banners ) ? (array) $this->info->banners : '';

		// Return the new API object with our custom data.
		return $api;
	}

	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action        The name of the $_POST action var.
	 * @param array  $body          The content to retrieve from the remote URL.
	 * @param array  $headers       The headers to send to the remote URL.
	 * @param string $return_format The format for returning content from the remote URL.
	 *
	 * @return string|boolean          Json decoded response on success, false on failure.
	 */
	public function perform_remote_request( $action, $body = array(), $headers = array(), $return_format = 'json' ) {

		// Build the body of the request.
		$body = wp_parse_args(
			$body,
			array(
				'action'            => $action,
				'api_key'           => $this->key,
				'slug'              => 'seedprod-pro',
				'wp_version'        => get_bloginfo( 'version' ),
				'domain'            => home_url(),
				'installed_version' => $this->version,
			)
		);
		$body = http_build_query( $body, '', '&' );

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Content-Type'   => 'application/x-www-form-urlencoded',
				'Content-Length' => strlen( $body ),
			)
		);

		// Setup variable for wp_remote_post.
		$post = array(
			'headers' => $headers,
			'body'    => $body,
		);

		// Perform the query and retrieve the response.
		$response      = wp_remote_post( esc_url_raw( $this->remote_url ), $post );
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( 200 !== $response_code || is_wp_error( $response_body ) ) {
			return false;
		}

		if ( ! empty( $response_body ) ) {
			$body          = json_decode( $response_body );
			$response_body = json_decode( $response_body );
		}

		if ( ! empty( $body->valid ) && true === $body->valid ) {
			// Store API key
			update_option( 'seedprod_api_token', $body->api_token );
			update_option( 'seedprod_api_message', $body->message );
			update_option( 'seedprod_license_name', $body->license_name );
			update_option( 'seedprod_a', true );
			update_option( 'seedprod_per', $body->per );
			$response = array(
				'status' => 'true',
				'msg'    => $body->message,
				'body'   => $body,
			);
		} elseif ( isset( $body->valid ) && false === $body->valid ) {
			$api_msg = __( 'Invalid License Key.', 'seedprod-pro' );
			if ( 'Unauthenticated.' != $body->message ) {
				$api_msg = $body->message;
			}
			update_option( 'seedprod_license_name', '' );
			update_option( 'seedprod_api_token', '' );
			update_option( 'seedprod_api_message', $api_msg );
			update_option( 'seedprod_a', false );
			update_option( 'seedprod_per', '' );
			$response = array(
				'status' => 'false',
				'msg'    => $api_msg,
				'body'   => $body,
			);
		}

		// A few items need to be converted from an object to an array as that
		// is what WordPress expects.
		if ( ! empty( $response_body->package ) ) {
			if ( ! empty( $response_body->icons ) ) {
				$response_body->icons = (array) $response_body->icons;
			}
			if ( ! empty( $response_body->banners ) ) {
				$response_body->banners = (array) $response_body->banners;
			}
		}

		// Return the json decoded content.
		return $response_body;
	}
}
