<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP;
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

	public function get_view( $context ) {
		return $this->view->set_multiple( false );
	}

	public function get_value( $id ) {
		$post_id = $this->storage->get( $id );

		if ( is_array( $post_id ) && ! empty( $post_id ) ) {
			$post_id = $post_id[0];
		}

		return $post_id && is_scalar( $post_id )
			? [ $post_id => get_the_title( $post_id ) ]
			: false;
	}

	public function update( Request $request ) {
		$this->storage->update( (int) $request->get( 'id' ), $request->get( 'value' ) );
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		return $this->options_factory->create( $search, $page, $id );
	}

}