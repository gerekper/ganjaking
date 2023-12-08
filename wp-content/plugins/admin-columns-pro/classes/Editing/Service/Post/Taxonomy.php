<?php

namespace ACP\Editing\Service\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Taxonomy implements Service, PaginatedOptions {

	/**
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * @var bool
	 */
	private $enable_term_creation;

	/**
	 * @var Storage\Post\Taxonomy
	 */
	private $storage;

	public function __construct( string $taxonomy, bool $enable_term_creation ) {
		$this->taxonomy = $taxonomy;
		$this->enable_term_creation = $enable_term_creation;
		$this->storage = new Storage\Post\Taxonomy( $taxonomy, $enable_term_creation );
	}

	public function get_value( int $id ) {
		return $this->storage->get( $id );
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function get_view( string $context ): ?View {
		$view = new View\AjaxSelect();

		$view->set_multiple( 'post_format' !== $this->taxonomy )
		     ->set_clear_button( true );

		if ( $this->enable_term_creation ) {
			$view->set_tags( true );
		}

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view;
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new \ACP\Helper\Select\Taxonomy\PaginatedFactory() )->create( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->taxonomy,
		] );
	}

}