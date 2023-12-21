<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\ListScreenCollection;
use AC\ListScreenRepository;
use AC\ListScreenRepository\ListScreenRepositoryTrait;
use ACP\Exception\DecoderNotFoundException;
use ACP\Exception\UnserializeException;
use ACP\ListScreenPreferences;
use ACP\Search\SegmentCollection;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use ACP\Storage\Decoder\SegmentsDecoder;
use ACP\Storage\FileIterator;
use ACP\Storage\Unserializer\JsonUnserializer;
use ArrayIterator;
use SplFileInfo;

final class Template implements ListScreenRepository, SourceAware
{

    use ListScreenRepositoryTrait;
    use FilteredListScreenRepositoryTrait;

    private $decoder_factory;

    private $json_unserializer;

    private $files = [];

    /**
     * @var ListScreenCollection
     */
    private $list_screens;

    /**
     * @var SourceCollection
     */
    private $sources;

    public function __construct(
        AbstractDecoderFactory $decoder_factory,
        JsonUnserializer $json_unserializer
    ) {
        $this->decoder_factory = $decoder_factory;
        $this->json_unserializer = $json_unserializer;
    }

    public function add_file(string $file): void
    {
        $this->files[] = $file;
    }

    public function get_sources(): SourceCollection
    {
        $this->parse_files();

        return $this->sources;
    }

    protected function find_all_from_source(): ListScreenCollection
    {
        $this->parse_files();

        return $this->list_screens;
    }

    private function parse_files(): void
    {
        $this->list_screens = new ListScreenCollection();
        $this->sources = new SourceCollection();

        $iterator = new ArrayIterator();

        foreach ($this->files as $file) {
            $iterator->append(new SplFileInfo($file));
        }

        $iterator = new FileIterator($iterator, 'json');

        foreach ($iterator as $file) {
            $serialized_data = file_get_contents($file->getRealPath());

            try {
                $encoded_screens = $this->json_unserializer->unserialize($serialized_data);
            } catch (UnserializeException $e) {
                continue;
            }

            foreach ($encoded_screens as $encoded_screen) {
                try {
                    $decoder = $this->decoder_factory->create($encoded_screen);
                } catch (DecoderNotFoundException $e) {
                    continue;
                }

                if ( ! $decoder instanceof ListScreenDecoder || ! $decoder->has_list_screen()) {
                    continue;
                }

                $list_screen = $decoder->get_list_screen();

                $list_screen->set_preference(
                    ListScreenPreferences::SHARED_SEGMENTS,
                    $decoder instanceof SegmentsDecoder && $decoder->has_segments()
                        ? $decoder->get_segments()
                        : new SegmentCollection()
                );

                $this->list_screens->add($list_screen);
                $this->sources->add($list_screen->get_id(), $file->getRealPath());
            }
        }
    }

}