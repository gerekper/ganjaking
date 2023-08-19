<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Url extends BasicStorage {

	/**
	 * @var string
	 */
	private $placeholder;

	public function __construct( $placeholder ) {
		parent::__construct( new Storage\User\Field( 'user_url' ) );

		$this->placeholder = (string) $placeholder;
	}

	public function get_view( string $context ): ?View {
		return ( new View\Url() )->set_placeholder( $this->placeholder );
	}

}