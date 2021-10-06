<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class PingStatus extends AC\Column\Post\PingStatus
	implements Filtering\Filterable, Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'ping_status' );
	}

	public function editing() {
		return new Editing\Service\Post\PingStatus();
	}

	public function filtering() {
		return new Filtering\Model\Post\PingStatus( $this );
	}

	public function search() {
		return new Search\Comparison\Post\PingStatus();
	}

}