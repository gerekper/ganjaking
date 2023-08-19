<?php

namespace ACP\Updates;

use AC\Entity\Plugin;
use AC\Registerable;
use ACP\API\Request;
use ACP\ApiFactory;
use WP_Error;

/**
 * Show changelog when "click view details".
 */
class ViewPluginDetails implements Registerable
{

    private $plugin;

    private $api_factory;

    public function __construct(Plugin $plugin, ApiFactory $api_factory)
    {
        $this->plugin = $plugin;
        $this->api_factory = $api_factory;
    }

    public function register(): void
    {
        add_filter('plugins_api', [$this, 'get_plugin_information'], 10, 3);
    }

    /**
     * @param mixed  $result
     * @param string $action
     * @param object $args
     *
     * @return object|WP_Error
     */
    public function get_plugin_information($result, $action, $args)
    {
        if ('plugin_information' !== $action) {
            return $result;
        }

        $slug = $this->plugin->get_dirname();

        if ($slug !== $args->slug) {
            return $result;
        }

        $response = $this->api_factory->create()->dispatch(
            new Request\ProductInformation($slug)
        );

        if ($response->has_error()) {
            return $response->get_error();
        }

        return $response->get_body();
    }

}