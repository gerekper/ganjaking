<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class WpPassword extends View implements Placeholder {

	use PlaceholderTrait;

	public function __construct() {
		parent::__construct( 'wp_password' );

		$this->set_placeholder( __( 'Set new password', 'codepress-admin-columns' ) );
	}

}