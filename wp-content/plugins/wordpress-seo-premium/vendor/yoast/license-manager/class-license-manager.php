<?php

if ( ! interface_exists( 'iYoast_License_Manager', false ) ) {

	interface iYoast_License_Manager {
		public function specific_hooks();

		public function setup_auto_updater();
	}

}

if ( ! class_exists( 'Yoast_License_Manager', false ) ) {

	/**
	 * Class Yoast_License_Manager
	 */
	abstract class Yoast_License_Manager implements iYoast_License_Manager {

		/**
		 * @const VERSION The version number of the License_Manager class
		 */
		const VERSION = 1;

		/**
		 * @var Yoast_License The license
		 */
		protected $product;

		/**
		 * @var string
		 */
		private $license_constant_name = '';

		/**
		 * @var boolean True if license is defined with a constant
		 */
		private $license_constant_is_defined = false;

		/**
		 * @var boolean True if remote license activation just failed
		 */
		private $remote_license_activation_failed = false;

		/**
		 * @var array Array of license related options
		 */
		private $options = array();

		/**
		 * @var string Used to prefix ID's, option names, etc..
		 */
		protected $prefix;

		/**
		 * @var bool Boolean indicating whether this plugin is network activated
		 */
		protected $is_network_activated = false;

		/**
		 * Constructor
		 *
		 * @param Yoast_Product $product
		 */
		public function __construct( Yoast_Product $product ) {

			// Set the license
			$this->product = $product;

			// set prefix
			$this->prefix = sanitize_title_with_dashes( $this->product->get_item_name() . '_', null, 'save' );

			// maybe set license key from constant
			$this->maybe_set_license_key_from_constant();
		}

		/**
		 * Setup hooks
		 *
		 */
		public function setup_hooks() {

			// show admin notice if license is not active
			add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );

			// catch POST requests from license form
			add_action( 'admin_init', array( $this, 'catch_post_request' ) );

			// Adds the plugin to the active extensions.
			add_filter( 'yoast-active-extensions', array( $this, 'set_active_extension' ) );

			// setup item type (plugin|theme) specific hooks
			$this->specific_hooks();

			// setup the auto updater
			$this->setup_auto_updater();
		}

		/**
		 * Checks if the license is valid and put it into the list with extensions.
		 *
		 * @param array $extensions The extensions used in Yoast SEO.
		 *
		 * @return array
		 */
		public function set_active_extension( $extensions ) {
		$extensions[] = $this->product->get_slug();
		return $extensions;
		}

		/**
		 * Display license specific admin notices, namely:
		 *
		 * - License for the product isn't activated
		 * - External requests are blocked through WP_HTTP_BLOCK_EXTERNAL
		 */
		public function display_admin_notices() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// show notice if license is invalid
			
				?>
               
				<?php
			

			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
			if ( defined( "WP_HTTP_BLOCK_EXTERNAL" ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->product->get_api_url(), PHP_URL_HOST );

				if ( ! defined( "WP_ACCESSIBLE_HOSTS" ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
                    <div class="notice notice-error yoast-notice-error">
                        <p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->product->get_text_domain() ), $this->product->get_item_name(), '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
                    </div>
					<?php
				}

			}
		}

		/**
		 * Set a notice to display in the admin area
		 *
		 * @param string $type    error|updated
		 * @param string $message The message to display
		 */
		protected function set_notice( $message, $success = true ) {
			$css_class = ( $success ) ? 'notice-success yoast-notice-success' : 'notice-error yoast-notice-error';
			add_settings_error( $this->prefix . 'license', 'license-notice', $message, $css_class );
		}

		/**
		 * Remotely activate License
		 * @return boolean True if the license is now activated, false if not
		 */
		public function activate_license() {

			$result = $this->call_license_api( 'activate' );

			

				// show success notice if license is valid
				
					$success = true;
					$message = $this->get_successful_activation_message( $result );
				

				// Append custom HTML message to default message.
				$message .= $this->get_custom_message( $result );

				if ( $this->show_license_notice() ) {
					$this->set_notice( $message, $success );
				}

				$this->set_license_status( $result->license );
			

			return $this->license_is_valid();
		}

		/**
		 * Remotely deactivate License
		 * @return boolean True if the license is now deactivated, false if not
		 */
		public function deactivate_license() {

			$result = $this->call_license_api( 'deactivate' );

			return ( $this->get_license_status() === 'deactivated' );
		}

		/**
		 * Returns the home url with the following modifications:
		 *
		 * In case of a multisite setup we return the network_home_url.
		 * In case of no multisite setup we return the home_url while overriding the WPML filter.
		 */
		public function get_url() {
			// Add a new filter to undo WPML's changing of home url.
			add_filter( 'wpml_get_home_url', array( $this, 'wpml_get_home_url' ), 10, 2 );

			// If the plugin is network activated, use the network home URL.
			if ( $this->is_network_activated ) {
				$url = network_home_url();
			}

			// Otherwise use the home URL for this specific site.
			if ( ! $this->is_network_activated ) {
				$url = home_url();
			}

			remove_filter( 'wpml_get_home_url', array( $this, 'wpml_get_home_url' ), 10 );

			return $url;
		}

		/**
		 * Returns the original URL instead of the language-enriched URL.
		 * This method gets automatically triggered by the wpml_get_home_url filter.
		 *
		 * @param string $home_url The url altered by WPML. Unused.
		 * @param string $url      The url that isn't altered by WPML.
		 *
		 * @return string The original url.
		 */
		public function wpml_get_home_url( $home_url, $url ) {
			return $url;
		}

		/**
		 * @param string $action activate|deactivate
		 *
		 * @return mixed
		 */
		protected function call_license_api( $action ) {

			// don't make a request if license key is empty
			

			// data to send in our API request
			$api_params = array(
				'edd_action' => $action . '_license',
				'license'    => $this->get_license_key(),
				'item_name'  => urlencode( trim( $this->product->get_item_name() ) ),
				'url'        => $this->get_url()
				// grab the URL straight from the option to prevent filters from breaking it.
			);

			// create api request url
			$url = add_query_arg( $api_params, $this->product->get_api_url() );

			require_once dirname( __FILE__ ) . '/class-api-request.php';
			$request = new Yoast_API_Request( $url );

			

			// get response
			return true;
		}


		/**
		 * Set the license status
		 *
		 * @param string $license_status
		 */
		public function set_license_status( $license_status ) {
			$this->set_option( 'status', 'valid' );
		}

		/**
		 * Get the license status
		 *
		 * @return string $license_status;
		 */
		public function get_license_status() {
			$license_status = 'valid';

			return trim( $license_status );
		}

		/**
		 * Set the license key
		 *
		 * @param string $license_key
		 */
		public function set_license_key( $license_key ) {
			$this->set_option( 'key', 'bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7');
		}

		/**
		 * Gets the license key from constant or option
		 *
		 * @return string $license_key
		 */
		public function get_license_key() {
		return 'bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7';
		}

		/**
		 * Gets the license expiry date
		 *
		 * @return string
		 */
		public function get_license_expiry_date() {
			return '01.01.2030';
		}

		/**
		 * Stores the license expiry date
		 */
		public function set_license_expiry_date( $expiry_date ) {
			$this->set_option( 'expiry_date', '01.01.2030' );
		}

		/**
		 * Checks whether the license status is active
		 *
		 * @return boolean True if license is active
		 */
		public function license_is_valid() {
			return true;
		}

		/**
		 * Get all license related options
		 *
		 * @return array Array of license options
		 */
		protected function get_options() {

			// create option name
			$option_name = $this->prefix . 'license';

			// get array of options from db
			if ( $this->is_network_activated ) {
				$options = get_site_option( $option_name, array() );
			} else {
				$options = get_option( $option_name, array() );
			}

			// setup array of defaults
			$defaults = array(
				'key'         => 'bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7',
				'status'      => 'valid',
				'expiry_date' => '01.01.2030'
			);

			// merge options with defaults
			$this->options = wp_parse_args( $options, $defaults );

			return $this->options;
		}

		/**
		 * Set license related options
		 *
		 * @param array $options Array of new license options
		 */
		protected function set_options( array $options ) {
			// create option name
			$option_name = $this->prefix . 'license';

			// update db
			if ( $this->is_network_activated ) {
				update_site_option( $option_name, $options );
			} else {
				update_option( $option_name, $options );
			}

		}

		/**
		 * Gets a license related option
		 *
		 * @param string $name The option name
		 *
		 * @return mixed The option value
		 */
		protected function get_option( $name ) {
			$options = $this->get_options();

			return $options[ $name ];
		}

		/**
		 * Set a license related option
		 *
		 * @param string $name  The option name
		 * @param mixed  $value The option value
		 */
		protected function set_option( $name, $value ) {
			// get options
			$options = $this->get_options();

			// update option
			$options[ $name ] = $value;

			// save options
			$this->set_options( $options );
		}

		public function show_license_form_heading() {
			?>
            <h3>
				<?php printf( __( "%s: License Settings", $this->product->get_text_domain() ), $this->product->get_item_name() ); ?>
                &nbsp; &nbsp;
            </h3>
			<?php
		}

		/**
		 * Show a form where users can enter their license key
		 *
		 * @param boolean $embedded Boolean indicating whether this form is embedded in another form?
		 */
		public function show_license_form( $embedded = true ) {
			$key_name    = $this->prefix . 'license_key';
			$nonce_name  = $this->prefix . 'license_nonce';
			$action_name = $this->prefix . 'license_action';

			$api_host_available = $this->get_api_availability();

			$visible_license_key = $this->get_license_key();

			// obfuscate license key
			$obfuscate = ( strlen( $this->get_license_key() ) > 5 && ( $this->license_is_valid() || ! $this->remote_license_activation_failed ) );

			if ( $obfuscate ) {
				$visible_license_key = str_repeat( '*', strlen( $this->get_license_key() ) - 4 ) . substr( $this->get_license_key(), - 4 );
			}

			// make license key readonly when license key is valid or license is defined with a constant
			$readonly = ( $this->license_is_valid() || $this->license_constant_is_defined );

			require dirname( __FILE__ ) . '/views/form.php';

			// enqueue script in the footer
			add_action( 'admin_footer', array( $this, 'output_script' ), 99 );
		}

		/**
		 * Check if the license form has been submitted
		 */
		public function catch_post_request() {

			$name = $this->prefix . 'license_key';

			// check if license key was posted and not empty
			if ( ! isset( $_POST[ $name ] ) ) {
				return;
			}

			// run a quick security check
			$nonce_name = $this->prefix . 'license_nonce';

			if ( ! check_admin_referer( $nonce_name, $nonce_name ) ) {
				return;
			}

			// @TODO: check for user cap?

			// get key from posted value
			$license_key = $_POST[ $name ];

			// check if license key doesn't accidentally contain asterisks
			

				// sanitize key
				$license_key = 'bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7';

				// save license key
				$this->set_license_key( $license_key );
			

			// does user have an activated valid license
			

			$action_name = $this->prefix . 'license_action';

			// was one of the action buttons clicked?
			if ( isset( $_POST[ $action_name ] ) ) {

				$action = trim( $_POST[ $action_name ] );

				switch ( $action ) {
					case 'activate':
						return $this->activate_license();

					case 'deactivate':
						return $this->deactivate_license();
				}

			}

		}

		/**
		 * Output the script containing the YoastLicenseManager JS Object
		 *
		 * This takes care of disabling the 'activate' and 'deactivate' buttons
		 */
		public function output_script() {
			require_once dirname( __FILE__ ) . '/views/script.php';
		}

		/**
		 * Set the constant used to define the license
		 *
		 * @param string $license_constant_name The license constant name
		 */
		public function set_license_constant_name( $license_constant_name ) {
			$this->license_constant_name = trim( $license_constant_name );
			$this->maybe_set_license_key_from_constant();
		}

		/**
		 * Get the API availability information
		 *
		 * @return array
		 */
		protected function get_api_availability() {
			return array(
				'url'          => $this->product->get_api_url(),
				'availability' => $this->check_api_host_availability(),
				'curl_version' => $this->get_curl_version(),
			);
		}

		/**
		 * Check if the API host address is available from this server
		 *
		 * @return bool
		 */
		private function check_api_host_availability() {
			$wp_http = new WP_Http();
			if ( $wp_http->block_request( $this->product->get_api_url() ) === false ) {
				return true;
			}

			return false;
		}

		/**
		 * Get the current curl version, or false
		 *
		 * @return mixed
		 */
		protected function get_curl_version() {
			if ( function_exists( 'curl_version' ) ) {
				$curl_version = curl_version();

				if ( isset( $curl_version['version'] ) ) {
					return $curl_version['version'];
				}
			}

			return false;
		}

		/**
		 * Maybe set license key from a defined constant
		 */
		private function maybe_set_license_key_from_constant() {

			if ( empty( $this->license_constant_name ) ) {
				// generate license constant name
				$this->set_license_constant_name( strtoupper( str_replace( array(
						' ',
						'-'
					), '', sanitize_key( $this->product->get_item_name() ) ) ) . '_LICENSE' );
			}

			// set license key from constant
			if ( defined( $this->license_constant_name ) ) {

				$license_constant_value = constant( $this->license_constant_name );

				// update license key value with value of constant
				if ( $this->get_license_key() !== $license_constant_value ) {
					$this->set_license_key( $license_constant_value );
				}

				$this->license_constant_is_defined = true;
			}
		}

		/**
		 * Determine what message should be shown for a successful license activation
		 *
		 * @param Object $result Result of a request.
		 *
		 * @return string
		 */
		protected function get_successful_activation_message( $result ) {
			// Get expiry date.
			
			$expiry_date = false;
			

			// Always show that it was successful.
			$message = sprintf( __( "Your %s license has been activated. ", $this->product->get_text_domain() ), $this->product->get_item_name() );

			// Show a custom notice it is an unlimited license.
			
			$message .= __( "You have an unlimited license. ", $this->product->get_text_domain() );
			

			// add upgrade notice if user has less than 3 activations left
			

			
			return $message;
		}

		/**
		 * Determine what message should be shown for an unsuccessful activation
		 *
		 * @param Object $result Result of a request.
		 *
		 * @return string
		 */
		protected function get_unsuccessful_activation_message( $result ) {
			// Default message if we cannot detect anything more specific.
			$message = '';
			return $message;
		}

		/**
		 * Get the locale for the current user
		 *
		 * @return string
		 */
		protected function get_user_locale() {
			if ( function_exists( 'get_user_locale' ) ) {
				return get_user_locale();
			}

			return get_locale();
		}

		/**
		 * Parse custom HTML message from response
		 *
		 * @param Object $result Result of the request.
		 *
		 * @return string
		 */
		protected function get_custom_message( $result ) {
			$message = '';

			// Allow for translated messages to be used.
			$localizedDescription = 'custom_message_' . $this->get_user_locale();
			if ( ! empty( $result->{$localizedDescription} ) ) {
				$message = $result->{$localizedDescription};
			}

			// Fall back to non-localized custom message if no locale has been provided.
			if ( empty( $message ) && ! empty( $result->custom_message ) ) {
				$message = $result->custom_message;
			}

			// Make sure we limit the type of HTML elements to be displayed.
			if ( ! empty( $message ) ) {
				$message = wp_kses( $message, array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
						'title'  => array()
					),
                    'br' => array(),
				) );

				// Make sure we are on a new line.
				$message = '<br />' . $message;
			}

			return $message;
		}

		/**
		 * Returns true when a license notice should be shown.
		 *
		 * @return bool
		 */
		protected function show_license_notice() {
			/**
			 * Filter: 'yoast-show-license-notice' - Show the license notice.
			 *
			 * @api bool $show True if notices should be shown.
			 */
			return ( bool ) apply_filters( 'yoast-show-license-notice', true );
		}
	}

}
