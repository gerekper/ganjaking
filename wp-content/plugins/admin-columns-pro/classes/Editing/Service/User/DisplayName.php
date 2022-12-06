<?php

namespace ACP\Editing\Service\User;

use AC;
use ACP\Editing\RemoteOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper;

class DisplayName extends BasicStorage implements RemoteOptions {

	public function __construct() {
		parent::__construct( new Storage\User\DisplayName() );
	}

	public function get_view( string $context ): ?View {
		if ( $context === self::CONTEXT_BULK ) {
			return null;
		}

		return new View\RemoteSelect();
	}

	public function get_remote_options( $id = null ) {
		$options = [];
		$nickname = ac_helper()->user->get_user_field( 'display_name', $id );
		$user_login = ac_helper()->user->get_user_field( 'user_login', $id );
		$first_name = ac_helper()->user->get_user_field( 'first_name', $id );
		$last_name = ac_helper()->user->get_user_field( 'last_name', $id );

		$options[ $nickname ] = $nickname;
		$options[ $user_login ] = $user_login;

		if ( ! empty( $first_name ) ) {
			$options[ $first_name ] = $first_name;
		}

		if ( ! empty( $last_name ) ) {
			$options[ $last_name ] = $last_name;
		}

		if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
			$options[ sprintf( '%s %s', $first_name, $last_name ) ] = sprintf( '%s %s', $first_name, $last_name );
			$options[ sprintf( '%s %s', $last_name, $first_name ) ] = sprintf( '%s %s', $last_name, $first_name );
		}

		return new AC\Helper\Select\Options\Paginated(
			new Helper\Select\Options\SinglePage(),
			AC\Helper\Select\Options::create_from_array( $options )
		);
	}

}