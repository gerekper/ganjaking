<?php

namespace ACP\Storage\ListScreen\Serializer;

use ACP\Storage\ListScreen\Serializer;

class JsonSerializer implements Serializer {

	public function serialize( array $encoded_list_screen ) {
		return (string) json_encode( $encoded_list_screen, JSON_PRETTY_PRINT );
	}

}