<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;

/**
 * @since 4.0
 */
class Title extends AC\Column\Post\Title
	implements Editing\Editable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Model\Post\TitleRaw( $this );
	}

	public function export() {
		return new Export\Model\Post\Title( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Title();
	}

}