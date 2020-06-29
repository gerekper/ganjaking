<?php

namespace ACP\Storage\ListScreen\Unserializer;

use ACP\Storage\ListScreen\Unserializer;

class JsonUnserializer implements Unserializer {

	/**
	 * @inheritDoc
	 */
	public function unserialize( $serialized_list_screen ) {
		return json_decode( $serialized_list_screen, true );
	}

}