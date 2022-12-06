<?php

namespace ACA\MLA\Service;

use AC\Plugin\Version;
use AC\PluginInformation;
use AC\Registerable;

class IntegratedMlaSupport implements Registerable {

	private $plugin_information;

	public function __construct( PluginInformation $plugin_information ) {
		$this->plugin_information = $plugin_information;
	}

	public function register() {
		if ( method_exists( 'MLACore', 'register_list_screen' ) && $this->plugin_information->get_version()->is_lt( new Version( '3.05' ) ) ) {
			remove_action( 'ac/list_screens', 'MLACore::register_list_screen' );
		}
	}

}