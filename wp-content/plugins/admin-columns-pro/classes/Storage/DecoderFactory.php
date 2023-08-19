<?php

declare(strict_types=1);

namespace ACP\Storage;

interface DecoderFactory
{

    public function create( array $encoded_data ): Decoder;

}