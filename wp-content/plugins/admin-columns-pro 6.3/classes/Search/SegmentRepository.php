<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\Type\ListScreenId;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentRepository\Sort;
use ACP\Search\Type\SegmentKey;

interface SegmentRepository
{

    public function generate_key(): SegmentKey;

    public function find(SegmentKey $key): ?Segment;

    public function find_all(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection;

    public function find_all_by_user(
        int $user_id,
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection;

    public function find_all_global(ListScreenId $list_screen_id = null, Sort $sort = null): SegmentCollection;

    /**
     * @throws FailedToSaveSegmentException
     */
    public function create(
        SegmentKey $segment_key,
        ListScreenId $list_screen_id,
        string $name,
        array $url_parameters,
        int $user_id = null
    ): Segment;

    public function delete(SegmentKey $key): void;

}