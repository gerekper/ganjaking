<?php

namespace ACP\Asset\Script;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Nonce\Ajax;

class PluginUpdatesCheck extends Script {

	public function __construct( Absolute $location ) {
		parent::__construct( 'acp-plugins-update-check', $location );
	}

	public function register() {
		parent::register();

		$this->add_inline_variable( 'ACP_PLUGIN_UPDATES_CHECK', [
			'nonce' => ( new Ajax() )->create(),
		] );
	}

}