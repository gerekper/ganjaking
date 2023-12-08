<?php

namespace ACP\Editing\Service\Taxonomy;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;

class TaxonomyParent extends BasicStorage implements PaginatedOptions {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( $taxonomy ) {
		parent::__construct( new Storage\Taxonomy\TaxonomyParent( $taxonomy ) );

		$this->taxonomy = $taxonomy;
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'       => $search,
			'page'         => $page,
			'exclude_tree' => $id,
			'taxonomy'     => $this->taxonomy,
		] );
	}

	public function get_view( string $context ): ?View {
		return ( new View\AjaxSelect() )->set_clear_button( true );
	}

}