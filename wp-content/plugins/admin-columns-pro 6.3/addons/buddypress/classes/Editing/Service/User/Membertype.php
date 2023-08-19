<?php

namespace ACA\BP\Editing\Service\User;

use ACP;
use ACP\Editing\Service;
use ACP\Editing\View;

class Membertype implements Service {

	/**
	 * @var array
	 */
	private $options;

	public function __construct( array $options ) {
		$this->options = $options;
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Select( $this->options );
	}

	public function get_value( $id ) {
		return bp_get_member_type( $id );
	}

	public function update( int $id, $data ): void {
		bp_set_member_type( $id, $data );
	}

}