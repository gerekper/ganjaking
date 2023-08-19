<?php

namespace ACP\Service;

use AC\Capabilities;
use AC\Registerable;
use ACP\ActivationTokenFactory;
use ACP\Transient\UpdateCheckTransientHourly;
use ACP\Updates\PluginDataUpdater;

class ForcePluginUpdate implements Registerable
{

    private $activation_token_factory;

    private $updater;

    public function __construct(ActivationTokenFactory $activation_token_factory, PluginDataUpdater $updater)
    {
        $this->activation_token_factory = $activation_token_factory;
        $this->updater = $updater;
    }

    public function register(): void
    {
        add_action('admin_init', [$this, 'force_plugin_updates']);
        add_action('load-plugins.php', [$this, 'force_plugin_updates_cached'], 9);
        add_action('load-update-core.php', [$this, 'force_plugin_updates_cached'], 9);
        add_action('load-update.php', [$this, 'force_plugin_updates_cached'], 9);
    }

    private function is_force_check_request(): bool
    {
        global $pagenow;

        return '1' === filter_input(INPUT_GET, 'force-check') && $pagenow === 'update-core.php' && current_user_can(
                Capabilities::MANAGE
            );
    }

    /**
     * Forces to check for updates on a manual request
     */
    public function force_plugin_updates(): void
    {
        if ( ! $this->is_force_check_request()) {
            return;
        }

        $this->updater->update($this->activation_token_factory->create());
    }

    /**
     * Forces to check for updates on plugins page
     */
    public function force_plugin_updates_cached(): void
    {
        $transient = new UpdateCheckTransientHourly();

        if ($transient->is_expired()) {
            $this->updater->update($this->activation_token_factory->create());

            $transient->save();
        }
    }

}