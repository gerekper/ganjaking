<?php

namespace ACP\Editing\Service;

use ACP\Editing\Storage;
use ACP\Editing\View;

class Basic extends BasicStorage {

	/**
	 * @var View
	 */
	private $view;

	public function __construct( View $view, Storage $storage ) {
		parent::__construct( $storage );

		$this->view = $view;
	}

	public function get_view( string $context ): ?View {
		return $this->view;
	}

}