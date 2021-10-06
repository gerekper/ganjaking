<?php

namespace ACP\Editing\Service\Media;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class MetaData implements Service {

	/**
	 * @var View
	 */
	private $view;

	/**
	 * @var string
	 */
	private $sub_key;

	/**
	 * @var Storage\Post\Meta
	 */
	private $storage;

	public function __construct( View $view, $sub_key ) {
		$this->view = $view;
		$this->sub_key = $sub_key;
		$this->storage = new Storage\Post\Meta( '_wp_attachment_metadata' );
	}

	public function get_view( $context ) {
		return $this->view;
	}

	public function get_value( $id ) {
		$data = $this->storage->get( $id );

		return isset( $data[ $this->sub_key ] )
			? $data[ $this->sub_key ]
			: false;
	}

	public function update( Request $request ) {
		$id = (int) $request->get( 'id' );

		$data = $this->storage->get( $id );

		if ( ! $data || ! is_array( $data ) ) {
			$data = [];
		}

		$data[ $this->sub_key ] = $request->get( 'value' );

		return $this->storage->update( $id, $data );
	}

}