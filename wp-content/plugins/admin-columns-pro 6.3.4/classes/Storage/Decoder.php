<?php

declare(strict_types=1);

namespace ACP\Storage;

interface Decoder
{

    public function has_required_version(): bool;

}