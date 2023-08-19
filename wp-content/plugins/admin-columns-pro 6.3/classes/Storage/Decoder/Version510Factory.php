<?php

declare(strict_types=1);

namespace ACP\Storage\Decoder;

use AC\ListScreenFactory;
use ACP\Storage\Decoder;
use ACP\Storage\DecoderFactory;

final class Version510Factory implements DecoderFactory
{

    private $list_screen_factory;

    public function __construct(ListScreenFactory $list_screen_factory)
    {
        $this->list_screen_factory = $list_screen_factory;
    }

    public function create(array $encoded_data): Decoder
    {
        return new Version510($encoded_data, $this->list_screen_factory);
    }

}