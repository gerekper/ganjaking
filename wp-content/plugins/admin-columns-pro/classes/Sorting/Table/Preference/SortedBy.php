<?php

namespace ACP\Sorting\Table\Preference;

use AC;
use ACP\Sorting\Table\Preference;

class SortedBy extends Preference {

	public function __construct( $key ) {
		parent::__construct( $key, new AC\Preferences\Site( 'sorted_by' ) );
	}

}