<?php

namespace ACA\Pods\Editing\Service;

use ACP\Editing;
use ACP\Editing\View;

class FieldStorage extends Editing\Service\BasicStorage {

	/**
	 * @var View
	 */
	private $view;

	public function __construct( Editing\Storage $storage, View $view = null ) {
		parent::__construct( $storage );

		$this->view = $view;
	}

	public function get_view( string $context ): ?View {
		return $this->view ?? ( new View\Text() )->set_clear_button( true );
	}

}