<?php

namespace ACP\Message;

use AC\Message\InlineMessage;

class Disabled extends InlineMessage {

	public function __construct() {
		parent::__construct(
			$this->get_construct_message(),
			'acp-disabled-listscreen'
		);

		$this->set_type( self::INFO );
	}

	private function get_construct_message() {
		return sprintf(
			'<p>%s</p>',
			sprintf( __( 'These settings are %s and will not be available on the list table.', 'codepress-admin-columns' ),
				sprintf( '<strong>%s</strong>', __( 'Inactive', 'codepress-admin-columns' )
				)
			)
		);
	}

}