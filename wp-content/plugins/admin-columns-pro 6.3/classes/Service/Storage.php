<?php

declare(strict_types=1);

namespace ACP\Service;

use AC;
use AC\ListScreenRepository\Storage\ListScreenRepository;
use AC\Registerable;
use ACP\ListScreenRepository\Callback;
use ACP\ListScreenRepository\FileFactory;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Directory;

final class Storage implements Registerable
{

    private $storage;

    private $decoder_factory;

    private $file_factory;

    public function __construct(
        AC\ListScreenRepository\Storage $storage,
        AbstractDecoderFactory $decoder_factory,
        FileFactory $file_factory
    ) {
        $this->storage = $storage;
        $this->decoder_factory = $decoder_factory;
        $this->file_factory = $file_factory;
    }

    public function register(): void
    {
        add_action('acp/ready', [$this, 'configure'], 20);
    }

    public function configure(): void
    {
        $repositories = $this->storage->get_repositories();

        $this->configure_file_storage($repositories);

        $repositories = apply_filters(
            'acp/storage/repositories',
            $repositories,
            $this->file_factory
        );

        $callbacks = apply_filters('acp/storage/repositories/callback', []);
        
        foreach ($callbacks as $key => $callback) {
            $repositories['acp-callback-' . $key] = new ListScreenRepository(
                new Callback($this->decoder_factory, $callback),
                false
            );
        }

        $this->storage->set_repositories($repositories);
    }

    private function configure_file_storage(array &$repositories): void
    {
        if (apply_filters('acp/storage/file/enable_for_multisite', false) && is_multisite()) {
            return;
        }

        $path = apply_filters('acp/storage/file/directory', null);

        if ( ! is_string($path) || $path === '') {
            return;
        }

        $directory = new Directory($path);

        if ( ! $directory->exists() && $directory->has_path(WP_CONTENT_DIR)) {
            $directory->create();
        }

        $file = $this->file_factory->create(
            $path,
            (bool)apply_filters('acp/storage/file/directory/writable', true),
            null,
            (string)apply_filters('acp/storage/file/directory/i18n_text_domain', null)
        );

        $repositories['acp-file'] = $file;

        if ( ! $file->is_writable() || ! $this->storage->has_repository('acp-database')) {
            return;
        }

        $database = $this->storage->get_repository('acp-database');

        if (apply_filters('acp/storage/file/directory/migrate', false)) {
            $this->run_migration($database, $file);
        }

        if (apply_filters('acp/storage/file/directory/copy', false)) {
            $this->run_copy($database, $file);
        }

        $repositories['acp-database'] = $database->with_writable(false);
    }

    private function run_migration(ListScreenRepository $from, ListScreenRepository $to): void
    {
        foreach ($from->with_writable(true)->find_all() as $list_screen) {
            $to->save($list_screen);
            $from->delete($list_screen);
        }
    }

    private function run_copy(ListScreenRepository $from, ListScreenRepository $to): void
    {
        foreach ($from->find_all() as $list_screen) {
            $to->save($list_screen);
        }
    }

}