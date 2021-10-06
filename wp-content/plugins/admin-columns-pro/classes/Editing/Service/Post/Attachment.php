<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Attachment extends Basic {

	public function __construct() {
		parent::__construct(
			( new View\Media() )->set_multiple( true ),
			new Storage\Post\Attachments()
		);
	}

}