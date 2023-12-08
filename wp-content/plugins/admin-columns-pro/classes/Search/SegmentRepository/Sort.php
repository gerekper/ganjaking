<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository;

use ACP\Search\SegmentCollection;

abstract class Sort
{

    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     *
     */
    private $reverse;

    public function __construct(string $order = null)
    {
        if (null === $order) {
            $order = self::ASC;
        }

        $this->reverse = $order === self::DESC;
    }

    protected function sort_by_callable(SegmentCollection $segment_collection, callable $sort): SegmentCollection
    {
        $data = iterator_to_array($segment_collection, false);

        usort($data, $sort);

        if ($this->reverse) {
            $data = array_reverse($data);
        }

        return new SegmentCollection($data);
    }

    abstract public function sort(SegmentCollection $segment_collection): SegmentCollection;

}