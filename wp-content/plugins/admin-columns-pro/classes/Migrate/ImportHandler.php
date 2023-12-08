<?php

declare(strict_types=1);

namespace ACP\Migrate;

use AC\ListScreenCollection;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;
use ACP\ListScreenFactory\PrototypeFactory;
use ACP\ListScreenPreferences;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Decoder\ListScreenDecoder;
use ACP\Storage\Decoder\SegmentsDecoder;
use Exception;

class ImportHandler
{

    private $storage;

    private $decoder_factory;

    private $list_screen_factory;

    public function __construct(
        Storage $storage,
        AbstractDecoderFactory $decoder_factory,
        PrototypeFactory $list_screen_factory
    ) {
        $this->storage = $storage;
        $this->decoder_factory = $decoder_factory;
        $this->list_screen_factory = $list_screen_factory;
    }

    public function handle(array $encoded_data, ListScreenId $id = null): ListScreenCollection
    {
        $list_screens = new ListScreenCollection();

        foreach ($encoded_data as $encoded_item) {
            $decoder = $this->decoder_factory->create($encoded_item);

            if ( ! $decoder instanceof ListScreenDecoder || ! $decoder->has_list_screen()) {
                continue;
            }

            $list_screen = $decoder->get_list_screen();

            if ($id && ! $list_screen->get_id()->equals($id)) {
                continue;
            }

            $overwrites = [];

            if ($decoder instanceof SegmentsDecoder && $decoder->has_segments()) {
                $overwrites['preferences'] = $list_screen->get_preferences();
                $overwrites['preferences'][ListScreenPreferences::SHARED_SEGMENTS] = $decoder->get_segments();
            }

            if ($this->storage->exists($list_screen->get_id())) {
                $overwrites['title'] = sprintf(
                    '%s (%s)',
                    $list_screen->get_title(),
                    __('copy', 'codepress-admin-columns')
                );
            }

            $list_screen = $this->list_screen_factory->create_from_list_screen(
                $list_screen,
                $overwrites
            );

            try {
                $this->storage->save($list_screen);
            } catch (Exception $e) {
                continue;
            }

            $list_screens->add($list_screen);
        }

        return $list_screens;
    }

}