<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC;
use AC\ListScreen;
use AC\ListScreenFactory;
use ACP\Exception\FailedToSaveSegmentException;
use ACP\ListScreenPreferences;
use ACP\Search\SegmentRepository;

final class Database extends AC\ListScreenRepository\Database
{

    use SegmentTrait;

    public function __construct(ListScreenFactory $list_screen_factory, SegmentRepository\Database $segment_repository)
    {
        $this->segment_repository = $segment_repository;

        parent::__construct($list_screen_factory);
    }

    protected function save_preferences(ListScreen $list_screen): array
    {
        $preferences = parent::save_preferences($list_screen);

        unset($preferences[ListScreenPreferences::SHARED_SEGMENTS]);

        return $preferences;
    }

    protected function get_preferences(ListScreen $list_screen): array
    {
        $preferences = parent::get_preferences($list_screen);
        $preferences[ListScreenPreferences::SHARED_SEGMENTS] = $this->segment_repository->find_all_shared(
            $list_screen->get_id()
        );

        return $preferences;
    }

    /**
     * @throws FailedToSaveSegmentException
     */
    public function save(ListScreen $list_screen): void
    {
        $segments = $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS);

        parent::save($list_screen);

        if ( ! $segments) {
            return;
        }

        $this->save_segments(
            $segments,
            $list_screen->get_id()
        );
    }

    public function delete(ListScreen $list_screen): void
    {
        $this->segment_repository->delete_all_shared($list_screen->get_id());

        parent::delete($list_screen);
    }

}