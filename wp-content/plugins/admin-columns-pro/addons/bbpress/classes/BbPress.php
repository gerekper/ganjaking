<?php

namespace ACA\BbPress;

use AC\Registerable;
use ACA\BbPress\Service;

class BbPress implements Registerable {

	public function register() {
		if ( ! class_exists( 'bbPress' ) ) {
			return;
		}

		$services = [
			new Service\Columns(),
			new Service\Editing(),
			new Service\ListScreens(),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registerable ) {
				$service->register();
			}
		}
	}

}