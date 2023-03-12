<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\IntegrationRepository;
use ACP\Admin\Page;

class Addons implements PageFactoryInterface {

	private $location;

	private $integrations;

	private $menu_factory;

	public function __construct(
		Location\Absolute $location,
		IntegrationRepository $integrations,
		MenuFactoryInterface $menu_factory
	) {
		$this->location = $location;
		$this->integrations = $integrations;
		$this->menu_factory = $menu_factory;
	}

	public function create() {
		return new Page\Addons(
			$this->location,
			$this->integrations,
			new AC\Admin\View\Menu( $this->menu_factory->create( 'addons' ) )
		);
	}

}