<?php

namespace ACA\BP;

use AC;
use AC\Registerable;
use ACA\BP\Service;
use ACP\Service\IntegrationStatus;

final class BuddyPress implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! class_exists( 'BuddyPress', false ) ) {
			return;
		}

		$services = [
			new Service\Admin( $this->location ),
			new Service\Columns(),
			new Service\ListScreens(),
			new Service\Table( $this->location ),
			new IntegrationStatus( 'ac-addon-buddypress')
		];

		array_map( [ $this, 'register_service' ], $services );
	}

	private function register_service( $service ) {
		if ( $service instanceof Registerable ) {
			$service->register();
		}
	}

}