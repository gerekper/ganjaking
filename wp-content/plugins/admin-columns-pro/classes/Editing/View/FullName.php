<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class FullName extends View {

	public function __construct() {
		parent::__construct( 'fullname' );

		$this->set_placeholder_first_name( __( 'First Name', 'codepress-admin-columns' ) );
		$this->set_placeholder_last_name( __( 'Last Name', 'codepress-admin-columns' ) );
	}

	/**
	 * @param string $placeholder_first_name
	 *
	 * @return $this
	 */
	public function set_placeholder_first_name( $placeholder_first_name ) {
		$this->set( 'placeholder_first_name', (string) $placeholder_first_name );

		return $this;
	}

	/**
	 * @param string $placeholder_last_name
	 *
	 * @return $this
	 */
	public function set_placeholder_last_name( $placeholder_last_name ) {
		$this->set( 'placeholder_last_name', (string) $placeholder_last_name );

		return $this;
	}

}