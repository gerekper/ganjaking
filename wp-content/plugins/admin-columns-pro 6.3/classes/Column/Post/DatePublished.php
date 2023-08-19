<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.4
 */
class DatePublished extends AC\Column\Post\DatePublished
	implements Sorting\Sortable, Filtering\Filterable, Editing\Editable, Search\Searchable, ConditionalFormat\Formattable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'date' );
	}

	public function filtering() {
		return new Filtering\Model\Post\Date( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Date\PostPublished();
	}

	public function editing() {
		return new Editing\Service\Post\Date();
	}

	public function conditional_format(): ?FormattableConfig {
		return new ConditionalFormat\FormattableConfig( new ConditionalFormat\Formatter\DateFormatter\FormatFormatter( 'Y-m-d H:i:s' ) );
	}
}