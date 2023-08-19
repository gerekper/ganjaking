<?php

declare(strict_types=1);

namespace ACP\Storage\Serializer\PhpSerializer;

use ACP\Storage\Serializer;

final class File implements Serializer
{

    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize(array $data): string
    {
        return '<?php' . "\n\n" . 'return ' . $this->serializer->serialize($data) . ';';
    }

}