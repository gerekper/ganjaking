<?php

namespace ACP\QuickAdd;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP\QuickAdd\Admin\HideOnScreen;
use ACP\QuickAdd\Model\Factory;
use ACP\QuickAdd\Model\PostFactory;
use ACP\QuickAdd\Table;

class Addon implements AC\Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location
	 */
	private $location;

	/**
	 * @var AC\Request
	 */
	private $request;

	public function __construct( Storage $storage, Location $location, AC\Request $request ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->request = $request;
	}

	public function register() {
		$preference = new Table\Preference\ShowButton();
		$filter = new Filter();

		Factory::add_factory( new PostFactory() );

		$services = [
			new Controller\AjaxNewItem( $this->storage, $this->request ),
			new Controller\AjaxScreenOption( $this->storage, $preference ),
			new Table\Loader( $this->location, new HideOnScreen\QuickAdd(), $preference, $filter ),
			new Admin\Settings( $filter ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof AC\Registrable ) {
				$service->register();
			}
		}
	}

}