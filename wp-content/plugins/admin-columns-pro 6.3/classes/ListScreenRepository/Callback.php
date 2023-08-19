<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository;
use AC\ListScreenRepository\Filter;
use AC\Type\ListScreenId;
use ACP\Exception\DecoderNotFoundException;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use Closure;

final class Callback implements ListScreenRepository
{

    use ListScreenRepository\ListScreenRepositoryTrait;

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

    protected function find_from_source(ListScreenId $id): ?ListScreen
    {
        $list_screens = (new Filter\ListId($id))->filter(
            $this->find_all()
        );

        return $list_screens->get_first() ?: null;
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

            $collection->add($decoder->get_list_screen());
        }

        return $collection;
    }

    protected function find_all_by_key_from_source(string $key): ListScreenCollection
    {
        return (new Filter\ListKey($key))->filter(
            $this->find_all()
        );
    }

}