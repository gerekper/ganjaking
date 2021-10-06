<?php

namespace ACP\Editing\Service\Taxonomy;

use AC;
use AC\Request;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View\AjaxSelect;
use ACP\Helper\Select;

class TaxonomyParent implements Service, PaginatedOptions {

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
		$this->storage = new Storage\Taxonomy\Field( $taxonomy, 'parent' );
	}

	public function get_value( $id ) {
		$parent_id = $this->storage->get( $id );

		if ( ! $parent_id ) {
			return false;
		}

		$parent = get_term_by( 'id', $parent_id, $this->taxonomy );

		if ( ! $parent ) {
			return false;
		}

		return [
			$parent->term_id => $parent->name,
		];
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

	public function get_view( $context ) {
		return ( new AjaxSelect() )->set_clear_button( true );
	}

	public function update( Request $request ) {
		return $this->storage->update( $request->get( 'id' ), $request->get( 'value', '' ) );
	}

}