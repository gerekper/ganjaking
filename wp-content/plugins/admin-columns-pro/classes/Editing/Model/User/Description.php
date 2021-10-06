<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

/**
 * @deprecated 5.6
 */
class Description extends Service\Basic {

	public function __construct() {
		parent::__construct(
			( new View\TextArea() )->set_clear_button( true ),
			new Storage\User\Meta( 'description' )
		);
	}

}