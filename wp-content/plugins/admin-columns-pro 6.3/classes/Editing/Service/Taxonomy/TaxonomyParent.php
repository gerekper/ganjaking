<?php

namespace ACP\Editing\Service\Taxonomy;

use AC;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;

class TaxonomyParent extends BasicStorage implements PaginatedOptions {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( $taxonomy ) {
		parent::__construct( new Storage\Taxonomy\TaxonomyParent( $taxonomy ) );

		$this->taxonomy = $taxonomy;
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'       => $search,
			'page'         => $page,
			'exclude_tree' => $id,
			'taxonomy'     => $this->taxonomy,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);
	}

	public function get_view( string $context ): ?View {
		return ( new View\AjaxSelect() )->set_clear_button( true );
	}

}