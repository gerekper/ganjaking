<?php

declare(strict_types=1);

namespace ACP\Storage\Unserializer;

use ACP\Exception\UnserializeException;
use ACP\Storage\Unserializer;

final class JsonUnserializer implements Unserializer
{

    /**
     * @throws UnserializeException
     */
    public function unserialize(string $data): array
    {
        $output = json_decode($data, true);

        if (null === $output) {
            throw new UnserializeException($data);
        }

        return $output;
    }

}