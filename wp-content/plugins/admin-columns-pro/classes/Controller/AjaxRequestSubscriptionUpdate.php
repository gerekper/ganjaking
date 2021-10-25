<?php

namespace ACP\Controller;

use AC;
use AC\Registrable;
use ACP\API;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\RequestDispatcher;
use ACP\RequestHandler\SubscriptionDetails;
use ACP\Transient\LicenseCheckTransient;
use ACP\Type\SiteUrl;

class AjaxRequestSubscriptionUpdate implements Registrable {

	/**
	 * @var LicenseCheckTransient
	 */
	private $transient;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var AC\Asset\Location
	 */
	private $location;

	public function __construct( LicenseCheckTransient $transient, LicenseKeyRepository $license_key_repository, LicenseRepository $license_repository, RequestDispatcher $api, SiteUrl $site_url, AC\Asset\Location $location ) {
		$this->transient = $transient;
		$this->license_key_repository = $license_key_repository;
		$this->license_repository = $license_repository;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->location = $location;
	}

	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_script' ] );

		$this->get_ajax_handler()->register();
	}

	public function load_script() {
		if ( $this->transient->is_expired() ) {
			( new AC\Asset\Script( 'acp-license-check', $this->location->with_suffix( 'assets/core/js/license-check.js' ) ) )->enqueue();
		}
	}

	private function get_ajax_handler() {
		$handler = new AC\Ajax\Handler();
		$handler->set_action( 'acp_daily_subscription_update' )
		        ->set_callback( [ $this, 'handle' ] );

		return $handler;
	}

	public function handle() {
		$this->transient->save( DAY_IN_SECONDS );

		$key = $this->license_key_repository->find();

		if ( ! $key ) {
			wp_send_json_error();
		}

		$request_handler = new SubscriptionDetails( $this->license_repository, $this->api );

		$response = $request_handler->handle( new API\Request\SubscriptionDetails( $key, $this->site_url ) );

		if ( $response->has_error() ) {
			wp_send_json_error( $response->get_error()->get_error_message() );
		}

		wp_send_json_success();
	}

}