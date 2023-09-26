<?php

declare(strict_types=1);

namespace ACP\Migrate\Export\Response;

use AC\ListScreenCollection;
use ACP\Migrate\Export\Response;
use ACP\Migrate\MessageTrait;
use ACP\Search\SegmentCollection;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer\JsonSerializer;

final class File implements Response
{

    use MessageTrait;

    private $list_screens;

    private $encoder_factory;

    private $json_serializer;

    public function __construct(
        ListScreenCollection $list_screens,
        SegmentCollection $segment_collection,
        EncoderFactory $encoder_factory,
        JsonSerializer $json_serializer
    ) {
        $this->list_screens = $list_screens;
        $this->encoder_factory = $encoder_factory;
        $this->json_serializer = $json_serializer;
        $this->segment_collection = $segment_collection;
    }

    /**
     * @return void
     */
    public function send()
    {
        if ( ! $this->list_screens->count()) {
            $this->set_message(__('No screens selected for export.', 'codepress-admin-columns'));

            return;
        }

        $segments = [];

        foreach ($this->segment_collection as $segment) {
            $segments[(string)$segment->get_list_screen_id()][] = $segment;
        }

        $output = [];

        foreach ($this->list_screens as $list_screen) {
            $encoder = $this->encoder_factory->create();
            $encoder->set_list_screen($list_screen);

            if (array_key_exists($list_screen->get_id()->get_id(), $segments)) {
                $encoder->set_segments(
                    new SegmentCollection($segments[(string)$list_screen->get_id()])
                );
            }

            $output[] = $encoder->encode();
        }

        $headers = [
            'content-disposition' => 'attachment; filename="' . $this->get_file_name() . '"',
            'content-type'        => 'application/json',
        ];

        foreach ($headers as $header => $value) {
            header($header . ': ' . $value);
        }

        echo $this->json_serializer->serialize($output);

        exit;
    }

    private function get_file_name()
    {
        return sprintf(
            '%s-%s.%s',
            'admin-columns-export',
            date('Y-m-d-Hi'),
            'json'
        );
    }

}