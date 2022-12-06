<?php

namespace ACA\BeaverBuilder;

use AC\Registerable;
use ACA\BeaverBuilder\Service;

class BeaverBuilder implements Registerable {

	public function register() {
		if ( ! class_exists( 'FLBuilderLoader' ) ) {
			return;
		}

		$services = [
			new Service\ListScreens(),
			new Service\PostTypes(),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registerable ) {
				$service->register();
			}
		}
	}

}