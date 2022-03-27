<?php

namespace ACP\Type\Url;

use AC\Type;

class Changelog implements Type\QueryAware {

	use Type\QueryAwareTrait;

	public function __construct( $network, $plugin_name ) {
		$this->url = $network
			? network_admin_url( 'plugin-install.php' )
			: admin_url( 'plugin-install.php' );

		$this->add_one( 'tab', 'plugin-information' );
		$this->add_one( 'section', 'changelog' );
		$this->add_one( 'plugin', (string) $plugin_name );
	}

}