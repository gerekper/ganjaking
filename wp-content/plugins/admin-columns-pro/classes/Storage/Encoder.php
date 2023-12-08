<?php

declare(strict_types=1);

namespace ACP\Storage;

use AC\ListScreen;
use AC\Plugin\Version;
use ACP\ListScreenPreferences;
use ACP\Search\SegmentCollection;

final class Encoder
{

    private $version;

    /**
     * @var ListScreen
     */
    private $list_screen;

    /**
     * @var SegmentCollection
     */
    private $segments;

    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    public function set_list_screen(ListScreen $list_screen): self
    {
        $this->list_screen = $list_screen;

        return $this;
    }

    public function set_segments(SegmentCollection $segments): self
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
            $preferences = $this->list_screen->get_preferences();

            unset($preferences[ListScreenPreferences::SHARED_SEGMENTS]);

            $encoded_data['list_screen'] = [
                'title'    => $this->list_screen->get_title(),
                'type'     => $this->list_screen->get_key(),
                'id'       => $this->list_screen->has_id() ? (string)$this->list_screen->get_id() : '',
                'updated'  => $this->list_screen->get_updated()->getTimestamp(),
                'columns'  => $this->list_screen->get_settings(),
                'settings' => $preferences,
            ];
        }

        if ($this->segments instanceof SegmentCollection && $this->segments->count()) {
            $encoded_data['segments'] = [];

            foreach ($this->segments as $segment) {
                $encoded_data['segments'][] = [
                    'key'            => (string)$segment->get_key(),
                    'list_screen_id' => (string)$segment->get_list_id(),
                    'name'           => $segment->get_name(),
                    'url_parameters' => $segment->get_url_parameters(),
                    'date_created'   => $segment->get_modified() ? $segment->get_modified()->format('U') : null,
                ];
            }
        }

        return $encoded_data;
    }

}