<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreenCollection;

interface LegacyCollectionDecoder {

	public function decode( array $data ): ListScreenCollection;

	public function can_decode( array $data ): bool;

}