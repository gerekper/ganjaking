<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;
use WP_Error;

class Username extends Model {

	public function get_edit_value( $id ) {
		return ac_helper()->user->get_user_field( 'user_login', $id );
	}

	public function get_view_settings() {
		return [
			'type'         => 'text',
			'js'           => [
				'selector' => 'strong > a',
			],
			'display_ajax' => false,
		];
	}

	/**
	 * @param int    $id
	 * @param string $value
	 *
	 * @return bool|WP_Error
	 */
	public function save( $id, $value ) {
		global $wpdb;

		$value = sanitize_user( $value, true );

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(1) FROM {$wpdb->users} WHERE user_login = %s AND ID != %d", $value, $id ) ) ) {
			return new WP_Error( 'cacie_error_username_exists', __( 'The username already exists.', 'codepress-admin-columns' ) );
		}

		$result = $wpdb->update(
			$wpdb->users,
			[ 'user_login' => $value ],
			[ 'ID' => $id ],
			[ '%s' ],
			[ '%d' ]
		);

		clean_user_cache( $id );

		return $result !== false;
	}

}