<?php

declare(strict_types=1);

namespace ACP\Check;

use AC\Ajax;
use AC\Capabilities;
use AC\Entity\Plugin;
use AC\Message;
use AC\Registerable;
use AC\Screen;
use AC\Storage;
use AC\Type\Url\Site;
use AC\Type\Url\UtmTags;
use ACP\Access\ActivationStorage;
use ACP\ActivationTokenFactory;
use ACP\Entity;
use ACP\Type\SiteUrl;
use DateTime;

class Expired implements Registerable
{

    private $plugin;

    private $activation_token_factory;

    private $activation_storage;

    private $site_url;

    public function __construct(
        Plugin $plugin,
        ActivationTokenFactory $activation_token_factory,
        ActivationStorage $activation_storage,
        SiteUrl $site_url
    ) {
        $this->plugin = $plugin;
        $this->activation_token_factory = $activation_token_factory;
        $this->activation_storage = $activation_storage;
        $this->site_url = $site_url;
    }

    public function register(): void
    {
        add_action('ac/screen', [$this, 'display']);

        $this->get_ajax_handler()->register();
    }

    private function is_activation_expired(Entity\Activation $activation): bool
    {
        if ( ! $activation->is_expired() ||
             ! $activation->get_expiry_date()->exists()) {
            return false;
        }

        // Prevent overlap with auto renewal payments and message
        if ($activation->is_auto_renewal() &&
            $activation->is_expired() &&
            $activation->get_expiry_date()->get_expired_seconds() < (7 * DAY_IN_SECONDS)) {
            return false;
        }

        return true;
    }

    private function get_activation(): ?Entity\Activation
    {
        $token = $this->activation_token_factory->create();

        return $token
            ? $this->activation_storage->find($token)
            : null;
    }

    public function display(Screen $screen): void
    {
        if ( ! $screen->has_screen() || ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        switch (true) {
            // Inline message on plugin page
            case $screen->is_plugin_screen() :
                $activation = $this->get_activation();

                if ($activation && $this->is_activation_expired($activation)) {
                    $notice = new Message\Plugin(
                        $this->get_message($activation->get_expiry_date()->get_value()),
                        $this->plugin->get_basename(),
                        Message::WARNING
                    );
                    $notice->register();
                }

                return;

            // Permanent displayed on settings page
            case $screen->is_admin_screen() :
                $activation = $this->get_activation();

                if ($activation && $this->is_activation_expired($activation)) {
                    $notice = new Message\Notice(
                        $this->get_message($activation->get_expiry_date()->get_value()),
                        Message::WARNING
                    );
                    $notice->register();
                }

                return;

            // Dismissible on list table
            case $screen->is_list_screen() && $this->get_dismiss_option()->is_expired() :
                $activation = $this->get_activation();

                if ($activation && $this->is_activation_expired($activation)) {
                    $notice = new Message\Notice\Dismissible(
                        $this->get_message($activation->get_expiry_date()->get_value()),
                        $this->get_ajax_handler(),
                        Message::WARNING
                    );
                    $notice->register();
                }

                return;
        }
    }

    private function get_message(DateTime $expiration_date): string
    {
        $expired_on = ac_format_date(get_option('date_format'), $expiration_date->getTimestamp());

        $activation_token = $this->activation_token_factory->create();
        $url = new UtmTags(new Site(Site::PAGE_ACCOUNT_SUBSCRIPTIONS), 'expired');

        if ($activation_token) {
            $url = $url->with_arg($activation_token->get_type(), $activation_token->get_token())
                       ->with_arg('site_url', $this->site_url->get_url());
        }

        return sprintf(
            __(
                'Your Admin Columns Pro license has expired on %s. To receive updates, renew your license on the %s.',
                'codepress-admin-columns'
            ),
            '<strong>' . $expired_on . '</strong>',
            sprintf('<a href="%s">%s</a>', esc_url($url->get_url()), __('My Account Page', 'codepress-admin-columns'))
        );
    }

    protected function get_ajax_handler(): Ajax\Handler
    {
        $handler = new Ajax\Handler();
        $handler
            ->set_action('ac_notice_dismiss_expired')
            ->set_callback([$this, 'ajax_dismiss_notice']);

        return $handler;
    }

    protected function get_dismiss_option(): Storage\Timestamp
    {
        return new Storage\Timestamp(
            new Storage\UserMeta('ac_notice_dismiss_expired')
        );
    }

    public function ajax_dismiss_notice(): void
    {
        $this->get_ajax_handler()->verify_request();
        $this->get_dismiss_option()->save(time() + MONTH_IN_SECONDS);
    }

}