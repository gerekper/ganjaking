<?php

namespace ACP\Editing\Service\User;

use ACP\Editing;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;

class Registered extends Basic {

	public function __construct() {
		parent::__construct(
			new Editing\View\DateTime(),
			new Storage\User\Field( 'user_registered' )
		);
	}

}