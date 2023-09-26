<?php

namespace ACP\Plugin\NetworkUpdate;

use ACP;

class V5000 extends ACP\Plugin\Update\V5000 {

	protected function get_option( $key ) {
		return get_site_option( $key );
	}

	protected function update_option( $key, $value ) {
		return update_site_option( $key, $value );
	}

}