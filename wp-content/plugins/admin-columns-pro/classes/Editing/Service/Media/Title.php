<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Title extends Basic {

	public function __construct() {
		parent::__construct(
			( new View\Text() )->set_js_selector( 'strong > a' ),
			new Storage\Post\Field( 'post_title' )
		);
	}

}