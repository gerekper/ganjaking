<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class BeforeMoreTag extends AC\Column\Post\BeforeMoreTag
	implements Filtering\Filterable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'post_content' );
	}

	public function filtering() {
		return new Filtering\Model\Post\BeforeMoreTag( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Comparison\Post\BeforeMoreTag();
	}

}