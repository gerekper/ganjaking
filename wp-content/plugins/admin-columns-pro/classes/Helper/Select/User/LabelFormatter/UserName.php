<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\User\LabelFormatter;

use ACP\Helper\Select\User\LabelFormatter;
use WP_User;

class UserName implements LabelFormatter {

	public function format_label( WP_User $user ): string {
		return $this->get_label_user( $user );
	}

	public function format_label_unique( WP_User $user ): string {
		return sprintf( '%s (%s)', $this->format_label( $user ), $user->ID );
	}

	private function get_label_user( WP_User $user ): string {
		$label = trim( $user->first_name . ' ' . $user->last_name ) ?: $user->user_login;

		$label = sprintf(
			'%s (%s)',
			$label,
			$user->user_email ?: $user->user_login
		);

		return (string) apply_filters( 'acp/select/formatter/user_name', $label, $user );
	}

}