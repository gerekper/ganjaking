<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder Plugin
 *
 * @package WPBakeryPageBuilder
 *
 */

/**
 * Manage license
 *
 * Activation/deactivation is done via support portal and does not use Envato username and
 * api_key anymore
 */
class Vc_License {

	/**
	 * Option name where license key is stored
	 *
	 * @var string
	 */
	protected static $license_key_option = 'js_composer_purchase_code';

	/**
	 * Option name where license key token is stored
	 *
	 * @var string
	 */
	protected static $license_key_token_option = 'license_key_token';

	/**
	 * @var string
	 */
	protected static $support_host = 'https://support.wpbakery.com';

	/**
	 * @var string
	 */
	public $error = null;

	public function init() {

		if ( 'vc-updater' === vc_get_param( 'page' ) ) {
			// clear transient for check the license
			$site_url = self::getSiteUrl();
			$transient_name = 'wpb_license_key_check_' . md5( $site_url );
			delete_transient( $transient_name );
			$activate = vc_get_param( 'activate' );
			$deactivate = vc_get_param( 'deactivate' );
			if ( $activate ) {
				$this->finishActivationDeactivation( true, $activate );
			} elseif ( $deactivate ) {
				$this->finishActivationDeactivation( false, $deactivate );
			}
		}

		add_action( 'wp_ajax_vc_get_activation_url', array(
			$this,
			'startActivationResponse',
		) );
		add_action( 'wp_ajax_vc_get_deactivation_url', array(
			$this,
			'startDeactivationResponse',
		) );

		add_action( 'wp_ajax_nopriv_vc_check_license_key', array(
			vc_license(),
			'checkLicenseKeyFromRemote',
		) );

		add_action( 'admin_notices', array(
			$this,
			'outputLastError',
		) );

		add_action( 'vc_after_init', array(
			$this,
			'checkLicenseKey',
		) );
	}

	/**
	 * Output notice
	 *
	 * @param string $message
	 * @param bool $success
	 */
	public function outputNotice( $message, $success = true ) {
		echo sprintf( '<div class="%s"><p>%s</p></div>', (bool) $success ? 'updated' : 'error', wp_kses( $message, array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'target' => array(),
				'rel' => array(),
			),
		) ) );
	}

	/**
	 * Show error
	 *
	 * @param string $error
	 */
	public function showError( $error ) {
		// save error in db
		// get current errors from db
		$errors = get_option( 'wpb_license_errors', array() );
		// add new error
		$errors[] = [
			'message' => $error,
			'time' => time(),
		];
		// save errors
		update_option( 'wpb_license_errors', $errors );
	}

	/**
	 * Output last error
	 */
	public function outputLastError() {
		// get errors from db
		$errors = get_option( 'wpb_license_errors', array() );
		// filter errors by time < 10 min
		$errors = array_filter( $errors, function ( $error ) {
			return $error['time'] > time() - 600;
		} );
		// update db
		update_option( 'wpb_license_errors', $errors );
		// output all errors
		// unique
		$errors = array_unique( array_column( $errors, 'message' ) );
		foreach ( $errors as $error ) {
			$this->outputNotice( $error, false );
		}
	}

	/**
	 * Output successful activation message
	 */
	public function outputActivatedSuccess() {
		$this->outputNotice( esc_html__( 'WPBakery Page Builder successfully activated.', 'js_composer' ), true );
	}

	/**
	 * Output successful deactivation message
	 */
	public function outputDeactivatedSuccess() {
		$this->outputNotice( esc_html__( 'WPBakery Page Builder successfully deactivated.', 'js_composer' ), true );
	}

	/**
	 * Finish pending activation/deactivation
	 *
	 * 1) Make API call to support portal
	 * 2) Receive success status and license key
	 * 3) Set new license key
	 *
	 * @param bool $isActivation
	 * @param string $user_token
	 *
	 * @return bool
	 */
	public function finishActivationDeactivation( $isActivation, $user_token ) {
		if ( ! $this->isValidToken( $user_token ) ) {
			$this->showError( esc_html__( 'Token is not valid or has expired', 'js_composer' ) );

			return false;
		}

		if ( $isActivation ) {
			$url = self::$support_host . '/finish-license-activation';
		} else {
			$url = self::$support_host . '/finish-license-deactivation';
		}

		$params = array(
			'body' => array( 'token' => $user_token ),
			'timeout' => 30,
		);
		// FIX SSL SNI
		$filter_add = true;
		if ( function_exists( 'curl_version' ) ) {
			$version = curl_version();
			if ( version_compare( $version['version'], '7.18', '>=' ) ) {
				$filter_add = false;
			}
		}
		if ( $filter_add ) {
			add_filter( 'https_ssl_verify', '__return_false' );
		}
		$response = wp_remote_post( $url, $params );

		if ( $filter_add ) {
			remove_filter( 'https_ssl_verify', '__return_false' );
		}

		if ( is_wp_error( $response ) ) {
			$this->showError( sprintf( esc_html__( '%s. Please try again.', 'js_composer' ), $response->get_error_message() ) );

			return false;
		}
		if ( 200 !== $response['response']['code'] ) {
			$this->showError( sprintf( esc_html__( 'Server did not respond with OK: %s', 'js_composer' ), $response['response']['code'] ) );

			return false;
		}

		$json = json_decode( $response['body'], true );

		if ( ! $json || ! isset( $json['status'] ) ) {
			$this->showError( esc_html__( 'Invalid response structure. Please contact us for support.', 'js_composer' ) );

			return false;
		}

		if ( ! $json['status'] ) {
			$this->showError( esc_html__( 'Something went wrong. Please contact us for support.', 'js_composer' ) );

			return false;
		}

		if ( $isActivation ) {
			if ( ! isset( $json['license_key'] ) || ! $this->isValidFormat( $json['license_key'] ) ) {
				$this->showError( esc_html__( 'Invalid response structure. Please contact us for support.', 'js_composer' ) );

				return false;
			}

			$this->setLicenseOptions( $json['license_key'] );

			add_action( 'admin_notices', array(
				$this,
				'outputActivatedSuccess',
			) );
		} else {
			$this->setLicenseKey( '' );

			$this->setLicenseOptions();

			add_action( 'admin_notices', array(
				$this,
				'outputDeactivatedSuccess',
			) );
		}

		return true;
	}

	/**
	 * Set some options related to license.
	 *
	 * @param string $licenseKey
	 */
	public function setLicenseOptions( $licenseKey = '' ) {
		$this->setLicenseKey( $licenseKey );
		update_option( 'wpb_license_errors', array() );
		$this->setLicenseKeyToken( '' );
	}

	/**
	 * @return boolean
	 */
	public function isActivated() {
		return (bool) $this->getLicenseKey();
	}

	/**
	 * Check license key from remote
	 *
	 * Function is used by support portal to check if VC w/ specific license is still installed
	 */
	public function checkLicenseKeyFromRemote() {
		$license_key = vc_request_param( 'license_key' );

		if ( ! $this->isValid( $license_key ) ) {
			$response = array(
				'status' => false,
				'error' => esc_html__( 'Invalid license key', 'js_composer' ),
			);
		} else {
			$response = array( 'status' => true );
		}

		die( wp_json_encode( $response ) );
	}

	public function checkLicenseKey() {
		$site_url = self::getSiteUrl();
		// Send request to remote server to check is license is activated on this domain
		$license_key = $this->getLicenseKey();
		if ( empty( $license_key ) ) {
			return;
		}
		$transient_key = 'wpb_license_key_check_' . md5( $site_url );
		// if transient cache exists skip
		if ( false !== get_transient( $transient_key ) ) {
			return;
		}

		$url = self::$support_host . '/check-license-key';
		$params = array(
			'body' => array(
				'license_key' => $license_key,
				'domain' => $site_url,
			),
			'timeout' => 30,
		);
		// FIX SSL SNI
		$filter_add = true;
		if ( function_exists( 'curl_version' ) ) {
			$version = curl_version();
			if ( version_compare( $version['version'], '7.18', '>=' ) ) {
				$filter_add = false;
			}
		}
		if ( $filter_add ) {
			add_filter( 'https_ssl_verify', '__return_false' );
		}
		$response = wp_remote_get( $url, $params );
		if ( $filter_add ) {
			remove_filter( 'https_ssl_verify', '__return_false' );
		}
		if ( is_wp_error( $response ) ) {
			$this->showError( sprintf( esc_html__( '%s. Please try again.', 'js_composer' ), $response->get_error_message() ) );

			return false;
		}

		if ( 200 !== $response['response']['code'] ) {
			$this->showError( sprintf( esc_html__( 'Server did not respond with OK: %s', 'js_composer' ), $response['response']['code'] ) );

			return false;
		}
		$json = json_decode( $response['body'], true );
		if ( ! $json || ! isset( $json['result'] ) ) {
			set_transient( 'wpb_license_key_check', true, 600 ); // 10 minutes wait for next check in case if error

			return false;
		}
		if ( ! $json['result'] ) {
			// license not found or not activated or activated on another domain
			$message = $json['message'];
			// force deactivate license
			$this->setLicenseKey( '' );
			$this->setLicenseKeyToken( '' );

			$this->showError( $message );
			set_transient( $transient_key, true, 600 ); // 10 minutes wait for next check in case if error

			return false;
		}
		// all good
		set_transient( $transient_key, true, 86400 ); // 24 hours wait for next check

		return true;
	}

	/**
	 * Generate action URL
	 *
	 * @return string
	 */
	public function generateActivationUrl() {
		$token = sha1( $this->newLicenseKeyToken() );
		$url = esc_url( self::getSiteUrl() );
		$redirect = esc_url( vc_updater()->getUpdaterUrl() );

		return sprintf( '%s/activate-license?token=%s&url=%s&redirect=%s', self::$support_host, $token, $url, $redirect );
	}

	/**
	 * Generate action URL
	 *
	 * @return string
	 */
	public function generateDeactivationUrl() {
		$license_key = $this->getLicenseKey();
		$token = sha1( $this->newLicenseKeyToken() );
		$url = esc_url( self::getSiteUrl() );
		$redirect = esc_url( vc_updater()->getUpdaterUrl() );

		return sprintf( '%s/deactivate-license?license_key=%s&token=%s&url=%s&redirect=%s', self::$support_host, $license_key, $token, $url, $redirect );
	}

	/**
	 * Start activation process and output redirect URL as JSON
	 */
	public function startActivationResponse() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'manage_options' )->validateDie()->part( 'settings' )->can( 'vc-updater-tab' )->validateDie();

		$response = array(
			'status' => true,
			'url' => $this->generateActivationUrl(),
		);

		die( wp_json_encode( $response ) );
	}

	/**
	 * Start deactivation process and output redirect URL as JSON
	 */
	public function startDeactivationResponse() {
		vc_user_access()->checkAdminNonce()->validateDie( 'Failed nonce check' )->wpAny( 'manage_options' )->validateDie( 'Failed access check' )->part( 'settings' )->can( 'vc-updater-tab' )
			->validateDie( 'Failed access check #2' );

		$response = array(
			'status' => true,
			'url' => $this->generateDeactivationUrl(),
		);

		die( wp_json_encode( $response ) );
	}

	/**
	 * Set license key
	 *
	 * @param string $license_key
	 */
	public function setLicenseKey( $license_key ) {
		if ( vc_is_network_plugin() ) {
			update_site_option( 'wpb_js_' . self::$license_key_option, $license_key );
		} else {
			vc_settings()->set( self::$license_key_option, $license_key );
		}
	}

	/**
	 * Get license key
	 *
	 * @return string
	 */
	public function getLicenseKey() {
		if ( vc_is_network_plugin() ) {
			$value = get_site_option( 'wpb_js_' . self::$license_key_option );
		} else {
			$value = vc_settings()->get( self::$license_key_option );
		}

		return $value;
	}

	/**
	 * Check if specified license key is valid
	 *
	 * @param string $license_key
	 *
	 * @return bool
	 */
	public function isValid( $license_key ) {
		return $license_key === $this->getLicenseKey();
	}

	/**
	 * Set up license activation notice if needed
	 *
	 * Don't show notice on dev environment
	 */
	public function setupReminder() {
		if ( self::isDevEnvironment() ) {
			return;
		}

		$version1 = isset( $_COOKIE['vchideactivationmsg_vc11'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['vchideactivationmsg_vc11'] ) ) : '';

		if ( ! $this->isActivated() && ( empty( $version1 ) || version_compare( $version1, WPB_VC_VERSION, '<' ) ) && ! ( vc_is_network_plugin() && is_network_admin() ) ) {
			add_action( 'admin_notices', array(
				$this,
				'adminNoticeLicenseActivation',
			) );
		}
	}

	/**
	 * Check if current environment is dev
	 *
	 * Environment is considered dev if host is:
	 * - ip address
	 * - tld is local, dev, wp, test, example, localhost or invalid
	 * - no tld (localhost, custom hosts)
	 *
	 * @param string $host Hostname to check. If null, use HTTP_HOST
	 *
	 * @return boolean
	 */
	public static function isDevEnvironment( $host = null ) {
		if ( ! $host ) {
			$host = self::getSiteUrl();
		}

		$chunks = explode( '.', $host );

		if ( 1 === count( $chunks ) ) {
			return true;
		}

		if ( in_array( end( $chunks ), array(
			'local',
			'dev',
			'wp',
			'test',
			'example',
			'localhost',
			'invalid',
		), true ) ) {
			return true;
		}

		if ( preg_match( '/^[0-9\.]+$/', $host ) ) {
			return true;
		}

		return false;
	}

	public function adminNoticeLicenseActivation() {
		if ( vc_is_network_plugin() ) {
			update_site_option( 'wpb_js_composer_license_activation_notified', 'yes' );
		} else {
			vc_settings()->set( 'composer_license_activation_notified', 'yes' );
		}
		$redirect = esc_url( vc_updater()->getUpdaterUrl() );
		$first_tag = 'style';
		$second_tag = 'script';
		// @codingStandardsIgnoreStart
		?>
		<<?php echo esc_attr( $first_tag ); ?>>
			.vc_license-activation-notice {
				position: relative;
			}
		</<?php echo esc_attr( $first_tag ); ?>>
		<<?php echo esc_attr( $second_tag ); ?>>
			(function ( $ ) {
				var setCookie = function ( c_name, value, exdays ) {
					var exdate = new Date();
					exdate.setDate( exdate.getDate() + exdays );
					var c_value = encodeURIComponent( value ) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
					document.cookie = c_name + "=" + c_value;
				};
				$( document ).off( 'click.vc-notice-dismiss' ).on( 'click.vc-notice-dismiss',
					'.vc-notice-dismiss',
					function ( e ) {
						e.preventDefault();
						var $el = $( this ).closest(
							'#vc_license-activation-notice' );
						$el.fadeTo( 100, 0, function () {
							$el.slideUp( 100, function () {
								$el.remove();
							} );
						} );
						setCookie( 'vchideactivationmsg_vc11',
							'<?php echo esc_attr( WPB_VC_VERSION ); ?>',
							30 );
					} );
			})( window.jQuery );
		</<?php echo esc_attr( $second_tag ); ?>>
		<?php
		echo '<div class="updated vc_license-activation-notice" id="vc_license-activation-notice"><p>' . sprintf( esc_html__( 'Hola! Would you like to receive automatic updates and unlock premium support? Please %sactivate your copy%s of WPBakery Page Builder.', 'js_composer' ), '<a href="' . esc_url( wp_nonce_url( $redirect ) ) . '">', '</a>' ) . '</p>' . '<button type="button" class="notice-dismiss vc-notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice.', 'js_composer' ) . '</span></button></div>';

		// @codingStandardsIgnoreEnd
	}

	/**
	 * Get license key token
	 *
	 * @return string
	 */
	public function getLicenseKeyToken() {
		$value = vc_is_network_plugin() ? get_site_option( self::$license_key_token_option ) : get_option( self::$license_key_token_option );

		return $value;
	}

	/**
	 * Set license key token
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	public function setLicenseKeyToken( $token ) {
		if ( vc_is_network_plugin() ) {
			$value = update_site_option( self::$license_key_token_option, $token );
		} else {
			$value = update_option( self::$license_key_token_option, $token );
		}

		return $value;
	}

	/**
	 * Return new license key token
	 *
	 * Token is used to change license key from remote location
	 *
	 * Format is: timestamp|20-random-characters
	 *
	 * @return string
	 */
	public function generateLicenseKeyToken() {
		$token = current_time( 'timestamp' ) . '|' . vc_random_string( 20 );

		return $token;
	}

	/**
	 * Generate and set new license key token
	 *
	 * @return string
	 */
	public function newLicenseKeyToken() {
		$token = $this->generateLicenseKeyToken();

		$this->setLicenseKeyToken( $token );

		return $token;
	}

	/**
	 * Check if specified license key token is valid
	 *
	 * @param string $token_to_check SHA1 hashed token
	 * @param int $ttl_in_seconds Time to live in seconds. Default = 20min
	 *
	 * @return boolean
	 */
	public function isValidToken( $token_to_check, $ttl_in_seconds = 1200 ) {
		$token = $this->getLicenseKeyToken();

		if ( ! $token_to_check || sha1( $token ) !== $token_to_check ) {
			return false;
		}

		$chunks = explode( '|', $token );

		if ( intval( $chunks[0] ) < ( current_time( 'timestamp' ) - $ttl_in_seconds ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if license key format is valid
	 *
	 * license key is version 4 UUID, that have form xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
	 * where x is any hexadecimal digit and y is one of 8, 9, A, or B.
	 *
	 * @param string $license_key
	 *
	 * @return boolean
	 */
	public function isValidFormat( $license_key ) {
		$pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

		return (bool) preg_match( $pattern, $license_key );
	}

	/**
	 * @return string
	 */
	public static function getSiteUrl() {
		if ( vc_is_network_plugin() ) {
			return network_site_url();
		} else {
			return site_url();
		}
	}
}
