<?php

namespace ACP\Storage\ListScreen;

interface Unserializer {

	/**
	 * @param string $serialized_list_screen
	 *
	 * @return array
	 */
	public function unserialize( $serialized_list_screen );

}