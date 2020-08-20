<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_Optimus_Prime_Plugin_Update' ) ) {

	/**
	 * Get updates from Optimus Prime
	 */
	class CT_Optimus_Prime_Plugin_Update {

		/**
		 * @var string
		 */
		private $optimus_url = 'http://update.optimus-prime.createit.pl/updater';

		/**
		 * Less than 45 chars
		 * @var string
		 */
		private $transient_id = 'ct_op_plugin_update';

		/**
		 * How long to cache server responses
		 * @var int seconds
		 */
		private $transient_expiration = DAY_IN_SECONDS;

		/**
		 * Used when there is internet connection problems
		 * To prevent site being blocked on every refresh, this fake version will be cached in the transient
		 * @var string
		 */
		private $fake_latest_version = '0.0.0';

		/**
		 * @var array
		 */
		private $admin_notices = array();

		/**
		 * @var array
		 */
		private $plugins_to_update = array();

		/**
		 * @param $notice
		 */
		private function add_admin_notice( $notice ) {
			$this->admin_notices[] = $notice;
		}

		/**
		 * CT_Optimus_Prime_Update constructor.
		 */
		public function __construct() {

			// set to true for local debug
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				add_filter( 'http_request_host_is_external', '__return_true' );
			}

			$this->plugins_to_update = $this->get_plugins_to_update();

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'api_check' ) );
			add_action( 'delete_site_transient_update_plugins', array( $this, 'delete_transient' ) );
			add_action( 'admin_notices', array( $this, 'render_admin_notices' ) );
			add_action( 'core_upgrade_preamble', array( $this, 'get_plugins_latest_version' ) );
			add_filter( 'upgrader_package_options', array( $this, 'fix_package_download_url' ) );
			add_filter( 'upgrader_pre_download', array( $this, 'upgrader_modify_strings' ), 10, 3 );
			add_filter( 'ct_plugin_op_version', array( $this, 'tgmpa_plugin_version_filter' ), 10, 2 );

		}

		/**
		 * @param $transient
		 *
		 * @return mixed
		 */
		public function api_check( $transient ) {

			// Check if the transient contains the 'checked' information
			// If not, just return its value without hacking it
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			// check the version and decide if it's new
			foreach ( $this->plugins_to_update as $plugin ) {

				$new_version  = $this->get_latest_version( $plugin['slug'] );
				$optimus_data = $this->fetch_response_data( $plugin['slug'], '' );
				$download_url = is_wp_error($optimus_data) ? '' : $optimus_data['download_url'];

				if ( $download_url && version_compare( $plugin['version'], $new_version ) < 0 ) {

					$response              = new stdClass;
					$response->new_version = $new_version;
					$response->slug        = $plugin['slug'];
					$response->plugin      = $plugin['slug'];
					$response->package     = $download_url;

					$transient->response[ $plugin['slug'] ] = $response;
				}

			}

			return $transient;
		}

		/** Update tgmpa input info
		 *
		 * @param $version
		 * @param $slug
		 *
		 * @return string|WP_Error
		 */
		public function tgmpa_plugin_version_filter( $version, $slug ) {

			$plugins = $this->get_plugins_to_update();
			if ( empty( $plugins[ $slug ] ) ) {
				return $version;
			}

			$op_version = $this->get_latest_version( $slug, false, $slug );

			if ( version_compare( $version, $op_version, '<' ) ) {
				$version = $op_version;
			}

			return $version;

		}

		/** Hide implicit OP url */
		public function upgrader_modify_strings( $return, $package, $upgrader ) {

			$upgrader->strings['downloading_package'] = esc_html__( 'Downloading update...', 'ct-ultimate-gdpr' );

			return $return;
		}

		/**
		 *
		 */
		public function render_admin_notices() {

			$class = 'notice notice-error';

			foreach ( $this->admin_notices as $notice ) {
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $notice ) );
			}

		}

		/**
		 * Return latest version of the theme if this service supports theme update
		 *
		 * @param bool $force_check Check now, do not use cache
		 *
		 * @return string|false|WP_Error
		 *      false    Does not know how to work with extension.
		 *      WP_Error Knows how to work with it, but there is an error
		 *      string   Everything is ok, here is latest version
		 *
		 * @internal
		 */
		public function get_plugins_latest_version( $force_check ) {

			foreach ( $this->get_plugins_to_update() as $ct_plugin_data ) {

				$name    = $ct_plugin_data['slug'];
				$license = isset( $ct_plugin_data['licence'] ) ? $ct_plugin_data['licence'] : '';

				$this->get_latest_version(
					$name,
					$force_check,
					esc_html__( 'Plugin', 'ct-ultimate-gdpr' ),
					$license
				);

			}

		}

		/**
		 * Get repository latest release version
		 *
		 * @param string $name Theme name
		 * @param bool $force_check Bypass cache
		 * @param string $title Used in messages
		 *
		 * @param string $license
		 *
		 * @return string
		 */
		private function get_latest_version( $name, $force_check = false, $title = '', $license = '' ) {

			if ( $force_check ) {

				delete_site_transient( $this->transient_id );

			} else {

				$cache = get_site_transient( $this->transient_id );

				if ( isset( $cache[ $name ] ) ) {
					return $cache[ $name ];
				}

			}

			if ( ! isset( $cache ) || ! is_array( $cache ) ) {
				$cache = array();
			}

			$latest_version = $this->fetch_latest_version( $name, $license );

			/** @var WP_Error $latest_version */
			if ( is_wp_error( $latest_version ) ) {

				/**
				 * Internet connection problems or Optimus down.
				 * Cache fake version to prevent requests on every refresh.
				 */
				$cache = array_merge( $cache, array( $name => $this->fake_latest_version ) );

				/**
				 * Show the error to the user because it is not visible elsewhere
				 */
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					$this->add_admin_notice(
						$latest_version->get_error_message()
					);
				}

			} else {

				$cache = array_merge( $cache, array( $name => $latest_version ) );
				set_site_transient(
					$this->transient_id,
					$cache,
					$this->transient_expiration
				);

			}

			return $cache[ $name ];

		}

		/**
		 * @param $name
		 * @param $licence
		 *
		 * @return mixed
		 */
		private function fetch_latest_version( $name, $licence ) {

			$response_array = $this->fetch_response_data( $name, $licence );

			if ( is_wp_error( $response_array ) ) {
				return $response_array;
			}

			// change link
			return $response_array['version'];

		}

		/**
		 * @param $name
		 * @param $licence
		 *
		 * @return mixed
		 */
		private function fetch_response_data( $name, $licence ) {

			$url = $this->get_optimus_url( $name, $licence );

			$response = wp_remote_get( $url, array(
				'timeout' => 3,
			) );

			if ( is_array( $response ) ) {
				$response = $response['body']; // use the content
			}

			$response_array = json_decode( $response, true );

			// there is no download url inside response
			if ( is_wp_error( $response ) || ! $response || ! $response_array || empty( $response_array['version'] ) ) {
				return new WP_Error( '0', esc_html__( 'Could not get new version info from OP', 'ct-ultimate-gdpr' ) );
			}

			// change link
			return $response_array;

		}

		/**
		 * Delete transient information about updates
		 */
		public function delete_transient() {
			delete_site_transient( $this->transient_id );
		}

		/**
		 * Get url to query for update
		 *
		 * @param $name
		 * @param string $license
		 *
		 * @return string
		 */
		protected function get_optimus_url( $name, $license = '' ) {

			$url = $this->optimus_url;
			$url = add_query_arg( array( 'theme' => $name ), $url );
			if ( $license ) {
				$url = add_query_arg( array( 'license' => $license ), $url );
			}
			$url = apply_filters( 'ct_optimus_prime_plugin_update_url', $url, $name, $license );

			return $url;
		}

		/**
		 * Format:
		 * 'slug' => array(
		 *  'file' => 'full_path',
		 *  'slug' => 'slug',
		 * )
		 *
		 * @return array
		 */
		protected function get_plugins_to_update() {

			// get the name
			$data = apply_filters( 'ct_optimus_prime_plugin_update_plugins', array() );

			if ( ! is_array( $data ) ) {
				$data = array();
			}

			return $data;

		}

		/**
		 * @param $options
		 *
		 * @return mixed
		 */
		public function fix_package_download_url( $options ) {

			$original = $options['package'];

			// the link is not this plugin
			if ( false === strpos( $original, 'updater?theme=ct-ultimate-gdpr' ) ) {
				return $options;
			}

			$response = wp_remote_get( $original, array(
				'timeout' => 3,
			) );

			if ( is_array( $response ) ) {
				$response = $response['body']; // use the content
			}

			$response_array = json_decode( $response, true );

			// there is no download url inside response
			if ( is_wp_error( $response ) || ! $response || ! $response_array || empty( $response_array['download_url'] ) ) {
				return $options;
			}

			// change link
			$options['package'] = $response_array['download_url'];

			return $options;

		}

	}

	new CT_Optimus_Prime_Plugin_Update();
}
