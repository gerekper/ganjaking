<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class TextArea extends View implements Placeholder, MaxLength {

	use MaxlengthTrait,
		PlaceholderTrait;

	public function __construct() {
		parent::__construct( 'textarea' );

		$this->set_rows( 6 );
	}

	/**
	 * @param int $rows
	 */
	public function set_rows( $rows ) {
		$this->set( 'rows', (string) $rows );

		return $this;
	}

}