<?php

namespace ACP\Asset\Script;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Nonce\Ajax;

class LicenseCheck extends Script {

	public function __construct( Absolute $location ) {
		parent::__construct( 'acp-license-check', $location );
	}

	public function register() {
		parent::register();

		$this->add_inline_variable( 'ACP_LICENCE_CHECK', [
			'nonce' => ( new Ajax() )->create(),
		] );
	}

}