<?php
namespace Essential_Addons_Elementor\Pro\Classes\License;

// Exit if accessed directly
use Essential_Addons_Elementor\Pro\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles license input and validation
 */
class EAELicense {
	/**
	 * Product Slug
	 * @var string
	 */
	private $product_slug;
	/**
	 * Product Text Domain
	 * @var string
	 */
	private $text_domain;
	/**
	 * Product Name
	 * @var string
	 */
	private $product_name;
	/**
	 * Product ID in Store
	 * @var integer
	 */
	private $item_id;
	/**
	 * Settings Page Slug
	 * @var string
	 */
	private $page_slug = 'eael-settings';
	/**
	 * Store URL
	 * @var string
	 */
	private $storeURL = EAEL_STORE_URL;
	/**
	 * DEV MODE
	 * @var bool
	 */
	private $dev_mode;
	/**
	 * Contact Support URL
	 * @var string
	 */
	private $support_url = 'https://wpdeveloper.com/support/new-ticket';
	/**
	 * Initializes the license manager client.
	 */
	public function __construct( $product_slug, $product_name, $text_domain ) {
		/**
		 * Set DEV Mode if Exists.
		 */
		$this->dev_mode = defined('EAE_DEV') && EAE_DEV;
		/**
		 * Setup all properties
		 */
		$this->product_slug = $product_slug;
		$this->product_name = $product_name;
		$this->text_domain  = $text_domain;
		$this->item_id      = EAEL_SL_ITEM_ID;
		/**
		 * Initialize all actions.
		 */
		$this->init();
	}
	/**
	 * Adds actions required for class functionality
	 */
	public function init() {
		if ( is_admin() ) {
			/**
			 * Register license settings field.
			 */
			add_action( 'admin_init', array( $this, 'register_license_settings' ) );
			/**
			 * Add License Page into eael_licensing
			 */
			add_action( 'eael_licensing', array( $this, 'render_licenses_page' ) );
			/**
			 * Activate License
			 * this will happens when license already activated before and its checking for its status.
			 */
			add_action( 'admin_init', array( $this, 'activate_license_when_check' ) );
			/**
			 * Activate License
			 */
			add_action( 'admin_init', array( $this, 'activate_license' ) );
			/**
			 * Deactivate License
			 */
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );
			/**
			 * Admin Notices
			 */
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'eael_admin_notices', array( $this, 'admin_notices' ) );
		}
	}
	/**
	 * Creates the settings fields needed for the license settings menu.
	 */
	public function register_license_settings() {
		register_setting( $this->page_slug, $this->product_slug . '-license-key', 'sanitize_license' );
	}
	/**
	 * Sanitize License
	 * @param string $new
	 * @return string
	 */
	public function sanitize_license( $new ) {
		$old = get_option( $this->product_slug . '-license-key', false );
		if ( $old && $old != $new ) {
			delete_option( $this->product_slug . '-license-status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
	/**
	 * This function is responsible for rendering license activation form in settings.
	 * @return void
	 */
	public function render_licenses_page(){
		$license_key = $this->get_license_key();
		$status      = $this->get_license_status();
		$title       = sprintf( __( '%s License', $this->text_domain ), $this->product_name );
		if( $status !== 'valid' ) {
			$this->set_license_key('');
		}
		include_once __DIR__ . '/views/settings.php';
	}
	/**
	 * Updates the license key option
	 * @return bool|string   The product license key, or false if not set
	 */
	public function set_license_key( $license_key ) {
		if( ! empty( $license_key ) ) {
			return update_option( $this->product_slug . '-license-key', $license_key );
		} else {
			return delete_option( $this->product_slug . '-license-key' );
		}
	}
	/**
	 * Gets the currently set license key
	 * @return bool|string   The product license key, or false if not set
	 */
	private function get_license_key(){
		$license = get_option( $this->product_slug . '-license-key' );
		if ( ! $license ) {
			return false;
		}
		return trim( $license );
	}
	/**
	 * Gets the currently set license key in a hidden way
	 * @return string   The product license key
	 */
	private function get_hidden_license_key() {
		$input_string = $this->get_license_key();
		$length = mb_strlen( $input_string ) - 10; // 5 - 5;
		return substr_replace( $input_string, mb_substr( preg_replace( '/\S/', '*', $input_string ), 5, $length ), 5, $length );
	}
	/**
	 * Gets the currently set license data
	 * @param boolean $force_request
	 * @return object|bool
	 */
	private function get_license_data( $force_request = false ) {
		$license_data = get_transient( $this->product_slug . '-license_data' );
		if ( false === $license_data || $force_request ) {
			$license = $this->get_license_key();
			if( empty( $license ) ) {
				return false;
			}
			$body_args = [
				'edd_action' => 'check_license',
				'license' => $license,
			];
			$license_data = $this->remote_post( $body_args );
			if ( is_wp_error( $license_data ) ) {
				$license_data = new \stdClass();
				$license_data->license = 'valid';
				$license_data->payment_id = 0;
				$license_data->license_limit = 0;
				$license_data->site_count = 0;
				$license_data->activations_left = 0;
				$this->set_license_data( $license_data, 30 * MINUTE_IN_SECONDS );
				$this->set_license_status( $license_data->license );
			} else {
				$this->set_license_data( $license_data );
				$this->set_license_status( $license_data->license );
			}
		}
		return $license_data;
	}
	/**
	 * Updates the license status option
	 * @return bool|string   The product license key, or false if not set
	 */
	public function set_license_status( $license_status ) {
		if( ! empty( $license_status ) ) {
			return update_option( $this->product_slug . '-license-status', $license_status );
		}
	}
	/**
	 * Gets the current license status
	 * @return bool|string   The product license key, or false if not set
	 */
	public function get_license_status() {
		$status = get_option( $this->product_slug . '-license-status', false );
		return ($status === false || $status === "" ) ? false : trim( $status );
	}
	/**
	 * Set the license data
	 * @param object $license_data
	 * @param integer $expiration
	 * @return void
	 */
	public function set_license_data( $license_data, $expiration = null ) {
		if ( null === $expiration ) {
			$expiration = $this->dev_mode ? 10 : 12 * HOUR_IN_SECONDS;
		}

		if( isset( $license_data->expires ) && $license_data->expires === 'lifetime' ) {
			$expiration = 0;
		} elseif( isset( $license_data->expires ) ) {
			$expiration = strtotime( $license_data->expires );
		}

		set_transient( $this->product_slug . '-license_data', $license_data, $expiration );
	}
	/**
	 * License Activation
	 * @return void
	 */
	public function activate_license(){
		if( ! isset( $_POST[ $this->product_slug . '_license_activate' ] ) ) {
			return;
		}
		// run a quick security check
		if( ! check_admin_referer( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ) ) {
			return;
		}
		// retrieve the license from the form|$_POST
		$license = isset( $_POST[ $this->product_slug . '-license-key' ] ) ? trim( $_POST[ $this->product_slug . '-license-key' ] ) : false;
		if( $license === false ) {
			$message = __( 'License field can not be empty!', $this->text_domain );
			$this->redirect( $message );
		}
		/**
		 * Get Ready for License Activation Hit
		 */
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
		);
		$request = $this->remote_post( $api_params ); // Hit it.
		/**
		 * If its an error
		 */
		if( is_wp_error( $request ) ) {
			$message = $request->get_error_message();
		}
		/**
		 * Get Formatted Message
		 * if anything goes wrong.
		 */
		$message = $this->get_formatted_message( null, $request );
		/**
		 * Check if anything passed on a message constituting a failure
		 */
		if ( ! empty( $message ) ) {
			$this->redirect( $message );
		}

		// $license_data->license will be either "valid" or "invalid"
		$this->set_license_key( $license );
		$this->set_license_data( $request );
		$this->set_license_status( $request->license );
		$this->redirect();
	}
	/**
	 * License Deactivation
	 * @return void
	 */
	public function deactivate_license(){
		if( ! isset( $_POST[ $this->product_slug . '_license_deactivate' ] ) ) {
			return;
		}
		// run a quick security check
		if( ! check_admin_referer( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ) ) {
			return;
		}
		// retrieve the license from the database
		$license = $this->get_license_key();
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
		);
		$request = $this->remote_post( $api_params ); // HIT IT
		/**
		 * If its an error
		 */
		if( is_wp_error( $request ) ) {
			$message = $request->get_error_message();
			if( ! empty( $message ) ) {
				$this->redirect( $message ); // redirect to settings page with message
			}
		}

		$message = $this->get_formatted_message( null, $request );

		if( $request->license != 'deactivated' ) {
			$message = __( 'An error occurred, please try again', $this->text_domain );
			$this->redirect( $message ); // redirect to settings page with message
		}

		if( $request->license == 'deactivated' ) {
			delete_option( $this->product_slug . '-license-status' );
			delete_option( $this->product_slug . '-license-key' );
			$transient = get_transient( $this->product_slug . '-license_data' );
			if( $transient !== false ) {
				$option = delete_option( '_transient_' . $this->product_slug . '-license_data' );
				if( $option ) {
					delete_option( '_transient_timeout_' . $this->product_slug . '-license_data' );
				}
			}
		}

		$this->redirect(); // Redirect to settings page
	}
	/**
	 * Activate License when check
	 * @return void
	 */
	public function activate_license_when_check(){
		$license_data = $this->get_license_data();
		$status = $this->get_license_status();
		if( $status === 'valid' ) {
			return;
		}

		if( $status === 'inactive' || $status === 'site_inactive' ) {
			$license = $this->get_license_key();
			if( empty( $license ) ) {
				return;
			}
			/**
			 * Get Ready for License Activation Hit
			 */
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
			);
			$request = $this->remote_post( $api_params ); // Hit it.
			/**
			 * If its an error
			 */
			if( is_wp_error( $request ) ) {
				$message = $request->get_error_message();
				if ( ! empty( $message ) ) {
					$this->redirect( $message );
				}
			}
			/**
			 * Get Formatted Message
			 * if anything goes wrong.
			 */
			$message = $this->get_formatted_message( null, $request );
			/**
			 * Check if anything passed on a message constituting a failure
			 */
			if ( ! empty( $message ) ) {
				$this->set_license_status( $request->license !== 'invalid' ? $request->license : $request->error );
				$this->redirect( $message );
			}

			// $license_data->license will be either "valid" or "invalid"
			$this->set_license_key( $license );
			$this->set_license_data( $request );
			$this->set_license_status( $request->license );
			$this->redirect();
		}
	}
	/**
	 * All Type if admin notices
	 * @return void
	 */
	public function admin_notices(){
		$license_data = $this->get_license_data();
		$status = $this->get_license_status();
		if( $license_data !== false ) {
			if( isset( $license_data->license ) ) {
				$status = $license_data->license;
			}
			if( \is_object( $license_data ) && isset( $license_data->error ) ) {
				$message = $this->get_formatted_message( $status, $license_data );
			}
		}
		$status = $status === 'invalid' ? false : $status;
		if( $status === 'http_error' ) {
			return;
		}

		$message = $this->get_formatted_message( $status );

		switch( $status ) {
			case 'expired':
				$message = sprintf(
					__( 'Your license has been expired. Please %1$srenew your license%2$s key to enable updates for %3$s.', $this->text_domain ),
					'<a href="https://wpdeveloper.com/account">', '</a>',
					'<strong>' . $this->product_name . '</strong>'
				);
				break;
			case false:
				$message = sprintf(
					__( 'Please %1$sactivate your license%2$s key to enable updates for %3$s.', $this->text_domain ),
					'<a href="' . admin_url( 'admin.php?page=' . $this->page_slug ) . '">', '</a>',
					'<strong>' . $this->product_name . '</strong>'
				);
				break;
		}

		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['response_message'] ) ) {
			switch( $_GET['sl_activation'] ) {
				case 'false':
					$message = esc_html(urldecode( $_GET['response_message'] ));
					break;
				case 'true':
				default:
					// Try custom message if you want.
					break;
			}
		}

		if( empty( $message ) ) {
			return;
		}

		$output = '<div class="notice notice-error">';
			$output .= '<p>'. $message .'</p>';
		$output .= '</div>';

		echo Helper::eael_wp_kses( $output );
	}
	/**
	 * Its a helper function for HTTP remote post.
	 * @param array $body_args
	 * @return \stdClass|\WP_Error
	 */
	private function remote_post( &$body_args = [] ) {
		$api_params = wp_parse_args(
			$body_args,
			[
				'item_id' => urlencode( $this->item_id ),
				'url'     => home_url(),
			]
		);
		// HIT IT
		$response = wp_remote_post( $this->storeURL, [
			'sslverify' => false,
			'timeout' => 40,
			'body' => $api_params,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', $this->text_domain ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $data ) || ! is_object( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', $this->text_domain ) );
		}

		return $data;
	}
	/**
	 * Its a helper function for getting error message formatted.
	 * @param object $response
	 * @return string
	 */
	private function get_formatted_message( $status = null, &$response = null ){
		$message = '';
		if ( ( isset( $response->success ) && false === boolval( $response->success ) ) || ! is_null( $status ) ) {
			switch( is_null( $status  ) ? $response->error : $status ) {
				case 'expired' :
					if( ! is_null( $response ) ) {
						$message = sprintf(
							__( 'Your license key expired on %s.', $this->text_domain ),
							date_i18n( get_option( 'date_format' ), strtotime( $response->expires, current_time( 'timestamp' ) ) )
						);
					}
					break;
				case 'revoked' :
					$message = __( 'Your license key has been disabled.', $this->text_domain );
					break;
				case 'missing' :
					$message = __( 'Invalid license.', $this->text_domain );
					break;
				case 'invalid' :
				case 'site_inactive' :
				case 'inactive' :
					$message = __( 'Your license is not active for this website.', $this->text_domain );
					break;
				case 'item_name_mismatch' :
					$message = sprintf( '%s %s.', __( 'This appears to be an invalid license key for', $this->text_domain ), $this->product_name );
					break;
				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', $this->text_domain );
					break;
				case 'disabled':
					$message = sprintf(
						__( 'Your license key has been disabled, Please contact the %s %sSupport Team%s.', $this->text_domain ),
						"<strong>$this->product_name</strong>",
						"<a href=". esc_url( $this->support_url ) .">", "</a>"
					);
					break;
				case 'valid':
					$message = '';
					break;
				default :
					$message = __( 'An error occurred, please try again.', $this->text_domain );
					break;
			}
		}
		return $message;
	}
	/**
	 * Its a helper function for redirection.
	 *
	 * @param [type] $message
	 * @return void
	 */
	private function redirect( $message = null, $http_code = 301 ){
		if( is_null( $message ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . $this->page_slug ), $http_code );
			die;
		}

		$redirect = add_query_arg(
			array( 'sl_activation' => 'false', 'response_message' => \urlencode( $message ) ),
			admin_url( 'admin.php?page=' . $this->page_slug )
		);
		wp_safe_redirect( $redirect, $http_code );
		die;
	}
}
