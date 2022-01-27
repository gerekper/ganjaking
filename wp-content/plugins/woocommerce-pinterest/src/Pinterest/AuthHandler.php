<?php namespace Premmerce\WooCommercePinterest\Pinterest;

use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\ServiceContainer;

/**
 * Class AuthHandler
 *
 * Responsible for user authorization
 *
 * @package Premmerce\WooCommercePinterest\Pinterest
 */
class AuthHandler {


	/**
	 * ApiState instance
	 *
	 * @var ApiState
	 */
	private $apiState;

	/**
	 * Base request url for v3 API requests
	 *
	 * @var string
	 */
	private $v3BaseUrl = 'https://connect.woocommerce.com/login/pinterestv3?';

	/**
	 * Basename for transients
	 *
	 * @var string
	 */
	private $transientBasename = 'woocommerce_pinterest_code-';

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $pinterestIntegration;

	/**
	 * AuthHandler constructor.
	 *
	 * @param ApiState $apiState
	 * @param PinterestIntegration $pinterestIntegration
	 */
	public function __construct( ApiState $apiState, PinterestIntegration $pinterestIntegration ) {
		$this->apiState             = $apiState;
		$this->pinterestIntegration = $pinterestIntegration;
	}

	/**
	 * Init endpoints
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'handlePinterestAuthResponse' ) );
		add_action( 'admin_post_woocommerce_pinterest_connect', array( $this, 'connectPinterest' ) );
		add_action( 'admin_post_woocommerce_pinterest_disconnect', array( $this, 'disconnectPinterest' ) );
	}

	/**
	 * Check admin requests for Woocommerce response.
	 * Verify nonce and save token if it is.
	 *
	 */
	public function handlePinterestAuthResponse() {
		$token           = filter_input( INPUT_GET, 'pinterestv3_access_token', FILTER_SANITIZE_STRING );
		$tokenApiVersion = 'v3';

		$nonce = filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_STRING );

		$transientName = $this->transientBasename . $tokenApiVersion;

		if ( $token && $nonce && get_transient( $transientName ) === $nonce ) {
			if ( 'v3' === $tokenApiVersion ) {
				update_option( ApiState::V3_TOKEN_OPTION_NAME, $token );
			}

			delete_transient( $transientName );

			$this->redirectToSettingsPage();
		}
	}

	/**
	 * Authentication
	 * Admin post endpoint. Generate code and redirect user to auth server
	 */
	public function connectPinterest() {
		if ( ! check_admin_referer( 'woocommerce_pinterest_connect', 'woocommerce_pinterest_nonce' ) ) {
			$this->redirectToSettingsPage();
		}

		$code = wp_generate_password( 12, false );

		$apiVersion = filter_input( INPUT_GET, 'api_version', FILTER_SANITIZE_STRING );

		$allowedApiVersions = array( 'v3' );

		if ( ! in_array( $apiVersion, $allowedApiVersions, true ) ) {
			wp_die( 'unknown API version' );
		}

		set_transient( $this->transientBasename . $apiVersion, $code, 5 * 60 );

		$params = array(
			'redirect' => get_admin_url() . '?nonce=' . $code,
			'scopes'   => 'all'
		);

		$url = $this->v3BaseUrl . http_build_query( $params );

		wp_redirect( $url );
	}

	/**
	 * Disconnect account
	 * Admin post endpoint. Remove user data
	 */
	public function disconnectPinterest() {
		if ( check_admin_referer( 'woocommerce_pinterest_disconnect', 'woocommerce_pinterest_nonce' ) ) {
			$apiVersion = filter_input( INPUT_GET, 'api_version', FILTER_SANITIZE_STRING );
			$this->apiState->disconnect( $apiVersion );
		}

		$this->redirectToSettingsPage();
	}

	private function redirectToSettingsPage() {
		wp_safe_redirect( esc_url_raw( $this->pinterestIntegration->getSettingsPageUrl() ) );

		die;
	}
}
