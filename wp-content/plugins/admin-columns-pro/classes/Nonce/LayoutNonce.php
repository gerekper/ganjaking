<?php

namespace ACP\Nonce;

use AC\Form\Nonce;

class LayoutNonce extends Nonce {

	public function __construct() {
		parent::__construct( 'acp-layout', '_acnonce' );
	}
}