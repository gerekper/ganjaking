<?php

namespace ACP\Migrate;

use AC\Message;

trait MessageTrait {

	/**
	 * @param string      $message
	 * @param string|null $type
	 */
	protected function set_message( $message, $type = null ) {
		if ( null === $type ) {
			$type = Message::ERROR;
		}

		$notice = new Message\Notice( $message );
		$notice->set_type( $type )
		       ->register();
	}

}