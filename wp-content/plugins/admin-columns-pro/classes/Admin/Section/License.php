<?php

namespace ACP\Admin\Section;

use AC;
use AC\Asset\Assets;
use AC\Asset\Enqueueables;
use AC\Asset\Location;
use AC\Asset\Style;
use AC\View;
use ACP;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

class License extends AC\Admin\Section implements Enqueueables {

	const NAME = 'license';

	/** @var Location */
	private $location;

	/** @var LicenseRepository */
	private $license_repository;

	/**
	 * @var Key
	 */
	private $license_key_repository;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var bool
	 */
	private $is_network_active;

	public function __construct(
		Location $location,
		LicenseRepository $license_repository,
		LicenseKeyRepository $license_key_repository,
		SiteUrl $site_url,
		$is_network_active
	) {
		parent::__construct( self::NAME );

		$this->location = $location;
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->site_url = $site_url;
		$this->is_network_active = $is_network_active;
	}

	public function get_assets() {
		return new Assets( [
			new Style( 'acp-license-manager', $this->location->with_suffix( 'assets/core/css/license-manager.css' ) ),
		] );
	}

	/**
	 * @return string
	 */
	private function render_license_form() {
		$license = null;

		$license_key = $this->license_key_repository->find();

		if ( $license_key ) {
			$license = $this->license_repository->find( $license_key );
		}

		$my_account_link = ac_get_site_utm_url( 'my-account/subscriptions', 'license-activation' );

		if ( $license ) {
			$my_account_link = add_query_arg(
				[
					'subscription_key' => $license->get_key()->get_value(),
					'site_url'         => $this->site_url->get_url(),
				],
				$my_account_link
			);
		}

		$license_info = new AC\View( [
			'license_key'        => $license_key,
			'license'            => $license,
			'is_license_defined' => defined( 'ACP_LICENCE' ) && ACP_LICENCE,
			'license_key_masked' => $license_key ? substr( $license_key->get_value(), 0, 7 ) : null,
			'my_account_link'    => $my_account_link,
		] );

		$license_info->set_template( 'admin/section-license' );

		return $license_info->render();
	}

	public function render() {
		$view = new View( [
			'title'       => __( 'Updates', 'codepress-admin-columns' ),
			'description' => __( 'Enter your license code to receive automatic updates.', 'codepress-admin-columns' ),
			'content'     => $this->render_license_form(),
			'class'       => 'general',
		] );

		$view->set_template( 'admin/page/settings-section' );

		return $view->render();
	}

}