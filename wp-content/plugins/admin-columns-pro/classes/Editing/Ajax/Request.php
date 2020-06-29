<?php

namespace ACP\Editing\Ajax;

use AC;

abstract class Request implements AC\Registrable {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @param AC\Request $request
	 */
	public function __construct( AC\Request $request ) {
		$this->request = $request;
	}

	/**
	 * @return string
	 */
	abstract protected function get_action();

	/**
	 * @return bool
	 */
	public function is_request() {
		return $this->request->get( 'ac_action' ) === $this->get_action();
	}

	protected function check_nonce() {
		check_ajax_referer( 'ac-ajax' );
	}

}