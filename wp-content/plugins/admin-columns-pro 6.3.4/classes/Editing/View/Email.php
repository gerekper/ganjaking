<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Email extends View implements Placeholder, MaxLength {

	use MaxlengthTrait,
		PlaceholderTrait;

	public function __construct() {
		parent::__construct( 'email' );
	}

}