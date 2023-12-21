<?php

declare(strict_types=1);

namespace ACP\Service;

use AC;
use AC\ListScreenRepository\Storage\ListScreenRepository;
use AC\ListScreenRepositoryWritable;
use AC\Registerable;
use ACP\ListScreenRepository\Callback;
use ACP\ListScreenRepository\Database;
use ACP\ListScreenRepository\FileFactory;
use ACP\ListScreenRepository\Template;
use ACP\ListScreenRepository\Types;
use ACP\Migrate\Preference\PreviewMode;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Directory;

final class Storage implements Registerable
{

    private $storage;

    private $database_list_screen_repository;

    private $decoder_factory;

    private $file_factory;

    private $template_repository;

    private $preview_mode;

    public function __construct(
        AC\ListScreenRepository\Storage $storage,
        Database $database_list_screen_repository,
        AbstractDecoderFactory $decoder_factory,
        FileFactory $file_factory,
        Template $template_repository,
        PreviewMode $preview_mode
    ) {
        $this->storage = $storage;
        $this->database_list_screen_repository = $database_list_screen_repository;
        $this->decoder_factory = $decoder_factory;
        $this->file_factory = $file_factory;
        $this->template_repository = $template_repository;
        $this->preview_mode = $preview_mode;
    }

    public function register(): void
    {
        add_action('acp/ready', [$this, 'configure'], 20);

        // Migrate can only run after post types have been initialised
        add_action('init', [$this, 'migrate'], 300);
    }

    public function configure(): void
    {
        $repositories = $this->storage->get_repositories();

        // Use the ACP version instead of the AC version
        $repositories[AC\ListScreenRepository\Types::DATABASE] = new ListScreenRepository(
            $this->database_list_screen_repository, true
        );

        $this->configure_file_storage($repositories);

        $repositories = apply_filters(
            'acp/storage/repositories',
            $repositories,
            $this->file_factory
        );

        $callbacks = apply_filters('acp/storage/repositories/callback', []);

        foreach ($callbacks as $key => $callback) {
            $repositories['ac-callback-' . $key] = new ListScreenRepository(
                new Callback($this->decoder_factory, $callback),
                false
            );
        }

        if ($this->preview_mode->is_active()) {
            $repositories[Types::TEMPLATE] = new ListScreenRepository($this->template_repository);
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

        if ( ! $directory->exists() || str_contains($path, WP_CONTENT_DIR)) {
            $directory->create();
        }

        $file = $this->file_factory->create(
            $path,
            (bool)apply_filters('acp/storage/file/directory/writable', true),
            null,
            (string)apply_filters('acp/storage/file/directory/i18n_text_domain', null)
        );

        $repositories[Types::FILE] = $file;

        if ( ! $file->is_writable() || ! $this->storage->has_repository(Types::DATABASE)) {
            return;
        }

        $repositories[Types::DATABASE] = $repositories[Types::DATABASE]->with_writable(false);
    }

    public function migrate(): void
    {
        $do_migrate = apply_filters('acp/storage/file/directory/migrate', false);

        if ( ! $do_migrate) {
            return;
        }

        if ( ! $this->storage->has_repository(Types::FILE) || ! $this->storage->has_repository(Types::DATABASE)) {
            return;
        }

        $file = $this->storage
            ->get_repository(Types::FILE)
            ->get_list_screen_repository();

        $database = $this->storage
            ->get_repository(Types::DATABASE)
            ->with_writable(true)
            ->get_list_screen_repository();

        if ( ! $database instanceof ListScreenRepositoryWritable || ! $file instanceof ListScreenRepositoryWritable) {
            return;
        }

        foreach ($database->find_all() as $list_screen) {
            $file->save($list_screen);
            $database->delete($list_screen);
        }
    }

}