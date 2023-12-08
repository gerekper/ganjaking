<?php

declare(strict_types=1);

namespace ACP\Exception;

use AC\Type\ListScreenId;
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

    public static function from_list_screen_not_available(ListScreenId $id): self
    {
        return new self(sprintf('Could not locate List Screen for %s.', $id));
    }

}