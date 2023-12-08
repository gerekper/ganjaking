<?php

declare(strict_types=1);

namespace ACP\Storage\Decoder;

use AC\ListScreen;
use AC\ListScreenFactory;
use AC\Plugin\Version;
use AC\Type\ListScreenId;
use ACP\Exception\NonDecodableDataException;
use ACP\ListScreenPreferences;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentCollection;
use ACP\Search\Type\SegmentKey;
use DateTime;

final class Version630 extends BaseDecoder implements SegmentsDecoder, ListScreenDecoder
{

    private $list_screen_factory;

    public function __construct(array $encoded_data, ListScreenFactory $list_screen_factory)
    {
        parent::__construct($encoded_data);

        $this->list_screen_factory = $list_screen_factory;
    }

    public function get_version(): Version
    {
        return new Version('6.3');
    }

    public function has_segments(): bool
    {
        $segments = $this->encoded_data['segments'] ?? null;

        return $segments && is_array($segments);
    }

    public function get_segments(): SegmentCollection
    {
        if ( ! $this->has_required_version() || ! $this->has_segments()) {
            throw new NonDecodableDataException($this->encoded_data);
        }

        $segments = [];

        foreach ($this->encoded_data['segments'] as $encoded_segment) {
            // Backwards compatibility for segments that have not stored their creation date
            $date_created = isset($encoded_segment['date_created'])
                ? DateTime::createFromFormat('U', (string)$encoded_segment['date_created'])
                : new DateTime();

            $segments[] = new Segment(
                new SegmentKey($encoded_segment['key']),
                $encoded_segment['name'],
                $encoded_segment['url_parameters'],
                new ListScreenId($encoded_segment['list_screen_id']),
                null,
                $date_created
            );
        }

        return new SegmentCollection($segments);
    }

    public function has_list_screen(): bool
    {
        $type = $this->encoded_data['list_screen']['type'] ?? null;

        if ( ! $type) {
            return false;
        }

        if ( ! $this->list_screen_factory->can_create((string)$type)) {
            return false;
        }

        return true;
    }

    public function get_list_screen(): ListScreen
    {
        if ( ! $this->has_required_version() || ! $this->has_list_screen()) {
            throw new NonDecodableDataException($this->encoded_data);
        }

        $preferences = $this->encoded_data['list_screen']['settings'] ?? [];

        if ($this->has_segments()) {
            $preferences[ListScreenPreferences::SHARED_SEGMENTS] = $this->get_segments();
        }

        return $this->list_screen_factory->create(
            $this->encoded_data['list_screen']['type'],
            [
                'list_id' => $this->encoded_data['list_screen']['id'],
                'columns' => $this->encoded_data['list_screen']['columns'] ?? [],
                'preferences' => $preferences,
                'title' => $this->encoded_data['list_screen']['title'] ?? '',
                'date' => DateTime::createFromFormat('U', (string)$this->encoded_data['list_screen']['updated']),
            ]
        );
    }

}