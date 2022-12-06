<?php

namespace ACP;

use AC\Ajax;
use AC\Registerable;

class RequestAjaxParser implements Registerable {

	/**
	 * @var RequestAjaxHandlers
	 */
	private $handlers;

	public function __construct( RequestAjaxHandlers $handlers ) {
		$this->handlers = $handlers;
	}

	public function register() {
		foreach ( $this->handlers->all() as $action => $handler ) {
			( new Ajax\Handler() )->set_action( $action )
			                      ->set_callback( [ $handler, 'handle' ] )
			                      ->register();
		}
	}

}