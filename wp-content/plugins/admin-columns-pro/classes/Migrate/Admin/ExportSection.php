<?php
namespace ACP\Migrate\Admin;

use AC\ListScreenRepository\ListScreenRepository;
use AC\View;
use ACP\Admin\Renderable;

class ExportSection implements Renderable {

	/** @var ListScreenRepository */
	private $repository;

	public function __construct( ListScreenRepository $repository ) {
		$this->repository = $repository;
	}

	public function render() {
		$view = new View( [
			'table' => new Table( $this->repository ),
		] );

		echo $view->set_template( 'admin/section-export' );
	}

}