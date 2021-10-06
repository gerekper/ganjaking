<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

/**
 * @deprecated 5.6
 */
class TitleRaw extends Basic {

	public function __construct() {
		parent::__construct(
			new View\Text(),
			new Storage\Post\Field( 'post_title' )
		);
	}

}