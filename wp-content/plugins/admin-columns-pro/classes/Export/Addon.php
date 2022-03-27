<?php

namespace ACP\Export;

use AC\Asset\Location;
use AC\ListScreenRepository;
use AC\Registrable;
use ACP;
use ACP\Export\Asset;
use ACP\Export\RequestHandler\Ajax\FileName;
use ACP\RequestAjaxHandlers;
use ACP\RequestAjaxParser;

class Addon implements Registrable {

	/**
	 * @var Location
	 */
	private $location;

	/**
	 * @var ListScreenRepository
	 */
	private $list_screen_repository;

	public function __construct( Location $location, ListScreenRepository $list_screen_repository ) {
		$this->location = $location;
		$this->list_screen_repository = $list_screen_repository;
	}

	public function register() {
		$request_ajax_handlers = new RequestAjaxHandlers();
		$request_ajax_handlers->add( 'acp-export-file-name', new FileName( $this->list_screen_repository ) );

		$services = [
			new Admin(),
			new RequestAjaxParser( $request_ajax_handlers ),
			new Settings( $this->location ),
			new TableScreen( $this->location ),
			new TableScreenOptions( $this->location ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registrable ) {
				$service->register();
			}
		}
	}

}