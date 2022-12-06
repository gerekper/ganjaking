<?php

namespace ACA\Polylang;

use AC;
use AC\Registerable;
use ACA\Polylang\Service;

class Polylang implements Registerable {

	public function register() {
		if ( ! defined( 'POLYLANG_VERSION' ) ) {
			return;
		}

		$services = [
			new Service\Columns(),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registerable ) {
				$service->register();
			}
		}

		add_action( 'ac/table/list_screen', function ( AC\ListScreen $list_screen ) {
			( new Service\ColumnReplacement( $list_screen ) )->register();
		} );
	}

}