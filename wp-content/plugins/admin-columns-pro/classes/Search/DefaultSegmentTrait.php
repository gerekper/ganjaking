<?php

declare(strict_types=1);

namespace ACP\Search;

use AC;
use ACP;
use ACP\Search\Entity\Segment;
use ACP\Search\Type\SegmentKey;

trait DefaultSegmentTrait
{

    /**
     * @var SegmentRepository
     */
    protected $segment_repository;

    protected function get_default_segment(AC\ListScreen $list_screen): ?Segment
    {
        $segment_key = $this->get_default_segment_key($list_screen);

        if ( ! $segment_key) {
            return null;
        }

        /** @var SegmentCollection $segments */
        $segments = $list_screen->get_preference(ACP\ListScreenPreferences::SHARED_SEGMENTS);

        return $segments->contains($segment_key)
            ? $segments->get($segment_key)
            : null;
    }

    protected function get_default_segment_key(AC\ListScreen $list_screen): ?SegmentKey
    {
        $setting = $list_screen->get_preference(ACP\ListScreenPreferences::FILTER_SEGMENT);

        if ( ! $setting) {
            return null;
        }

        return new SegmentKey((string)$setting);
    }

}