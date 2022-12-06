<?php

namespace ACP\Integration\Filter;

use AC\Integration;
use AC\Integration\Filter;
use AC\Integrations;

class IsProActive implements Filter {

	public function filter( Integrations $integrations ) {
		return new Integrations( array_filter( $integrations->all(), [ $this, 'is_active' ] ) );
	}

	private function is_active( Integration $integration ) {
		return apply_filters( 'acp/integration/active', false, $integration );
	}

}