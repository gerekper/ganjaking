<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository;

use ACP\Search\Type\SegmentKey;

trait KeyGeneratorTrait
{

    public function generate_key(): SegmentKey
    {
        return new SegmentKey(uniqid());
    }

}