<?php

namespace ACP\Column\Media;

use AC;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.0
 */
class MediaParent extends AC\Column\Media\MediaParent
	implements Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function filtering() {
		return new Filtering\Model\Post\PostParent( $this );
	}

	public function export() {
		return new Export\Model\Post\PostParent( $this );
	}

	public function search() {
		return new Search\Comparison\Post\PostParent( 'any' );
	}

}