<?php

declare(strict_types=1);

namespace ACP\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use AC\ListScreenFactory\Aggregate;
use AC\Type\ListScreenId;
use ACP\ListScreenPreferences;
use ACP\Search\Entity\Segment;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository\KeyGeneratorTrait;
use WP_Screen;

class PrototypeFactory implements ListScreenFactory
{

    use KeyGeneratorTrait;

    private $aggregate;

    public function __construct(Aggregate $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    public function can_create(string $key): bool
    {
        return $this->aggregate->can_create($key);
    }

    public function create(string $key, array $settings = []): ListScreen
    {
        return $this->aggregate->create($key, $settings);
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $this->aggregate->can_create_from_wp_screen($screen);
    }

    public function create_from_wp_screen(WP_Screen $screen, array $settings = []): ListScreen
    {
        return $this->aggregate->create_from_wp_screen($screen, $settings);
    }

    public function create_from_list_screen(ListScreen $list_screen, array $overwrites = []): ListScreen
    {
        $list_id = ListScreenId::generate();

        $settings = [
            'list_id'     => (string)$list_id,
            'columns'     => $list_screen->get_settings(),
            'preferences' => $list_screen->get_preferences(),
            'title'       => $list_screen->get_title(),
        ];

        $settings = array_merge($settings, $overwrites);

        $segments = $settings['preferences'][ListScreenPreferences::SHARED_SEGMENTS] ?? null;

        if ($segments) {
            $settings['preferences'] = $this->apply_segments_to_preferences(
                $list_id,
                $settings['preferences'],
                $segments
            );
        }

        return $this->create(
            $list_screen->get_key(),
            $settings
        );
    }

    private function apply_segments_to_preferences(
        ListScreenId $list_id,
        array $preferences,
        SegmentCollection $source_segments
    ): array {
        $segments = new SegmentCollection();

        foreach ($source_segments as $segment) {
            $segment_key = $this->generate_key();

            $segments->add(
                new Segment(
                    $segment_key,
                    $segment->get_name(),
                    $segment->get_url_parameters(),
                    $list_id
                )
            );

            // update pre-applied filter
            if ((string)$segment->get_key() === ($preferences[ListScreenPreferences::FILTER_SEGMENT] ?? null)) {
                $preferences[ListScreenPreferences::FILTER_SEGMENT] = (string)$segment_key;
            }
        }

        $preferences[ListScreenPreferences::SHARED_SEGMENTS] = $segments;

        return $preferences;
    }

}