<?php

namespace ACP\Editing\Service\Comment;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Content extends Basic {

	public function __construct() {
		parent::__construct(
			( new View\TextArea() )->set_clear_button( true ),
			new Storage\Comment\Field( 'comment_content' )
		);
	}

}