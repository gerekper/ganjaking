<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository\Sort;

use ACP\Search\Entity\Segment;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository\Sort;

final class Name extends Sort
{

    public function sort(SegmentCollection $segment_collection): SegmentCollection
    {
        $callable = static function (Segment $a, Segment $b) {
            return $a->get_name() <=> $b->get_name();
        };

        return $this->sort_by_callable($segment_collection, $callable);
    }

}