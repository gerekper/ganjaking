<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Password extends View implements Placeholder, MaxLength {

	use MaxlengthTrait,
		PlaceholderTrait;

	public function __construct() {
		parent::__construct( 'password' );
	}

}