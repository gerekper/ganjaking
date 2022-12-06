<?php

namespace ACA\JetEngine;

use AC;
use AC\Registerable;
use ACP\Service\IntegrationStatus;

class JetEngine implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! class_exists( 'Jet_Engine', false ) || ! $this->check_minimum_jet_engine_version() ) {
			return;
		}

		$services = [
			new Service\Admin( $this->location ),
			new Service\ColumnInstantiate,
			new Service\ColumnGroups,
			new Service\RelationalColumns(),
			new Service\MetaColumns(),
			new IntegrationStatus( 'ac-addon-jetengine' ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registerable ) {
				$service->register();
			}
		}
	}

	private function check_minimum_jet_engine_version(): bool {
		$jet_engine = jet_engine();

		return $jet_engine
			? version_compare( $jet_engine->get_version(), '2.11.0', '>=' )
			: false;
	}

}