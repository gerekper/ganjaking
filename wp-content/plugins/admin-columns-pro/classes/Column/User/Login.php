<?php

namespace ACP\Column\User;

use AC;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Login extends AC\Column\User\Login
	implements Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\User\UserField( 'user_login' );
	}

	public function export() {
		return new Export\Model\User\Login( $this );
	}

	public function search() {
		return new Search\Comparison\User\UserName();
	}

}