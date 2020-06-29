<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.0
 */
class Email extends AC\Column\User\Email
	implements Editing\Editable, Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Model\User\Email( $this );
	}

	public function filtering() {
		return new Filtering\Model\User\Email( $this );
	}

	public function export() {
		return new Export\Model\User\Email( $this );
	}

	public function search() {
		return new Search\Comparison\User\Email();
	}

}