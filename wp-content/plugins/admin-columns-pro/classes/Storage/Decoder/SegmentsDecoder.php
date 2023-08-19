<?php

declare(strict_types=1);

namespace ACP\Storage\Decoder;

use ACP\Search\SegmentCollection;
use ACP\Storage\Decoder;

interface SegmentsDecoder extends Decoder
{

    public function has_segments(): bool;

    public function get_segments(): SegmentCollection;

}