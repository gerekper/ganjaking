<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACA\MetaBox\Sorting;
use ACP;

class Text extends Column implements ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function sorting() {
		return ( new Sorting\Factory\Meta )->create( $this );
	}

	public function search() {
		return ( new Search\Factory\Meta() )->create( $this );
	}

	public function editing() {
		return ( new Editing\ServiceFactory\Input )->create( $this );
	}

}