<?php

namespace ACP\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Post implements Editing\Service, PaginatedOptions {

	/**
	 * @var View\AjaxSelect
	 */
	protected $view;

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var Editing\PaginatedOptionsFactory
	 */
	protected $options_factory;

	public function __construct( View\AjaxSelect $view, Storage $storage, Editing\PaginatedOptionsFactory $options_factory = null ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->options_factory = $options_factory ?: new PaginatedOptions\Posts();
	}

	public function get_view( string $context ): ?View {
		return $this->view->set_multiple( false );
	}

	private function get_stored_post_id( int $id ) {
		$post_id = $this->storage->get( $id );

		if ( is_array( $post_id ) ) {
			$post_id = reset( $post_id );
		}

		return $this->sanitize_post_id( $post_id );
	}

	public function get_value( int $id ) {
		$post_id = $this->get_stored_post_id( $id );

		if ( ! $post_id || ! get_post( $post_id ) ) {
			return false;
		}

		return [
			$post_id => get_the_title( $post_id ) ?: sprintf( __( '#%d (no title)' ), $post_id ),
		];
	}

	private function sanitize_post_id( $post_id ): ?int {
		return $post_id && is_numeric( $post_id )
			? (int) $post_id
			: null;
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $this->sanitize_post_id( $data ) );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return $this->options_factory->create( $search, $page, $id );
	}

}