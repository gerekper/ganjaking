<?php

namespace ACP\Column\User;

use AC;
use ACP\Export;
use ACP\Search;

/**
 * @since 4.1
 */
class Username extends AC\Column\User\Username
	implements Export\Exportable, Search\Searchable {

	public function export() {
		return new Export\Model\User\Login( $this );
	}

	public function search() {
		return new Search\Comparison\User\UserName();
	}

}