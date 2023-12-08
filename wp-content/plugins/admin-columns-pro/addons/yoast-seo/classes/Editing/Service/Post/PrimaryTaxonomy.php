<?php

namespace ACA\YoastSeo\Editing\Service\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;

class PrimaryTaxonomy implements Editing\Service, Editing\PaginatedOptions {

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( $taxonomy ) {
		$this->storage = new Storage\Post\Meta( '_yoast_wpseo_primary_' . $taxonomy );
		$this->taxonomy = $taxonomy;
	}

	public function get_value( $id ) {
		$term = $this->storage->get( $id );

		if ( ! $term ) {
			$terms = wp_get_post_terms( $id, $this->taxonomy );

			return empty( $terms ) || is_wp_error( $terms )
				? null
				: false;
		}

		$term = get_term( $term, $this->taxonomy );

		return [
			$term->term_id => $term->name,
		];
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function get_view( string $context ): ?View {
		return self::CONTEXT_SINGLE === $context ? new Editing\View\AjaxSelect() : null;
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'     => $search,
			'page'       => $page,
			'taxonomy'   => $this->taxonomy,
			'object_ids' => [ $id ],
		] );
	}

}