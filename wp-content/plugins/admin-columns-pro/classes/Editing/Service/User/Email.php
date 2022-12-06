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

	public function get_view( string $context ): ?View {
		if ( self::CONTEXT_BULK === $context ) {
			return null;
		}

		return ( new View\Email() )->set_placeholder( $this->placeholder );
	}

}