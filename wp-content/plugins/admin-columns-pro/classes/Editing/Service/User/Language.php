<?php

namespace ACP\Editing\Service\User;

use ACP\Editing;
use ACP\Editing\Service;
use ACP\Editing\View;

class Language extends Service\Basic {

	public function __construct( array $options ) {
		$options = array_merge( [ '' => _x( 'Site Default', 'default site language' ) ], $options );

		parent::__construct( new View\Select( $options ), new Editing\Storage\User\Meta( 'locale' ) );
	}

}