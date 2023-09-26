<?php

declare(strict_types=1);

namespace ACP\Storage;

use ACP\Exception\DecoderNotFoundException;

final class AbstractDecoderFactory
{

    /**
     * @var DecoderFactory[]
     */
    private $decoder_factories = [];

    public function __construct(array $decoder_factories)
    {
        array_map([$this, 'add_factory'], $decoder_factories);
    }

    private function add_factory(DecoderFactory $decoder_factory): void
    {
        $this->decoder_factories[] = $decoder_factory;
    }

    public function can_create( array $encoded_data ) : bool {
        foreach ($this->decoder_factories as $decoder_factory) {
            $decoder = $decoder_factory->create($encoded_data);

            if ($decoder->has_required_version()) {
                return true;
            }
        }

        return false;
    }

    public function create(array $encoded_data): Decoder
    {
        foreach ($this->decoder_factories as $decoder_factory) {
            $decoder = $decoder_factory->create($encoded_data);

            if ($decoder->has_required_version()) {
                return $decoder;
            }
        }

        throw new DecoderNotFoundException($encoded_data);
    }

}