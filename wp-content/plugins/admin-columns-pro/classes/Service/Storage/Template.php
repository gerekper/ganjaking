<?php

declare(strict_types=1);

namespace ACP\Service\Storage;

use AC\ListScreen;
use AC\Message;
use AC\Message\Notice;
use AC\Registerable;
use AC\Screen;
use ACP\ListScreenRepository;
use ACP\Migrate;
use ACP\Nonce\PreviewNonce;

final class Template implements Registerable
{

    private $repository;

    private $preview_mode;

    public function __construct(
        ListScreenRepository\Template $repository,
        Migrate\Preference\PreviewMode $preview_mode
    ) {
        $this->repository = $repository;
        $this->preview_mode = $preview_mode;
    }

    public function register(): void
    {
        add_action('acp/ready', [$this, 'add_templates']);
        add_action('ac/table/list_screen', [$this, 'register_preview_notice']);
        add_filter('acp/table/views/active', [$this, 'disable_table_views']);
        add_filter('ac/screen', [$this, 'disable_preview_mode']);
    }

    public function add_templates(): void
    {
        $files = (array)apply_filters('acp/storage/template/files', []);

        foreach ($files as $file) {
            $this->repository->add_file($file);
        }
    }

    public function disable_table_views(bool $active): bool
    {
        if ($this->preview_mode->is_active()) {
            $active = false;
        }

        return $active;
    }

    public function disable_preview_mode(Screen $screen): void
    {
        // Disable preview mode outside the list table
        if ($screen->is_admin_screen()) {
            $this->preview_mode->set_inactive();
        }
    }

    public function register_preview_notice(ListScreen $list_screen): void
    {
        // disable preview mode when visiting other list tables
        if ( ! $list_screen->has_id()) {
            $this->preview_mode->set_inactive();

            return;
        }

        if ( ! $this->preview_mode->is_active($list_screen->get_id())) {
            return;
        }

        $nonce_preview = new PreviewNonce();

        $url = $list_screen->get_editor_url()
                           ->with_arg('tab', 'import-export')
                           ->with_arg($nonce_preview->get_name(), $nonce_preview->create())
                           ->with_arg('ac_action', 'acp-preview-mode')
                           ->with_arg('preview_method', 'deactivate');

        $deactivate_url = $list_screen->get_table_url()
                                      ->with_arg($nonce_preview->get_name(), $nonce_preview->create())
                                      ->with_arg('ac_action', 'acp-preview-mode')
                                      ->with_arg('preview_method', 'deactivate');

        $message = sprintf(
            '%s %s %s',
            sprintf(
                __('This is a preview of %s.', 'codepress-admin-columns'),
                "<strong>" . esc_html($list_screen->get_title()) . "</strong>"
            ),
            sprintf(
                __('Return to the %s page to import this table view.', 'codepress-admin-columns'),
                sprintf("<a href='%s'>%s</a>", esc_url($url->get_url()), __('Tools', 'codepress-admin-columns'))
            ),
            sprintf(
                '<a href="%s">%s</a>',
                esc_url($deactivate_url->get_url()),
                __('Leave preview mode', 'codepress-admin-columns')
            )
        );

        $notice = new Notice(
            $message,
            Message::INFO
        );
        $notice->register();
    }

}