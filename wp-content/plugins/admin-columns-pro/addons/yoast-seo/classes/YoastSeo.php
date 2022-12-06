<?php

namespace ACA\YoastSeo;

use AC;
use AC\Registerable;
use ACA\YoastSeo\Service;
use ACP\Service\IntegrationStatus;

class YoastSeo implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	/**
	 * Register hooks
	 */
	public function register() {
		if ( ! defined( 'WPSEO_VERSION' ) ) {
			return;
		}

		$services = [
			new Service\Admin( $this->location ),
			new Service\ColumnGroups(),
			new Service\Columns(),
			new Service\HideFilters(),
			new Service\Table(),
			new IntegrationStatus( 'ac-addon-yoast-seo' ),
		];

		foreach( $services as $service ){
			if( $service instanceof Registerable ){
				$service->register();
			}
		}
	}

}