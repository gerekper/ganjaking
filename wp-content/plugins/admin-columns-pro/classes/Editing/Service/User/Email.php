<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Email extends BasicStorage {

	/**
	 * @var string
	 */
	private $placeholder;

	public function __construct( $placeholder ) {
		parent::__construct( new Storage\User\Field( 'user_email' ) );

		$this->placeholder = (string) $placeholder;
	}

	public function get_view( $context ) {
		if ( self::CONTEXT_BULK === $context ) {
			return false;
		}

		$view = new View\Email();

		return $view->set_placeholder( $this->placeholder );
	}

}