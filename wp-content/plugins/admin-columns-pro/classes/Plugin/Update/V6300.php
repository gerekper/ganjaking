<?php

declare(strict_types=1);

namespace ACP\Plugin\Update;

use AC\ListScreenRepository\Storage;
use AC\ListScreenRepositoryWritable;
use AC\Plugin\Update;
use AC\Plugin\Version;

final class V6300 extends Update
{

    private $storage;

    public function __construct(Storage $storage)
    {
        parent::__construct(new Version('6.3'));

        $this->storage = $storage;
    }

    public function apply_update(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'ac_segments';

        $queries[] = "
            UPDATE $table
            SET `user_id` = 0
            WHERE `global` = 1
        ";

        $queries[] = "
            ALTER TABLE $table
            ADD `key` char(13)
        ";

        foreach ($queries as $query) {
            $wpdb->query($query);
        }

        $sql = "
            SELECT *
            FROM $table
            WHERE `key` IS NULL
        ";

        $mapping = [];

        foreach ($wpdb->get_results($sql) as $row) {
            $key = uniqid();

            $wpdb->update(
                $table,
                [
                    'key' => $key,
                ],
                [
                    'id' => $row->id,
                ]
            );

            $mapping[$row->id] = $key;

            usleep(1);
        }

        // When all keys are filled, make unique
        $sql = "
            ALTER TABLE $table
            ADD UNIQUE (`key`)
        ";

        $wpdb->query($sql);

        // Update all list screens to reflect keys now instead of a local primary key
        foreach ($this->storage->get_repositories() as $repository) {
            $list_screen_repository = $repository->get_list_screen_repository();

            if ( ! $list_screen_repository instanceof ListScreenRepositoryWritable || ! $repository->is_writable()) {
                continue;
            }

            foreach ($list_screen_repository->find_all() as $list_screen) {
                $preferences = $list_screen->get_preferences();

                if ( ! isset($preferences['filter_segment'], $mapping[$preferences['filter_segment']])) {
                    continue;
                }

                $preferences['filter_segment'] = $mapping[$preferences['filter_segment']];

                $list_screen->set_preferences($preferences);

                $list_screen_repository->save($list_screen);
            }
        }
    }

}