<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class PasswordProtected extends AC\Column\Post\PasswordProtected
	implements Search\Searchable, Editing\Editable, Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'post_password' );
	}

	public function search() {
		return new Search\Comparison\Post\PasswordProtected();
	}

	public function editing() {
		return new Editing\Service\Post\PasswordProtected();
	}

}