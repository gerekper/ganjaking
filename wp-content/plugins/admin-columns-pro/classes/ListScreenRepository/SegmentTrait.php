<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\Type\ListScreenId;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepositoryWritable;

trait SegmentTrait
{

    /**
     * @var SegmentRepositoryWritable
     */
    protected $segment_repository;

    /**
     * @throws FailedToSaveSegmentException
     */
    private function save_segments(SegmentCollection $segments, ListScreenId $list_screen_id): void
    {
        $current_segments = $this->segment_repository->find_all_shared($list_screen_id);

        foreach ($segments as $segment) {
            if ( ! $current_segments->contains($segment->get_key())) {
                $this->segment_repository->save($segment);
            }
        }

        foreach ($current_segments as $segment) {
            if ( ! $segments->contains($segment->get_key())) {
                $this->segment_repository->delete($segment->get_key());
            }
        }
    }

}