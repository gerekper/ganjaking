<?php

/**
 * Class to check if the current WordPress and PHP versions meet our requirements
 *
 * @see           Docs https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/21692485/WFF+Requirements
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @version       2.0.0
 * @since         4.0.9
 */
// @formatter:off
if ( ! class_exists( 'Wbcr_Factory423_Requirements' ) ) {
	class Wbcr_Factory423_Requirements {

		/**
		 * Factory framework version
		 *
		 * @var string
		 */
		protected $factory_version;

		/**
		 * @var string
		 */
		protected $plugin_version;

		/**
		 * Plugin file path
		 *
		 * @var string
		 */
		protected $plugin_file;

		/**
		 * Plugin dir
		 *
		 * @var string
		 */
		protected $plugin_abs_path;

		/**
		 * Plugin base dir
		 *
		 * @var string
		 */
		protected $plugin_basename;

		/**
		 * Plugin url
		 *
		 * @var string
		 */
		protected $plugin_url;

		/**
		 * Plugin prefix
		 *
		 * @var string
		 */
		protected $plugin_prefix;

		/**
		 * Plugin name
		 *
		 * @var string
		 */
		protected $plugin_name;

		/**
		 * Plugin title
		 *
		 * @var string
		 */
		protected $plugin_title = "(no title)";

		/**
		 * @var string
		 */
		protected $plugin_text_domain;

		/**
		 * Required PHP version
		 *
		 * @var string
		 */
		protected $required_php_version = '5.3';

		/**
		 * Required WordPress version
		 *
		 * @var string
		 */
		protected $required_wp_version = '4.2.0';

		/**
		 * Is this plugin already activated?
		 *
		 * @var bool
		 */
		protected $plugin_already_activate = false;

		/**
		 * WFF_Requirements constructor.
		 *
		 * @param string $plugin_file
		 * @param array  $plugin_info
		 */
		public function __construct( $plugin_file, array $plugin_info ) {

			foreach ( (array) $plugin_info as $property => $value ) {
				if ( property_exists( $this, $property ) ) {
					$this->$property = $value;
				}
			}

			$this->plugin_file     = $plugin_file;
			$this->plugin_abs_path = dirname( $plugin_file );
			$this->plugin_basename = plugin_basename( $plugin_file );
			$this->plugin_url      = plugins_url( null, $plugin_file );

			$plugin_info = get_file_data( $this->plugin_file, array(
				'Version'          => 'Version',
				'FrameworkVersion' => 'Framework Version',
				'TextDomain'       => 'Text Domain'
			), 'plugin' );

			if ( isset( $plugin_info['FrameworkVersion'] ) ) {
				$this->factory_version = $plugin_info['FrameworkVersion'];
			}

			if ( isset( $plugin_info['Version'] ) ) {
				$this->plugin_version = $plugin_info['Version'];
			}

			if ( isset( $plugin_info['TextDomain'] ) ) {
				$this->plugin_text_domain = $plugin_info['TextDomain'];
			}

			add_action( 'admin_init', array( $this, 'register_notices' ) );
		}

		public function get_plugin_version() {
			return $this->plugin_version;
		}

		public function get_text_domain() {
			return $this->plugin_text_domain;
		}

		/**
		 * @since 4.1.1
		 * @return void
		 */
		public function register_notices() {
			if ( current_user_can( 'activate_plugins' ) && current_user_can( 'edit_plugins' ) && current_user_can( 'install_plugins' ) ) {

				if ( is_multisite() ) {
					add_action( 'network_admin_notices', array( $this, 'show_notice' ) );

					if ( ! empty( $this->plugin_basename ) && in_array( $this->plugin_basename, (array) get_option( 'active_plugins', array() ) ) ) {
						add_action( 'admin_notices', array( $this, 'show_notice' ) );
					}
				} else {
					add_action( 'admin_notices', array( $this, 'show_notice' ) );
				}
			}
		}

		/**
		 * Shows the incompatibility notification.
		 *
		 * @since 4.1.1
		 * @return void
		 */
		public function show_notice() {
			if ( $this->check() ) {
				return;
			}

			echo '<div class="notice notice-error"><p>' . $this->get_notice_text() . '</p></div>';
		}


		/**
		 * The method checks the compatibility of the plugin with php and wordpress version.
		 *
		 * @since 4.1.1
		 * @return bool
		 */
		public function check() {

			// Fix for ithemes sync. When the ithemes sync plugin accepts the request, set the WP_ADMIN constant,
			// after which the plugin Clearfy begins to create errors, and how the logic of its work is broken.
			// Solution to simply terminate the plugin if there is a request from ithemes sync
			// --------------------------------------
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'ithemes_sync_request' ) {
				return false;
			}

			if ( isset( $_GET['ithemes-sync-request'] ) && ! empty( $_GET['ithemes-sync-request'] ) ) {
				return false;
			}
			// ----------------------------------------

			if ( ! $this->check_php_compat() || ! $this->check_wp_compat() || $this->plugin_already_activate ) {
				return false;
			}

			return true;
		}

		/**
		 * The method checks the compatibility of the plugin with the php version of the server.
		 *
		 * @return mixed
		 */
		public function check_php_compat() {
			return version_compare( PHP_VERSION, $this->required_php_version, '>=' );
		}

		/**
		 * The method checks the compatibility of the plugin with the Wordpress version of the site.
		 *
		 * @return mixed
		 */
		public function check_wp_compat() {
			// Get the WP Version global.
			global $wp_version;

			return version_compare( $wp_version, $this->required_wp_version, '>=' );
		}

		/**
		 * Method returns notification text
		 *
		 * @return string
		 */
		protected function get_notice_text() {
			$notice_text         = $notice_default_text = '';
			$notice_default_text .= '<b>' . $this->plugin_title . ' ' . __( 'warning', '' ) . ':</b>' . '<br>';

			$notice_default_text .= sprintf( __( 'The %s plugin has stopped.', 'wbcr_factory_clearfy_000' ), $this->plugin_title ) . ' ';
			$notice_default_text .= __( 'Possible reasons:', '' ) . ' <br>';

			$has_one = false;

			if ( ! $this->check_php_compat() ) {
				$has_one     = true;
				$notice_text .= '- ' . $this->get_php_incompat_text() . '<br>';
			}

			if ( ! $this->check_wp_compat() ) {
				$has_one     = true;
				$notice_text .= '- ' . $this->get_wp_incompat_text() . '<br>';
			}

			if ( $this->plugin_already_activate ) {
				$has_one     = true;
				$notice_text = '- ' . $this->get_plugin_already_activate_text() . '<br>';
			}

			if ( $has_one ) {
				$notice_text = $notice_default_text . $notice_text;
			}

			return $notice_text;
		}

		/**
		 * @return string
		 */
		protected function get_php_incompat_text() {
			return sprintf( __( 'You need to update the PHP version to %s or higher!', 'wbcr_factory_423' ), $this->required_php_version );
		}

		/**
		 * @return string
		 */
		protected function get_wp_incompat_text() {
			return sprintf( __( 'You need to update WordPress to %s or higher!', 'wbcr_factory_423' ), $this->required_wp_version );
		}

		/**
		 * @return string
		 */
		protected function get_plugin_already_activate_text() {
			return sprintf( __( 'Plugin %s is already activated, you are trying to activate it again.', 'wbcr_factory_423' ), $this->plugin_title );
		}
	}
}
// @formatter:on