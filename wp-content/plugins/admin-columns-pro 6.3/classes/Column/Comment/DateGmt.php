<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class DateGmt extends AC\Column\Comment\DateGmt
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'comment_date_gmt' );
	}

	public function filtering() {
		return new Filtering\Model\Comment\DateGmt( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Date\Gmt();
	}

	public function conditional_format(): ?FormattableConfig {
		return new ConditionalFormat\FormattableConfig( new ConditionalFormat\Formatter\DateFormatter\FormatFormatter() );
	}

}