<?php

declare(strict_types=1);

namespace ACP\Storage;

interface Serializer
{

    public function serialize(array $data): string;

}