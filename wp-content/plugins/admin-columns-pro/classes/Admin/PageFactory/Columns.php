<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\Page;
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
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var DefaultColumnsRepository
	 */
	private $default_column_repository;

	/**
	 * @var MenuFactoryInterface
	 */
	private $menu_factory;

	public function __construct( Location\Absolute $location_core, Storage $storage, DefaultColumnsRepository $default_column_repository, MenuFactoryInterface $menu_factory ) {
		$this->location_core = $location_core;
		$this->storage = $storage;
		$this->default_column_repository = $default_column_repository;
		$this->menu_factory = $menu_factory;
	}

	public function create() {
		return new Page\Columns(
			$this->location_core,
			$this->default_column_repository,
			new AC\Admin\Section\Partial\Menu(),
			$this->storage,
			new AC\Admin\View\Menu( $this->menu_factory->create( 'columns' ) ),
			new AC\Admin\Preference\ListScreen()
		);
	}

}