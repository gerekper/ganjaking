<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP;
use ACP\Editing;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Posts implements Editing\Service, PaginatedOptions {

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
		$view = $this->view->set_multiple( true );

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true )->set_revisioning( false );
		}

		return $view;
	}

	public function get_value( $id ) {
		$ids = $this->storage->get( $id );

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return [];
		}

		return array_map( 'get_the_title', array_combine( $ids, $ids ) );
	}

	public function update( Request $request ) {
		$params = $request->get( 'value' );
		$id = (int) $request->get( 'id' );

		if ( ! isset( $params['method'] ) ) {
			$params = [
				'method' => 'replace',
				'value'  => $params,
			];
		}

		switch ( $params['method'] ) {
			case 'add':
				$ids = array_merge( array_keys( $this->get_value( $id ) ), $params['value'] );

				break;
			case 'remove':
				$ids = array_diff( array_keys( $this->get_value( $id ) ), $params['value'] );

				break;
			default:
				$ids = $params['value'];
		}

		$this->storage->update( $id, $ids );
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		return $this->options_factory->create( $search, $page, $id );
	}

}