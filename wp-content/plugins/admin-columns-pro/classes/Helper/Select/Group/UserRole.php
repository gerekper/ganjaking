<?php

namespace ACP\Helper\Select\Group;

use AC;
use WP_User;

class UserRole extends AC\Helper\Select\Group {

	/**
	 * @var WP_User[]
	 */
	private $helper;

	/**
	 * @param AC\Helper\Select\Formatter $formatter
	 */
	public function __construct( AC\Helper\Select\Formatter $formatter ) {
		$this->helper = new AC\Helper\User();

		parent::__construct( $formatter );
	}

	/**
	 * @param WP_User                 $user
	 * @param AC\Helper\Select\Option $option
	 *
	 * @return string
	 */
	public function get_label( $user, AC\Helper\Select\Option $option ) {
		return $this->helper->get_role_name( $user->roles[0] );
	}

}