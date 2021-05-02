<?php

namespace ACP\Storage\ListScreen\Serializer;

use ACP\Storage\ListScreen\Serializer;

class PhpSerializer implements Serializer {

	public function serialize( array $encoded_list_screen ) {
		return (string) var_export( $encoded_list_screen, true );
	}

}