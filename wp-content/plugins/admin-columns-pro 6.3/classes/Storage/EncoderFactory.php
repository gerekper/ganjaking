<?php

declare(strict_types=1);

namespace ACP\Storage;

use AC\Plugin\Version;

final class EncoderFactory
{

    private $version;

    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    public function create(): Encoder
    {
        return new Encoder($this->version);
    }

}