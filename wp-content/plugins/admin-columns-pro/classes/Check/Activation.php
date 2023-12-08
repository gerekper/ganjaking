<?php

namespace ACP\Check;

use AC\Admin\Page\Addons;
use AC\Admin\Page\Columns;
use AC\Admin\Page\Settings;
use AC\Ajax;
use AC\Capabilities;
use AC\Entity\Plugin;
use AC\Message;
use AC\Registerable;
use AC\Screen;
use AC\Storage;
use AC\Type\Uri;
use AC\Type\Url;
use ACP\Access\ActivationStorage;
use ACP\Access\Permissions;
use ACP\Access\PermissionsStorage;
use ACP\ActivationTokenFactory;
use ACP\Admin\Page\License;
use ACP\Admin\Page\Tools;

class Activation
    implements Registerable
{

    private $plugin;

    private $activation_token_factory;

    private $activation_storage;

    private $permission_storage;

    public function __construct(
        Plugin $plugin,
        ActivationTokenFactory $activation_token_factory,
        ActivationStorage $activation_storage,
        PermissionsStorage $permission_storage
    ) {
        $this->plugin = $plugin;
        $this->activation_token_factory = $activation_token_factory;
        $this->activation_storage = $activation_storage;
        $this->permission_storage = $permission_storage;
    }

    public function register(): void
    {
        add_action('ac/screen', [$this, 'register_notice']);

        $this->get_ajax_handler()->register();
    }

    private function get_ajax_handler(): Ajax\Handler
    {
        $handler = new Ajax\Handler();
        $handler
            ->set_action('ac_notice_dismiss_activation')
            ->set_callback([$this, 'ajax_dismiss_notice']);

        return $handler;
    }

    public function register_notice(Screen $screen): void
    {
        if ( ! $screen->has_screen() || ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        switch (true) {
            case $screen->is_plugin_screen() && $this->show_message() :
                $notice = new Message\Plugin(
                    $this->get_message(),
                    $this->plugin->get_basename(),
                    Message::INFO
                );
                $notice->register();
                break;
            case (
                     $screen->is_admin_screen(Settings::NAME) ||
                     $screen->is_admin_screen(Columns::NAME) ||
                     $screen->is_admin_screen(Tools::NAME) ||
                     $screen->is_admin_screen(Addons::NAME) ||
                     $screen->is_admin_screen(License::NAME)) && $this->show_message() :
                $notice = new Message\Notice($this->get_message());
                $notice
                    ->set_type(Message::INFO)
                    ->register();
                break;
            case $screen->is_list_screen() && $this->get_dismiss_option()->is_expired() && $this->show_message() :

                // Dismissible message on list table
                $notice = new Message\Notice\Dismissible($this->get_message(), $this->get_ajax_handler());
                $notice
                    ->set_type(Message::INFO)
                    ->register();
                break;
        }
    }

    private function show_message(): bool
    {
        // We send a different (locked) message when a use has no usage permissions
        $has_usage = $this->permission_storage->retrieve()->has_permission(Permissions::USAGE);

        if ( ! $has_usage) {
            return false;
        }

        $token = $this->activation_token_factory->create();
        $activation = $token ? $this->activation_storage->find($token) : null;

        if ( ! $activation) {
            return true;
        }

        // An expired license has its own message
        if ($activation->is_expired()) {
            return false;
        }

        return ! $activation->is_active();
    }

    private function get_license_page_url(): Uri
    {
        return $this->plugin->is_network_active()
            ? new Url\EditorNetwork('license')
            : new Url\Editor('license');
    }

    private function get_account_url(): Url\UtmTags
    {
        return new Url\UtmTags(new Url\Site(Url\Site::PAGE_ACCOUNT_SUBSCRIPTIONS), 'license-activation');
    }

    private function get_message(): string
    {
        return sprintf(
            '%s %s',
            sprintf(
                __(
                    "To enable automatic updates for %s, <a href='%s'>enter your license key</a>.",
                    'codepress_admin_columns'
                ),
                'Admin Columns Pro',
                esc_url($this->get_license_page_url()->get_url())
            ),
            sprintf(
                __('You can find your license key on your %s.', 'codepress-admin-columns'),
                sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url($this->get_account_url()->get_url()),
                    __('account page', 'codepress-admin-columns')
                )
            )
        );
    }

    private function get_dismiss_option(): Storage\Timestamp
    {
        return new Storage\Timestamp(
            new Storage\UserMeta('ac_notice_dismiss_activation')
        );
    }

    public function ajax_dismiss_notice(): void
    {
        $this->get_ajax_handler()->verify_request();
        $this->get_dismiss_option()->save(time() + (MONTH_IN_SECONDS * 2));

        exit;
    }

}