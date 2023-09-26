<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository\Sort;

use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository\Sort;

final class Nullable extends Sort
{

    public function sort(SegmentCollection $segment_collection): SegmentCollection
    {
        return $segment_collection;
    }

}