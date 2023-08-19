<?php

namespace ACP\Nonce;

use AC\Form\Nonce;

class LicenseNonce extends Nonce {

	public function __construct() {
		parent::__construct( 'acp-license', '_acnonce' );
	}
}