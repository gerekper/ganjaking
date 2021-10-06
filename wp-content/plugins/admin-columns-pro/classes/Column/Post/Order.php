<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Order extends AC\Column\Post\Order
	implements Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\OrderBy( 'menu_order' );
	}

	public function editing() {
		return new Editing\Service\Post\Order();
	}

	public function search() {
		return new Search\Comparison\Post\Order();
	}

}