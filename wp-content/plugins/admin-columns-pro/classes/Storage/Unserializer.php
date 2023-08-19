<?php

declare(strict_types=1);

namespace ACP\Storage;

interface Unserializer
{

    public function unserialize(string $data): array;

}