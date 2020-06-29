<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;

class PasswordProtected extends AC\Column\Post\PasswordProtected
	implements Search\Searchable, Editing\Editable {

	public function search() {
		return new Search\Comparison\Post\PasswordProtected();
	}

	public function editing() {
		return new Editing\Model\Post\Password( $this );
	}

}