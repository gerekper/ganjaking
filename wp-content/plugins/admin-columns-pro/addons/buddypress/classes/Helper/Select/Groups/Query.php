<?php

namespace ACA\BP\Helper\Select\Groups;

use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use BP_Groups_Group;

class Query extends ArrayIterator implements Paginated {

	/**
	 * @var int
	 */
	private $per_page;

	/**
	 * @var array
	 */
	protected $query;

	public function __construct( array $args = [] ) {
		$args = array_merge( [
			'type'        => 'alphabetical',
			'per_page'    => 20,
			'page'        => 1,
			'show_hidden' => true,
		], $args );

		$this->per_page = $args['per_page'];

		$items = BP_Groups_Group::get( $args );

		$this->query = $items;

		parent::__construct( $items['groups'] );
	}

	public function get_total_pages(): int {
		return (int) ceil( $this->query['total'] / $this->per_page );
	}

	public function get_page(): int {
		return (int) filter_input( INPUT_POST, 'page' );
	}

	public function is_last_page(): bool {
		return $this->get_total_pages() <= $this->get_page();
	}

}