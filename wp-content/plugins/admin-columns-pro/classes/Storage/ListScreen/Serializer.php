<?php

namespace ACP\Storage\ListScreen;

interface Serializer {

	/**
	 * @param array $encoded_list_screen
	 *
	 * @return string
	 */
	public function serialize( array $encoded_list_screen );

}