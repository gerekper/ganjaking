<?php

declare(strict_types=1);

namespace ACP\Plugin\Update;

use AC\ListScreenRepository\Storage;
use AC\ListScreenRepositoryWritable;
use AC\Plugin\Update;
use AC\Plugin\Version;

class V6000 extends Update
{

    private $storage;

    public function __construct(Storage $storage)
    {
        parent::__construct(new Version('6.0'));

        $this->storage = $storage;
    }

    public function apply_update(): void
    {
        $this->apply_acf_update();
    }

    private function apply_acf_update(): void
    {
        foreach ($this->storage->get_repositories() as $repository) {
            if ( ! $repository->is_writable()) {
                continue;
            }

            $_repository = $repository->get_list_screen_repository();

            foreach ($_repository->find_all() as $list_screen) {
                $settings = $list_screen->get_settings();
                $updated = false;

                foreach ($settings as $column_name => $setting) {
                    if ('column-acf_field' === $setting['type'] && function_exists('acf_get_field')) {
                        $field = $setting['field'];
                        $acf_field = acf_get_field($setting['field']);

                        if ($acf_field && $acf_field['type'] === 'group' && isset($setting['sub_field'])) {
                            $field = 'acfgroup__' . $field . '-' . $setting['sub_field'];
                        }

                        $setting['type'] = $field;

                        $settings[$column_name] = $setting;
                        $updated = true;
                    }
                }

                if ($updated && $_repository instanceof ListScreenRepositoryWritable) {
                    $list_screen->set_settings($settings);

                    $_repository->save($list_screen);
                }
            }
        }
    }

}