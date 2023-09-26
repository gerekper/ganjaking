<?php

namespace ACA\BP\Helper\Select\Entities;

use AC;
use ACA\BP\Helper\Select;
use BP_Groups_Group;
use WP_Query;

class Group extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	/** @var int */
	private $per_page;

	/**
	 * @var WP_Query
	 */
	protected $query;

	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Select\Value\Group();
		}

		$args = array_merge( [
			'type'        => 'alphabetical',
			'per_page'    => 20,
			'page'        => 1,
			'show_hidden' => true,
		], $args );

		$this->per_page = $args['per_page'];

		$items = BP_Groups_Group::get( $args );

		$this->query = $items;

		parent::__construct( $items['groups'], $value );
	}

	public function get_total_pages() {
		return ceil( $this->query['total'] / $this->per_page );
	}

	public function get_page() {
		return filter_input( INPUT_POST, 'page' );
	}

	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}