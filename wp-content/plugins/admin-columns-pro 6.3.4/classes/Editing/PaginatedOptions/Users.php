<?php

namespace ACP\Editing\PaginatedOptions;

use ACP\Editing\PaginatedOptionsFactory;
use ACP\Helper;

class Users implements PaginatedOptionsFactory {

	/**
	 * @var array
	 */
	private $args;

	public function __construct( array $args = [] ) {
		$this->args = $args;
	}

	public function create( $search, $page, $id = null ) {
		return new Helper\Select\Paginated\Users( $search, $page, $this->args );
	}

}