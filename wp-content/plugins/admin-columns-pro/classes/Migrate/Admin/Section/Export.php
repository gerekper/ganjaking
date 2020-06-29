<?php

namespace ACP\Migrate\Admin\Section;

use AC\ListScreenRepository\Storage;
use AC\Renderable;
use AC\View;
use ACP\Migrate\Admin\Table;

class Export implements Renderable {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $repository ) {
		$this->storage = $repository;
	}

	public function render() {
		$view = new View( [
			'table' => new Table\Export( $this->storage ),
		] );
		$view->set_template( 'admin/section-export' );

		return $view->render();
	}

}