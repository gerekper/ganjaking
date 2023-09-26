<?php

namespace ACA\BP\Editing\Service\Group;

use ACA\BP\Editing\Storage\Group;
use ACP;
use ACP\Editing\View;

class Status extends ACP\Editing\Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Group( 'status' ) );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Select( [
			'public'  => __( 'Public', 'buddypress' ),
			'private' => __( 'Private', 'buddypress' ),
			'hidden'  => __( 'Hidden', 'buddypress' ),
		] );
	}

}