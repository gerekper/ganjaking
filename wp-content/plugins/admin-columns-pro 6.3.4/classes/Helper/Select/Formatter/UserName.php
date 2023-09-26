<?php

namespace ACP\Helper\Select\Formatter;

use AC;
use WP_User;

class UserName extends AC\Helper\Select\Formatter {

	/**
	 * @var array
	 */
	private $properties;

	public function __construct( AC\Helper\Select\Entities $entities, $properties = [] ) {
		$this->properties = array_merge( [
			'first_name',
			'last_name',
		], $properties );

		parent::__construct( $entities );
	}

	/**
	 * @param WP_User $user
	 *
	 * @return string
	 */
	public function get_label( $user ) {
		$name_parts = [];

		foreach ( $this->properties as $key ) {
			if ( $user->$key ) {
				$name_parts[] = $user->$key;
			}
		}

		$label = implode( ' ', $name_parts );

		if ( ! $label ) {
			$label = $user->user_login;
		}

		$suffix = $user->user_email ?: $user->user_login;

		$label .= sprintf( ' (%s)', $suffix );

		return (string) apply_filters( 'acp/select/formatter/user_name', $label, $user );
	}

}