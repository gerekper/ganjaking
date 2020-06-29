<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Export;

/**
 * @since 4.1
 */
class AuthorAvatar extends AC\Column\Comment\AuthorAvatar
	implements Export\Exportable {

	public function export() {
		return new Export\Model\Comment\AuthorAvatar( $this );
	}

}