<?php

declare(strict_types=1);

namespace ACP\Storage\Serializer;

use ACP\Storage\Serializer;

final class JsonSerializer implements Serializer
{

    public function serialize(array $data): string
    {
        return (string)json_encode($data, JSON_PRETTY_PRINT);
    }

}