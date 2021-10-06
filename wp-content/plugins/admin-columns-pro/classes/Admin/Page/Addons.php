<?php

namespace ACP\Admin\Page;

use AC;
use AC\Asset\Location;
use AC\IntegrationRepository;
use AC\PluginInformation;
use AC\Renderable;
use ACP\Admin;
use ACP\Entity\License;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;

class Addons extends AC\Admin\Page\Addons {

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	public function __construct( Location\Absolute $location_lite, IntegrationRepository $integration_repository, LicenseKeyRepository $license_key_repository, LicenseRepository $license_repository, Renderable $head ) {
		parent::__construct( $location_lite, $integration_repository, $head );

		$this->license_key_repository = $license_key_repository;
		$this->license_repository = $license_repository;
	}

	/**
	 * @return License|null
	 */
	private function get_license() {
		$key = $this->license_key_repository->find();

		return $key
			? $this->license_repository->find( $key )
			: null;
	}

	protected function render_actions( AC\Integration $addon ) {
		return new Admin\Section\AddonStatus(
			new PluginInformation( $addon->get_basename() ),
			is_multisite(),
			is_network_admin(),
			$this->get_license()
		);
	}

}