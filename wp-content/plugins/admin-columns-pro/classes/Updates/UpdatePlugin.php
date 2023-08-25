<?php

namespace ACP\Updates;

use AC\Entity\Plugin;
use AC\Plugin\Version;
use AC\Registerable;
use ACP\Storage\PluginsDataFactory;
use stdClass;

/**
 * Hooks into the WordPress update process for plugins
 */
class UpdatePlugin implements Registerable
{

    private $plugin;

    private $storage_factory;

    public function __construct(Plugin $plugin, PluginsDataFactory $storage_factory)
    {
        $this->plugin = $plugin;
        $this->storage_factory = $storage_factory;
    }

    public function register(): void
    {
        /**
         * For testing purpose use `wp_clean_update_cache()`
         */
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
    }

    public function check_update($transient)
    {
        $data = $this->storage_factory->create()->get();

        if (empty($data) || ! is_array($data)) {
            return $transient;
        }

        $dir_name = $this->plugin->get_dirname();

        if ( ! isset($data[$dir_name])) {
            return $transient;
        }

        $plugin_data = (object)$data[$dir_name];

        if (null === $transient) {
            $transient = new stdClass();
        }

        if ($this->plugin->get_version()->is_lt(new Version($plugin_data->new_version))) {
            $transient->response[$this->plugin->get_basename()] = $plugin_data;
        }

        return $transient;
    }

}