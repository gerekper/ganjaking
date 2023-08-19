<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use RuntimeException;

class Password extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\User\Field( 'user_pass' ) );
	}

	public function get_view( string $context ): ?View {
		return $context === self::CONTEXT_BULK
			? null
			: new View\WpPassword();
	}

	public function get_value( int $id ) {
		if ( ! current_user_can( 'administrator' ) ) {
			return null;
		}

		return '';
	}

	public function update( int $id, $data ): void {
		if ( ! current_user_can( 'administrator' ) ) {
			throw new RuntimeException( __( "You're not allowed to change the password" ) );
		}

		if ( ! $data ) {
			throw new RuntimeException( __( "A password is required" ) );
		}

		parent::update( $id, $data );
	}

}