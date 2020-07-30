<?php

namespace ACP\ThirdParty\YoastSeo;

use AC;
use AC\Registrable;

final class Addon implements Registrable {

	public function register() {
		if ( ! $this->is_active() ) {
			return;
		}

		( new CoreAddon )->register();

		$plugin_information = new AC\PluginInformation( 'ac-addon-yoast-seo/ac-addon-yoast-seo.php' );

		if ( ! $plugin_information->is_installed() ) {

			// Load the deprecated features and show message to install the add-on
			( new DeprecatedAddon() )->register();
		}
	}

	/**
	 * @return bool
	 */
	private function is_active() {
		return defined( 'WPSEO_VERSION' );
	}

}