<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Registered extends AC\Column\User\Registered
	implements Filtering\Filterable, Sorting\Sortable, Editing\Editable, Search\Searchable, ConditionalFormat\Formattable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'registered' );
	}

	public function filtering() {
		return new Filtering\Model\User\Registered( $this );
	}

	public function editing() {
		return new Editing\Service\User\Registered();
	}

	public function search() {
		return new Search\Comparison\User\Date\Registered();
	}

	public function conditional_format(): ?FormattableConfig {
		return new ConditionalFormat\FormattableConfig( new ConditionalFormat\Formatter\DateFormatter\FormatFormatter( 'Y-m-d H:i:s' ) );
	}

}