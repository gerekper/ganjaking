<?php

declare(strict_types=1);

namespace ACP\Storage\Decoder;

use AC\Plugin\Version;
use ACP\Storage\Decoder;

abstract class BaseDecoder implements Decoder
{

    protected $encoded_data;

    public function __construct(array $encoded_data)
    {
        $this->encoded_data = $encoded_data;
    }

    abstract protected function get_version(): Version;

    public function has_required_version(): bool
    {
        if ( ! $this->encoded_data['version']) {
            return false;
        }

        return $this->get_version()->is_lte( new Version($this->encoded_data['version']));
    }

}