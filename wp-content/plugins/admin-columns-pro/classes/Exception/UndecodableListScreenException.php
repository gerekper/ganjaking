<?php

namespace ACP\Exception;

use LogicException;

final class UndecodableListScreenException extends LogicException {

	/**
	 * @var array
	 */
	private $encoded_list_screen;

	public function __construct( array $encoded_list_screen, $code = 0 ) {
		$this->encoded_list_screen = $encoded_list_screen;

		parent::__construct( 'Undecodable List Screen found.', $code );
	}

	/**
	 * @return array
	 */
	public function get_encoded_list_screen() {
		return $this->encoded_list_screen;
	}

}