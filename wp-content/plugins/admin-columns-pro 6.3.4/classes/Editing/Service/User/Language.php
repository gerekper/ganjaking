<?php

namespace ACP\Editing\Service\User;

use ACP\Editing;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\View;

class Language extends BasicStorage {

	/**
	 * @var array
	 */
	private $options;

	public function __construct( array $options ) {
		parent::__construct( new Editing\Storage\User\Meta( 'locale' ) );

		$this->options = $options;

	}

	public function get_view( string $context ): ?View {
		$options = array_merge( [ '' => _x( 'Site Default', 'default site language' ) ], $this->options );

		return new View\Select( $options );
	}

}