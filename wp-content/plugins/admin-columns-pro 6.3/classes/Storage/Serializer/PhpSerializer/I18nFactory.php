<?php

declare(strict_types=1);

namespace ACP\Storage\Serializer\PhpSerializer;

use ACP\Storage\Serializer;

final class I18nFactory
{

    public function create(Serializer $serializer, string $text_domain): Serializer
    {
        return new I18n($serializer, $text_domain);
    }

}