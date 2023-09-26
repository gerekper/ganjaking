<?php

namespace ACP\Admin;

use AC\Asset;
use AC\Asset\Enqueueable;
use AC\Entity\Plugin;
use AC\Registerable;
use ACP\Access\PermissionsStorage;
use ACP\Asset\Script;
use ACP\Transient\LicenseCheckTransient;

class Scripts implements Registerable
{

    private $location;

    private $permission_storage;

    private $plugin;

    public function __construct(
        Asset\Location\Absolute $location,
        PermissionsStorage $permission_storage,
        Plugin $plugin
    ) {
        $this->location = $location;
        $this->permission_storage = $permission_storage;
        $this->plugin = $plugin;
    }

    public function register(): void
    {
        add_action('ac/admin_scripts', [$this, 'register_usage_limiter']);
        add_action('admin_enqueue_scripts', [$this, 'register_daily_license_check']);
    }

    public function register_usage_limiter(): void
    {
        if ($this->permission_storage->retrieve()->has_usage_permission()) {
            return;
        }

        $assets = [
            new Asset\Style('acp-usage-limiter', $this->location->with_suffix('assets/core/css/usage-limiter.css')),
            new Asset\Script('acp-usage-limiter', $this->location->with_suffix('assets/core/js/usage-limiter.js')),
        ];

        array_map([$this, 'enqueue'], $assets);
    }

    public function register_daily_license_check(): void
    {
        $transient = new LicenseCheckTransient($this->plugin->is_network_active());

        if ($transient->is_expired()) {
            $script = new Script\LicenseCheck($this->location->with_suffix('assets/core/js/license-check.js'));
            $script->enqueue();

            $transient->save(DAY_IN_SECONDS);
        }
    }

    private function enqueue(Enqueueable $assets)
    {
        $assets->enqueue();
    }

}