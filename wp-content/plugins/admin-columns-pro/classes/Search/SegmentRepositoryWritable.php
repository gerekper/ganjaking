<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\Type\ListScreenId;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Search\Entity\Segment;
use ACP\Search\Type\SegmentKey;

interface SegmentRepositoryWritable extends SegmentRepository
{

    public function delete(SegmentKey $key): void;

    public function delete_all(ListScreenId $list_screen_id): void;

    public function delete_all_shared(ListScreenId $list_screen_id): void;

    /**
     * @throws FailedToSaveSegmentException
     */
    public function save(Segment $segment): void;

}