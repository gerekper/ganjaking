<?php

declare(strict_types=1);

namespace ACP\Exception;

use RuntimeException;

final class UnserializeException extends RuntimeException
{

    private $data;

    public function __construct(string $data)
    {
        parent::__construct('Failed to unserialize encoded data.');

        $this->data = $data;
    }

    public function get_data(): string
    {
        return $this->data;
    }

}