<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class ReplyTo extends AC\Column\Comment\ReplyTo
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_parent' );
	}

	public function filtering() {
		return new Filtering\Model\Comment\ReplyTo( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\ReplyTo();
	}

}