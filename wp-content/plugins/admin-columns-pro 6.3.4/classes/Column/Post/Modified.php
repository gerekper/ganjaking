<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Modified extends AC\Column\Post\Modified
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable, Search\Searchable, ConditionalFormat\Formattable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'modified' );
	}

	public function editing() {
		return new Editing\Service\Post\Modified();
	}

	public function filtering() {
		return new Filtering\Model\Post\Modified( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Date\PostModified();
	}

	public function conditional_format(): ?FormattableConfig {
		return new ConditionalFormat\FormattableConfig( new Formatter\DateFormatter\FormatFormatter() );
	}

}