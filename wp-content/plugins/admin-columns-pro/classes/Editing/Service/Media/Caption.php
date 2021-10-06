<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Caption extends Basic {

	public function __construct() {
		parent::__construct(
			new View\TextArea(),
			new Storage\Post\Field( 'post_excerpt' )
		);
	}

}