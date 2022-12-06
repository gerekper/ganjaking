<?php

namespace ACA\Pods;

use AC;
use AC\Registerable;
use ACA\Pods\Service;
use ACP\Service\IntegrationStatus;

class Pods implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		$min_required_pods_version = '2.7';

		if ( ! function_exists( 'pods' ) || ! defined( 'PODS_VERSION' ) || ! version_compare( PODS_VERSION, $min_required_pods_version, '>=' ) ) {
			return;
		}

		$services = [
			new Service\Columns(),
			new Service\Scripts( $this->location ),
			new IntegrationStatus( 'ac-addon-pods' ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registerable ) {
				$service->register();
			}
		}
	}

}