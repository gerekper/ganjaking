<?php

namespace ACP\Column\User;

use AC;
use ACP\Export;
use ACP\Sorting;

/**
 * @since 4.0
 */
class Posts extends AC\Column\User\Posts
	implements Sorting\Sortable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model\User\PostCount( [ 'post' ], [ 'publish', 'private' ] );
	}

	public function export() {
		return new Export\Model\User\Posts( $this );
	}

}