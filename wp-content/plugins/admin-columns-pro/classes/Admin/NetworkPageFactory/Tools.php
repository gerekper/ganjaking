<?php

namespace ACP\Admin\NetworkPageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Sort\Label;
use AC\ListScreenRepository\Storage;
use AC\Table\ListKeysFactoryInterface;
use ACP\Admin\Page;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;

class Tools implements PageFactoryInterface {

	private $location;

	private $storage;

	private $menu_factory;

	private $list_keys_factory;

	public function __construct(
		Location\Absolute $location,
		Storage $storage,
		MenuFactoryInterface $menu_factory,
		ListKeysFactoryInterface $list_keys_factory
	) {
		$this->location = $location;
		$this->storage = $storage;
		$this->menu_factory = $menu_factory;
		$this->list_keys_factory = $list_keys_factory;
	}

	private function get_list_screens(): ListScreenCollection {
		$list_screens = [];

		foreach ( $this->list_keys_factory->create()->all() as $list_key ) {
			if ( $list_key->is_network() ) {
				$list_screens[] = $this->storage->find_all_by_key( $list_key, new Label() )->get_copy();
			}
		}

		return new ListScreenCollection( array_merge( ...$list_screens ) );
	}

	public function create() {
		$page = new Page\Tools(
			$this->location,
			new AC\Admin\View\Menu( $this->menu_factory->create( 'import-export' ) )
		);

		$page->add_section( new Export( $this->storage, $this->get_list_screens() ) )
		     ->add_section( new Import() );

		return $page;
	}

}