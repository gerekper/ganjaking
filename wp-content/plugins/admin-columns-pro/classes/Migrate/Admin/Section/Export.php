<?php

namespace ACP\Migrate\Admin\Section;

use AC\ListScreenCollection;
use AC\ListScreenRepository\Storage;
use AC\Renderable;
use AC\View;
use ACP\Migrate\Admin\Table;

class Export implements Renderable {

	private $storage;

	private $list_screens;

	public function __construct( Storage $storage, ListScreenCollection $list_screens ) {
		$this->storage = $storage;
		$this->list_screens = $list_screens;
	}

	public function render() {
		$view = new View( [
			'table' => new Table\Export( $this->storage, $this->list_screens ),
		] );

		return $view->set_template( 'admin/section-export' )
		            ->render();
	}

}