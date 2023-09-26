<?php

declare(strict_types=1);

namespace ACP\Storage;

use AC\ListScreen;
use AC\Plugin\Version;
use ACP\Search\SegmentCollection;

final class Encoder
{

    private $version;

    private $list_screen;

    private $segments;

    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    public function set_list_screen(ListScreen $list_screen) : self
    {
        $this->list_screen = $list_screen;

        return $this;
    }

    public function set_segments(SegmentCollection $segments) : self
    {
        $this->segments = $segments;

        return $this;
    }

    public function encode(): array
    {
        $encoded_data = [
            'version' => (string)$this->version,
        ];

        if ($this->list_screen instanceof ListScreen) {
            $encoded_data['list_screen'] = [
                'title'    => $this->list_screen->get_title(),
                'type'     => $this->list_screen->get_key(),
                'id'       => $this->list_screen->get_layout_id(),
                'updated'  => $this->list_screen->get_updated()->getTimestamp(),
                'columns'  => $this->list_screen->get_settings(),
                'settings' => $this->list_screen->get_preferences(),
            ];
        }

        if ($this->segments instanceof SegmentCollection && $this->segments->count()) {
            $encoded_data['segments'] = [];

            foreach ($this->segments as $segment) {
                $encoded_data['segments'][] = [
                    'key'            => (string)$segment->get_key(),
                    'list_screen_id' => (string)$segment->get_list_screen_id(),
                    'user_id'        => $segment->get_user_id(),
                    'name'           => $segment->get_name(),
                    'url_parameters' => $segment->get_url_parameters(),
                ];
            }
        }

        return $encoded_data;
    }

}