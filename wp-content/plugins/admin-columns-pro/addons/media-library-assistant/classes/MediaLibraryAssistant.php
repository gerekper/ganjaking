<?php
declare( strict_types=1 );

namespace ACA\MLA;

use AC;
use AC\Registerable;
use ACA\MLA\Service;
use ACP\Service\IntegrationStatus;

class MediaLibraryAssistant implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! defined( 'MLA_PLUGIN_PATH' ) ) {
			return;
		}

		$services = [
			new Service\Admin( $this->location ),
			new Service\ColumnGroup(),
			new Service\Editing(),
			new Service\Export(),
			new Service\ListScreens(),
			new Service\TableScreen( $this->location ),
			new IntegrationStatus( 'ac-addon-media-library-assistant' ),
		];

		array_map( [ $this, 'register_service' ], $services );
	}

	private function register_service( Registerable $service ): void {
		$service->register();
	}

}