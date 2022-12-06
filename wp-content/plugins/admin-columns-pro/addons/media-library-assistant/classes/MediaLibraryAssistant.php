<?php
declare( strict_types=1 );

namespace ACA\MLA;

use AC\PluginInformation;
use AC\Registerable;

class MediaLibraryAssistant implements Registerable {

	public function register() {
		if ( ! defined( 'MLA_PLUGIN_PATH' ) ) {
			return;
		}

		$services = [
			new Service\IntegratedMlaSupport( new PluginInformation( MLA_PLUGIN_BASENAME . '/index.php' ) ),
		];

		array_map( [ $this, 'register_service' ], $services );
	}

	private function register_service( Registerable $service ): void {
		$service->register();
	}

}