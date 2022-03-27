<?php

namespace ACP\Admin\NetworkPageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\DefaultColumnsRepository;
use AC\ListScreenRepository\Storage;

class Columns implements PageFactoryInterface {

	/**
	 * @var Location\Absolute
	 */
	private $location_core;

	/**
	 * @var DefaultColumnsRepository
	 */
	private $default_columns_repository;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var MenuFactoryInterface
	 */
	private $menu_factory;

	public function __construct( Location\Absolute $location_core, DefaultColumnsRepository $default_columns_repository, Storage $storage, MenuFactoryInterface $menu_factory ) {
		$this->location_core = $location_core;
		$this->default_columns_repository = $default_columns_repository;
		$this->storage = $storage;
		$this->menu_factory = $menu_factory;
	}

	public function create() {
		return new AC\Admin\Page\Columns(
			$this->location_core,
			$this->default_columns_repository,
			new AC\Admin\Section\Partial\Menu( true ),
			$this->storage,
			new AC\Admin\View\Menu( $this->menu_factory->create( 'columns' ) ),
			new AC\Admin\Preference\ListScreen(),
			true
		);
	}

}