<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreenCollection;

interface LegacyCollectionDecoder {

	/**
	 * @param array $data
	 *
	 * @return ListScreenCollection
	 */
	public function decode( array $data );

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function can_decode( array $data );

}