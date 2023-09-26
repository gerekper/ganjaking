<?php

namespace ACA\JetEngine\Editing\Service;

use ACP;
use ACP\Editing;
use ACP\Editing\View;

abstract class Relationship extends ACP\Editing\Service\BasicStorage implements ACP\Editing\PaginatedOptions {

	/**
	 * @var boolean
	 */
	private $multiple;

	public function __construct( Editing\Storage $storage, $multiple ) {
		$this->multiple = (bool) $multiple;

		parent::__construct( $storage );
	}

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\AjaxSelect() )->set_multiple( $this->multiple );
	}

}