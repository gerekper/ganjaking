<?php

declare(strict_types=1);

namespace ACP\Migrate\Export\Response;

use AC\ListScreenCollection;
use ACP\ListScreenPreferences;
use ACP\Migrate\Export\Response;
use ACP\Migrate\MessageTrait;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer\JsonSerializer;

final class File implements Response
{

    use MessageTrait;

    private $list_screens;

    private $encoder_factory;

    private $json_serializer;

    private $segment_collection;

    public function __construct(
        ListScreenCollection $list_screens,
        EncoderFactory $encoder_factory,
        JsonSerializer $json_serializer
    ) {
        $this->list_screens = $list_screens;
        $this->encoder_factory = $encoder_factory;
        $this->json_serializer = $json_serializer;
    }

    public function send(): void
    {
        if ( ! $this->list_screens->count()) {
            $this->set_message(__('No screens selected for export.', 'codepress-admin-columns'));

            return;
        }

        $output = [];

        foreach ($this->list_screens as $list_screen) {
            $output[] = $this->encoder_factory->create()
                                              ->set_list_screen($list_screen)
                                              ->set_segments(
                                                  $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS)
                                              )->encode();
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

    private function get_file_name(): string
    {
        return sprintf(
            '%s-%s.%s',
            'admin-columns-export',
            date('Y-m-d-Hi'),
            'json'
        );
    }

}