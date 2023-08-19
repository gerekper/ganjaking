<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Editing\Storage;
use ACP\Export;
use ACP\Search;

class Title extends AC\Column\Post\Title
	implements Editing\Editable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Service\Basic(
			( new Editing\View\Text() )->set_placeholder( __( 'Add title', 'codepress-admin-columns' ) ),
			new Storage\Post\Field( 'post_title' )
		);
	}

	public function export() {
		return new Export\Model\Post\Title();
	}

	public function search() {
		return new Search\Comparison\Post\Title();
	}

}