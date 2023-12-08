<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\Type\ListScreenId;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentRepository\Sort;
use ACP\Search\Type\SegmentKey;

interface SegmentRepository
{

    public function generate_key(): SegmentKey;

    public function find(SegmentKey $key): ?Segment;

    public function find_all(
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection;

    public function find_all_personal(
        int $user_id,
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection;

    public function find_all_shared(
        ListScreenId $list_screen_id = null,
        Sort $sort = null
    ): SegmentCollection;

}