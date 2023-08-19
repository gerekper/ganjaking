<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Plugin\Version;
use AC\Storage;
use DirectoryIterator;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class V4301 extends Update
{

    private $plugin_dir;

    public function __construct(string $plugin_dir)
    {
        parent::__construct(new Version('4.3.1'));

        $this->plugin_dir = $plugin_dir;
    }

    /**
     * @throws Exception
     */
    public function apply_update(): void
    {
        $this->uppercase_class_files($this->plugin_dir . 'classes');
        $this->update_notice_preference_renewal();
    }

    /**
     * Set all files to the proper case
     */
    protected function uppercase_class_files(string $directory): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        /** @var DirectoryIterator $leaf */
        foreach ($iterator as $leaf) {
            $file = $leaf->getFilename();

            if ($leaf->isFile() && 'php' === $leaf->getExtension() && $file == strtolower($file)) {
                @rename($leaf->getPathname(), trailingslashit($leaf->getPath()) . ucfirst($file));
            }
        }
    }

    protected function get_users_by_meta_key(string $key): array
    {
        $user_ids = get_users([
            'fields'     => 'ids',
            'meta_query' => [
                [
                    'key'     => $key,
                    'compare' => 'EXISTS',
                ],
            ],
        ]);

        if ( ! $user_ids) {
            return [];
        }

        return $user_ids;
    }

    /**
     * @throws Exception
     */
    private function update_notice_preference_renewal(): void
    {
        $phase_key = 'cpac_hide_license_notice_phase';
        $timeout_key = 'cpac_hide_license_notice_timeout';

        foreach ($this->get_users_by_meta_key($phase_key) as $user_id) {
            $phase = get_user_meta($user_id, $phase_key, true);
            $timeout = get_user_meta($user_id, $timeout_key, true);

            if ( ! $timeout) {
                $timeout = time();
            }

            switch ($phase) {
                case '0':
                    $option = new Storage\Timestamp(
                        new Storage\UserMeta('ac_notice_dismiss_renewal_1', $user_id)
                    );
                    $option->save(time() + (MONTH_IN_SECONDS * 3));

                    break;
                case '1':
                    $option = new Storage\Timestamp(
                        new Storage\UserMeta('ac_notice_dismiss_renewal_2', $user_id)
                    );
                    $option->save(time() + (MONTH_IN_SECONDS * 3));

                    break;
                default: // completed or not set
                    $option = new Storage\Timestamp(
                        new Storage\UserMeta('ac_notice_dismiss_expired', $user_id)
                    );

                    $option->save($timeout + (MONTH_IN_SECONDS * 3));
            }

            delete_user_meta($user_id, $phase_key);
            delete_user_meta($user_id, $timeout_key);
        }
    }

}