<?php

namespace ACP\Editing\Service;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Menu implements Service {

	/**
	 * @var Storage\Menu
	 */
	private $storage;

	public function __construct( Storage\Menu $storage ) {
		$this->storage = $storage;
	}

	public function get_view( string $context ): ?View {
		return new View\Menu();
	}

	public function get_value( int $id ) {
		return $this->storage->get( $id );
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

}