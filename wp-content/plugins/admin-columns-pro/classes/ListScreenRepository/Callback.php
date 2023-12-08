<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\ListScreenCollection;
use AC\ListScreenRepository;
use ACP\Exception\DecoderNotFoundException;
use ACP\ListScreenPreferences;
use ACP\Search\SegmentCollection;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use ACP\Storage\Decoder\SegmentsDecoder;
use Closure;

final class Callback implements ListScreenRepository
{

    use ListScreenRepository\ListScreenRepositoryTrait;
    use FilteredListScreenRepositoryTrait;

    private $decoder_factory;

    /**
     * @var Closure
     */
    private $callback;

    public function __construct(AbstractDecoderFactory $decoder_factory, callable $callback)
    {
        $this->decoder_factory = $decoder_factory;
        $this->callback = Closure::fromCallable($callback);
    }

    protected function find_all_from_source(): ListScreenCollection
    {
        $collection = new ListScreenCollection();
        $callback = $this->callback;

        foreach ($callback() as $encoded_list_screen) {
            try {
                $decoder = $this->decoder_factory->create($encoded_list_screen);
            } catch (DecoderNotFoundException $e) {
                continue;
            }

            if ( ! $decoder instanceof ListScreenDecoder || ! $decoder->has_list_screen()) {
                continue;
            }

            $list_screen = $decoder->get_list_screen();

            $segments = $decoder instanceof SegmentsDecoder && $decoder->has_segments()
                ? $decoder->get_segments()
                : new SegmentCollection();

            $list_screen->set_preference(ListScreenPreferences::SHARED_SEGMENTS, $segments);

            $collection->add($list_screen);
        }

        return $collection;
    }

}