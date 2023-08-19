<?php

namespace ACP\Check;

use AC;
use AC\Capabilities;
use AC\Entity\Plugin;
use AC\Message;
use AC\Message\Notice;
use AC\Registerable;
use AC\Screen;
use AC\Type\Url;
use ACP\Access\PermissionsStorage;
use ACP\Admin\Page;

class LockedSettings implements Registerable
{

    private $plugin;

    private $permission_storage;

    public function __construct(Plugin $plugin, PermissionsStorage $permission_storage)
    {
        $this->plugin = $plugin;
        $this->permission_storage = $permission_storage;
    }

    public function register(): void
    {
        add_action('ac/screen', [$this, 'register_notice']);
    }

    private function get_license_page_url(): Url
    {
        return $this->plugin->is_network_active()
            ? new Url\EditorNetwork('license')
            : new Url\Editor('license');
    }

    private function get_message(): string
    {
        return sprintf(
            '%s %s',
            sprintf('%s is not yet activated.', 'Admin Columns Pro'),
            sprintf(
                __("Go to the %s and activate Admin Columns Pro to start using the plugin.", 'codepress_admin_columns'),
                sprintf(
                    '<a href="%s">%s</a>',
                    esc_url($this->get_license_page_url()->get_url()),
                    __('license page', 'codepress_admin_columns')
                )
            )
        );
    }

    private function get_inline_plugin_message(): string
    {
        return sprintf(
            '%s %s',
            sprintf(
                __('%s is not yet activated, please %s.', 'codepress_admin_columns'),
                'Admin Columns Pro',
                sprintf(
                    '<a href="%s">%s</a>',
                    esc_url($this->get_license_page_url()->get_url()),
                    __('enter your license key', 'codepress_admin_columns')
                )
            ),
            $this->get_message_account_page()
        );
    }

    private function missing_usage_permission(): bool
    {
        return ! $this->permission_storage->retrieve()->has_usage_permission();
    }

    private function get_account_url(): Url\UtmTags
    {
        return new Url\UtmTags(new Url\Site(Url\Site::PAGE_ACCOUNT_SUBSCRIPTIONS), 'license-activation');
    }

    private function get_message_account_page(): string
    {
        return sprintf(
            __('You can find your license key on your %s.', 'codepress-admin-columns'),
            sprintf(
                '<a href="%s" target="_blank">%s</a>',
                esc_url($this->get_account_url()->get_url()),
                __('account page', 'codepress-admin-columns')
            )
        );
    }

    private function get_license_page_message(): string
    {
        $documentation = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            (new Url\Documentation(Url\Documentation::ARTICLE_SUBSCRIPTION_QUESTIONS))->get_url(),
            sprintf(__('activating %s', 'codepress-admin-columns'), 'Admin Columns Pro')
        );

        $parts = [
            __('To start using Admin Columns Pro, fill in your license key below.', 'codepress-admin-columns'),
            sprintf(__('Read more about %s.'), $documentation),
        ];

        return implode(' ', $parts);
    }

    public function register_notice(Screen $screen)
    {
        if ( ! current_user_can(Capabilities::MANAGE) || ! $screen->has_screen()) {
            return;
        }

        switch (true) {
            case $screen->is_plugin_screen() && $this->missing_usage_permission() :
                $notice = new Message\Plugin(
                    $this->get_inline_plugin_message(),
                    $this->plugin->get_basename(),
                    Message::WARNING
                );

                $notice->register();
                break;
            case $screen->is_admin_screen(Page\License::NAME) && $this->missing_usage_permission() :
                $notice = new Notice(
                    $this->get_license_page_message(),
                    Message::ERROR
                );

                $notice->register();
                break;
            case ($screen->is_admin_screen(AC\Admin\Page\Columns::NAME) || $screen->is_admin_screen(
                        Page\Tools::NAME
                    ) || $screen->is_admin_screen(AC\Admin\Page\Settings::NAME)) && $this->missing_usage_permission() :
                $notice = new Notice(
                    $this->get_message(),
                    Message::ERROR
                );

                $notice->register();
                break;
        }
    }

}