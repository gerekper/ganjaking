<?php

namespace ACP;

use AC\Entity\Plugin;
use AC\Registerable;
use AC\Type\Url;
use ACP\Access\PermissionsStorage;

class PluginActionLinks implements Registerable
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
        add_filter('plugin_action_links', [$this, 'add_settings_link'], 1, 2);
        add_filter('network_admin_plugin_action_links', [$this, 'add_network_settings_link'], 1, 2);
    }

    private function has_usage_permission(): bool
    {
        return $this->permission_storage->retrieve()->has_usage_permission();
    }

    public function add_settings_link($links, $file)
    {
        if ($file === $this->plugin->get_basename()) {
            array_unshift(
                $links,
                $this->create_link_element(new Url\Editor($this->has_usage_permission() ? 'settings' : 'license'))
            );
        }

        return $links;
    }

    public function add_network_settings_link($links, $file)
    {
        if ($file === $this->plugin->get_basename()) {
            array_unshift(
                $links,
                $this->create_link_element(new Url\EditorNetwork($this->has_usage_permission() ? 'columns' : 'license'))
            );
        }

        return $links;
    }

    private function create_link_element(Url $url): string
    {
        return sprintf(
            '<a href="%s">%s</a>',
            esc_url($url->get_url()),
            __('Settings', 'codepress-admin-columns')
        );
    }

}