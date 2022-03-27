<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\IntegrationRepository;
use ACP\Access\PermissionsStorage;
use ACP\Admin\Page;

class Addons implements PageFactoryInterface {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var IntegrationRepository
	 */
	private $integrations;

	/**
	 * @var PermissionsStorage
	 */
	private $permissions_storage;

	/**
	 * @var MenuFactoryInterface
	 */
	private $menu_factory;

	public function __construct( Location\Absolute $location, IntegrationRepository $integrations, PermissionsStorage $permissions_storage, MenuFactoryInterface $menu_factory ) {
		$this->location = $location;
		$this->integrations = $integrations;
		$this->permissions_storage = $permissions_storage;
		$this->menu_factory = $menu_factory;
	}

	public function create() {
		return new Page\Addons(
			$this->location,
			$this->integrations,
			$this->permissions_storage,
			new AC\Admin\View\Menu( $this->menu_factory->create( 'addons' ) )
		);
	}

}