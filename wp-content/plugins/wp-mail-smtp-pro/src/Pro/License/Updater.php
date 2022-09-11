<?php

namespace WPMailSMTP\Pro\License;

/**
 * Updater class.
 *
 * @since 1.5.0
 */
class Updater {

	/**
	 * Plugin name.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $plugin_name = '';

	/**
	 * Plugin slug.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $plugin_slug = '';

	/**
	 * Plugin path: plugin/plugin.php.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $plugin_path = '';

	/**
	 * URL of the plugin.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $plugin_url = '';

	/**
	 * Remote URL for getting plugin updates.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $remote_url = 'https://wpmailsmtp.com/license-api';

	/**
	 * Version number of the plugin.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * License key for the plugin.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $key = '';

	/**
	 * Holds the update data returned from the API.
	 *
	 * @since 1.5.0
	 *
	 * @var bool|object
	 */
	public $update = false;

	/**
	 * Holds the plugin info details for the update.
	 *
	 * @since 1.5.0
	 *
	 * @var bool|object
	 */
	public $info = false;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.5.0
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
			if ( array_key_exists( $arg, $config ) ) {
				$this->$arg = $config[ $arg ];
			}
		}

		if ( defined( 'WPMS_UPDATER_API' ) ) {
			$this->remote_url = WPMS_UPDATER_API;
		}

		// If the user cannot update plugins, stop processing here.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		// Load the updater hooks and filters.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins_filter' ) );
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
		add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
	}

	/**
	 * Infuse plugin update details when WordPress runs its update checker.
	 *
	 * @since 1.5.0
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
			$this->update = $this->perform_remote_request( 'get-plugin-update', array( 'tgm-updater-plugin' => $this->plugin_slug ) );

			// No update is available.
			if ( ! $this->update || ! empty( $this->update->error ) ) {
				$this->update = false;

				$value->no_update[ $this->plugin_path ] = $this->get_no_update();

				return $value;
			}
		}

		// Infuse the update object with our data if the version from the remote API is newer.
		if ( isset( $this->update->new_version ) && version_compare( $this->version, $this->update->new_version, '<' ) ) {
			// The $plugin_update object contains new_version, package, slug and last_update keys.
			$this->update->old_version             = $this->version;
			$this->update->plugin                  = $this->plugin_path;
			$value->response[ $this->plugin_path ] = $this->update;
		} else {
			$value->no_update[ $this->plugin_path ] = $this->get_no_update();
		}

		// Return the update object.
		return $value;
	}

	/**
	 * Disable SSL verification to prevent download package failures.
	 *
	 * @since 1.5.0
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
	 * Filter the plugins_api function to get our own custom plugin information from our private repo.
	 *
	 * @since 1.5.0
	 *
	 * @param object $api    The original plugins_api object.
	 * @param string $action The action sent by plugins_api.
	 * @param array  $args   Additional args to send to plugins_api.
	 *
	 * @return object $api   New stdClass with plugin information on success, default response on failure.
	 */
	public function plugins_api( $api, $action = '', $args = null ) {

		$plugin = ( 'plugin_information' === $action ) && isset( $args->slug ) && ( $this->plugin_slug === $args->slug );

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
	 * @since 1.5.0
	 *
	 * @param object $default_api The default API object.
	 *
	 * @return object $api        Return custom plugin information to plugins_api.
	 */
	public function set_plugins_api( $default_api ) {

		// Perform the remote request to retrieve our plugin information. If it fails, return the default object.
		if ( ! $this->info ) {
			$this->info = $this->perform_remote_request( 'get-plugin-info', array( 'tgm-updater-plugin' => $this->plugin_slug ) );
			if ( ! $this->info || ! empty( $this->info->error ) ) {
				$this->info = false;

				return $default_api;
			}
		}

		// Create a new stdClass object and populate it with our plugin information.
		$api                        = new \stdClass();
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
	 * Query the remote URL via wp_remote_get and return a json decoded response.
	 *
	 * @since 1.5.0
	 * @since 2.7.0 Switch from POST to GET request.
	 *
	 * @param string $action        The name of the request action var.
	 * @param array  $body          The GET query attributes.
	 * @param array  $headers       The headers to send to the remote URL.
	 * @param string $return_format The format for returning content from the remote URL.
	 *
	 * @return string|bool          Json decoded response on success, false on failure.
	 */
	public function perform_remote_request( $action, $body = [], $headers = [], $return_format = 'json' ) {

		// Request query parameters.
		$query_params = wp_parse_args(
			$body,
			[
				'tgm-updater-action'      => $action,
				'tgm-updater-key'         => $this->key,
				'tgm-updater-wp-version'  => get_bloginfo( 'version' ),
				'tgm-updater-php-version' => phpversion(),
				'tgm-updater-referer'     => site_url(),
			]
		);

		$args = [
			'headers' => $headers,
		];

		// Perform the query and retrieve the response.
		$response      = wp_remote_get( add_query_arg( $query_params, $this->remote_url ), $args );
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( 200 !== $response_code || is_wp_error( $response_body ) ) {
			return false;
		}

		$response_body = json_decode( $response_body );

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

	/**
	 * Prepare the "mock" item to the `no_update` property.
	 * Is required for the enable/disable auto-updates links to correctly appear in UI.
	 *
	 * @since 3.0.0
	 *
	 * @return object
	 */
	protected function get_no_update() {

		return (object) [
			'id'            => $this->plugin_path,
			'slug'          => $this->plugin_slug,
			'plugin'        => $this->plugin_path,
			'new_version'   => $this->version,
			'url'           => '',
			'package'       => '',
			'icons'         => [],
			'banners'       => [],
			'banners_rtl'   => [],
			'tested'        => '',
			'requires_php'  => '',
			'compatibility' => new \stdClass(),
		];
	}
}
