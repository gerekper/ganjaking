<?php

namespace ACP\Admin\NetworkPageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP\Admin\Page;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;

class Tools implements PageFactoryInterface {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var MenuFactoryInterface
	 */
	private $menu_factory;

	public function __construct( Location\Absolute $location, Storage $storage, MenuFactoryInterface $menu_factory ) {
		$this->location = $location;
		$this->storage = $storage;
		$this->menu_factory = $menu_factory;
	}

	public function create() {
		$page = new Page\Tools(
			$this->location,
			new AC\Admin\View\Menu( $this->menu_factory->create( 'import-export' ) )
		);

		$page->add_section( new Export( $this->storage, true ) )
		     ->add_section( new Import() );

		return $page;
	}

}