<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreen;

interface Decoder {

	/**
	 * @param array $encoded_list_screen
	 *
	 * @return ListScreen
	 */
	public function decode( array $encoded_list_screen );

	/**
	 * @param array $encoded_list_screen
	 *
	 * @return bool
	 */
	public function can_decode( array $encoded_list_screen );

}