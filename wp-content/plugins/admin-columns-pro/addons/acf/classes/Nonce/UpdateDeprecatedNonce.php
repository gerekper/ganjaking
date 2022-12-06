<?php

namespace ACA\ACF\Nonce;

use AC\Form\Nonce;

class UpdateDeprecatedNonce extends Nonce {

	public function __construct() {
		parent::__construct( 'acf-deprecated-columns-updater', '_acnonce' );
	}
}