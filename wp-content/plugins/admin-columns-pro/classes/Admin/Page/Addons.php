<?php

namespace ACP\Admin\Page;

use AC;
use AC\Asset\Location;
use AC\IntegrationRepository;
use AC\PluginInformation;
use AC\Renderable;
use ACP\Access\PermissionsStorage;
use ACP\Admin;

class Addons extends AC\Admin\Page\Addons {

	/**
	 * @var PermissionsStorage
	 */
	private $permission_repository;

	public function __construct( Location\Absolute $location_lite, IntegrationRepository $integration_repository, PermissionsStorage $permission_repository, Renderable $head ) {
		parent::__construct( $location_lite, $integration_repository, $head );

		$this->permission_repository = $permission_repository;
	}

	protected function render_actions( AC\Integration $addon ) {
		return new Admin\Section\AddonStatus(
			new PluginInformation( $addon->get_basename() ),
			is_multisite(),
			is_network_admin(),
			$this->permission_repository
		);
	}

}