<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreen;

interface Decoder {

	public function decode( array $encoded_list_screen ): ListScreen;

	public function can_decode( array $encoded_list_screen ): bool;

}