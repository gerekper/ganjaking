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

	/**
	 * @var bool
	 */
	private $network_only;

	public function __construct( Storage $storage, $network_only ) {
		$this->storage = $storage;
		$this->network_only = $network_only;
	}

	public function render() {
		$view = new View( [
			'table' => new Table\Export( $this->storage, $this->network_only ),
		] );
		$view->set_template( 'admin/section-export' );

		return $view->render();
	}

}