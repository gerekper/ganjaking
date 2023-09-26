<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;

/**
 * @deprecated 5.6
 */
class AuthorEmail extends Basic {

	public function __construct() {
		parent::__construct( new Editing\View\Email(), new Storage\Comment\Field( 'comment_author_email' ) );
	}

}