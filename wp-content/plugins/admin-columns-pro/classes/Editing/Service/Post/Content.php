<?php

namespace ACP\Editing\Service\Post;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Content implements Service {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var View
	 */
	private $view;

	public function __construct( View $view ) {
		$this->view = $view;
		$this->storage = new Storage\Post\Field( 'post_content' );
	}

	public function get_value( $id ) {
		return $this->storage->get( $id );
	}

	public function get_view( $context ) {
		return $this->view;
	}

	public function update( Request $request ) {
		return $this->storage->update( (int) $request->get( 'id' ), $request->get( 'value' ) );
	}

}