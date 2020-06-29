<?php

namespace ACP\Export;

use AC\Asset\Location;
use AC\Registrable;
use ACP;
use ACP\Export\Asset;

class Addon implements Registrable {

	/**
	 * @var Location
	 */
	private $location;

	public function __construct( Location $location ) {
		$this->location = $location;
	}

	public function register() {
		$services = [
			new Admin( new ExportDirectory() ),
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