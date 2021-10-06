<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP;
use ACP\Editing;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;

class Users implements Service, PaginatedOptions {

	/**
	 * @var Editing\View\AjaxSelect
	 */
	private $view;

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var Editing\PaginatedOptionsFactory
	 */
	private $options_factory;

	public function __construct( Editing\View\AjaxSelect $view, Storage $storage, Editing\PaginatedOptionsFactory $options_factory ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->options_factory = $options_factory;
	}

	public function get_view( $context ) {
		$view = $this->view;

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view;
	}

	public function get_value( $id ) {
		$ids = $this->storage->get( $id );

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return false;
		}

		if ( is_scalar( $ids ) ) {
			$ids = [ $ids ];
		}

		$values = [];

		foreach ( $ids as $_id ) {
			$values[ $_id ] = ac_helper()->user->get_display_name( $_id );
		}

		return $values;
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

		return $this->storage->update( $id, $ids );
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		return $this->options_factory->create( $s, $paged, $id );
	}

}