<?php

namespace ACA\BP\Editing\Service\User;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP\Editing\Service;
use ACP\Editing\View;
use ACP\Editing\View\Toggle;

class Status implements Service {

	public function get_view( string $context ): ?View {
		$options = new ToggleOptions(
			new Option( 0, __( 'Active', 'buddypress' ) ),
			new Option( 1, __( 'Spammer', 'buddypress' ) )
		);

		return new Toggle( $options );
	}

	public function get_value( $id ) {
		return ac_helper()->user->get_user_field( 'user_status', $id );
	}

	public function update( int $id, $data ): void {
		global $wpdb;

		$wpdb->update(
			$wpdb->users,
			[ 'user_status' => $data ],
			[ 'ID' => $id ]
		);

		clean_user_cache( $id );
	}

}