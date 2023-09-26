<?php

namespace ACP\Editing\PaginatedOptions;

use ACP\Editing\PaginatedOptionsFactory;
use ACP\Helper;

class Posts implements PaginatedOptionsFactory {

	/**
	 * @var string[]
	 */
	private $post_types;

	/**
	 * @var array
	 */
	private $args;

	public function __construct( $post_types = null, array $args = [] ) {
		$this->post_types = empty( $post_types ) ? [ 'any' ] : (array) $post_types;
		$this->args = $args;
	}

	public function create( $search, $page, $id = null ) {
		$args = array_merge( [
			'post_type' => $this->post_types,
		], $this->args );

		return new Helper\Select\Paginated\Posts( $search, $page, $args );
	}

}