<?php

declare(strict_types=1);

namespace ACP\Exception;

use ACP\Search\Type\SegmentKey;
use RuntimeException;

final class FailedToSaveSegmentException extends RuntimeException
{

    public function __construct(string $message = null)
    {
        if ($message === null) {
            $message = 'Failed to save segment.';
        }

        parent::__construct($message);
    }

    public static function from_duplicate_key(SegmentKey $key): self
    {
        return new self(sprintf('Duplicate key found for %s.', $key));
    }

}