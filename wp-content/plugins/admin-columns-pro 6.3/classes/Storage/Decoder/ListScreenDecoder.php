<?php

declare(strict_types=1);

namespace ACP\Storage\Decoder;

use AC\ListScreen;
use ACP\Storage\Decoder;

interface ListScreenDecoder extends Decoder
{

    public function has_list_screen(): bool;

    public function get_list_screen(): ListScreen;

}