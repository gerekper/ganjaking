<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;

/**
 * @deprecated 5.6
 */
class Type extends Basic {

	public function __construct() {
		parent::__construct(
			new Editing\View\Text(),
			new Storage\Comment\Field( 'comment_type' )
		);
	}

}