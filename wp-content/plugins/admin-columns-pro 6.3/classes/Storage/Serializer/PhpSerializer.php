<?php

declare(strict_types=1);

namespace ACP\Storage\Serializer;

use ACP\Storage\Serializer;

final class PhpSerializer implements Serializer
{

    public function serialize(array $data): string
    {
        return (string)var_export($data, true);
    }

}