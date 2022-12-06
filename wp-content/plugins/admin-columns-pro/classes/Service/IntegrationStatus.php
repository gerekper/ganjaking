<?php

namespace ACP\Service;

use AC\Integration;
use AC\Registerable;

class IntegrationStatus implements Registerable {

	/**
	 * @var string
	 */
	private $slug;

	public function __construct( string $slug ) {
		$this->slug = $slug;
	}

	public function register() {
		add_filter( 'acp/integration/active', [ $this, 'is_active' ], 10, 2 );
	}

	public function is_active( $active, Integration $integration ) {
		if ( $integration->get_slug() === $this->slug ) {
			$active = true;
		}

		return $active;
	}

}