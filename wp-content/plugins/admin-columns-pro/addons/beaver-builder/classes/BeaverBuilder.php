<?php

namespace ACA\BeaverBuilder;

use AC;
use AC\Registerable;
use ACA\BeaverBuilder\Service;

class BeaverBuilder implements Registerable {

	public function register() {
		if ( ! class_exists( 'FLBuilderLoader' ) ) {
				return;
		}

		AC\ListScreenFactory::add( new ListScreenFactory\Templates() );
		AC\ListScreenFactory::add( new ListScreenFactory\SavedColumns() );
		AC\ListScreenFactory::add( new ListScreenFactory\SavedModules() );
		AC\ListScreenFactory::add( new ListScreenFactory\SavedRows() );

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