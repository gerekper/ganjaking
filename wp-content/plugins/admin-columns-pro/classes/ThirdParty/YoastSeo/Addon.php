<?php

namespace ACP\ThirdParty\YoastSeo;

use AC\Registrable;

final class Addon implements Registrable {

	public function register() {
		if ( ! $this->is_active() ) {
			return;
		}

		( new CoreAddon )->register();
	}

	/**
	 * @return bool
	 */
	private function is_active() {
		return defined( 'WPSEO_VERSION' );
	}

}