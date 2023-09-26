<?php

declare(strict_types=1);

namespace ACP\Exception;

use LogicException;

final class DecoderNotFoundException extends LogicException
{

    private $encoded_data;

    public function __construct(array $encoded_data, $code = 0)
    {
        $this->encoded_data = $encoded_data;

        parent::__construct('Could not find a suitable decoder.', $code);
    }

    public function get_encoded_data(): array
    {
        return $this->encoded_data;
    }

}