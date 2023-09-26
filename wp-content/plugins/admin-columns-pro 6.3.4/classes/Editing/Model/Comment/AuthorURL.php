<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;

/**
 * @deprecated 5.6
 */
class AuthorURL extends Basic {

	public function __construct() {
		parent::__construct( ( new Editing\View\Url() )->set_clear_button( true ), new Storage\Comment\Field( 'comment_author_url' ) );
	}

}