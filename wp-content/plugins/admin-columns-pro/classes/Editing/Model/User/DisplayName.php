<?php

namespace ACP\Editing\Model\User;

use AC;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper;

class DisplayName extends Model
	implements PaginatedOptions {

	public function get_edit_value( $id ) {
		$name = ac_helper()->user->get_user_field( 'display_name', $id );

		return [ $name => $name ];
	}

	public function get_view_settings() {
		return [
			'type'               => 'select2_dropdown',
			'ajax_populate'      => true,
			'store_single_value' => true,
		];
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
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

	/**
	 * @param int    $id
	 * @param string $value
	 *
	 * @return bool
	 */
	public function save( $id, $value ) {
		global $wpdb;

		$value = sanitize_user( $value, true );

		$result = $wpdb->update(
			$wpdb->users,
			[ 'display_name' => $value ],
			[ 'ID' => $id ],
			[ '%s' ],
			[ '%d' ]
		);

		clean_user_cache( $id );

		return $result !== false;
	}

}