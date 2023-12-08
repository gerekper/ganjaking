<?php

namespace ACA\ACF\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Taxonomy extends Service\BasicStorage implements PaginatedOptions {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( string $taxonomy, Storage $storage ) {
		$this->taxonomy = $taxonomy;

		parent::__construct( $storage );
	}

	public function get_view( string $context ): ?View {
		$view = new View\AjaxSelect();
		$view->set_clear_button( true );

		return $view;
	}

	public function get_value( int $id ) {
		$terms = ac_helper()->taxonomy->get_terms_by_ids(
			$this->storage->get( $id ),
			$this->taxonomy
		);

		$values = [];

		foreach ( $terms as $term ) {
			$values[ $term->term_id ] = $term->name;
		}

		return $values;
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new \ACP\Helper\Select\Taxonomy\PaginatedFactory() )->create( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->taxonomy,
		] );
	}

}