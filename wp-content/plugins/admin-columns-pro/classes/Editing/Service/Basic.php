<?php

namespace ACP\Editing\Service;

use ACP\Editing;
use ACP\Editing\Storage;

class Basic extends BasicStorage {

	/**
	 * @var Editing\View
	 */
	private $view;

	public function __construct( Editing\View $view, Storage $storage ) {
		parent::__construct( $storage );

		$this->view = $view;
	}

	public function get_view( $context ) {
		return $this->view;
	}

}