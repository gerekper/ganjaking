<?php

declare(strict_types=1);

namespace ACP;

use AC\Entity\Plugin;
use AC\Type\Url\Site;

class ApiFactory
{

    private $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function create(): API
    {
        $api = new API();
        $api->set_url(Site::URL)
            ->set_proxy('https://api.admincolumns.com')
            ->set_request_meta($this->get_meta());

        do_action('acp/api', $api);

        return $api;
    }

    private function is_local(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        return in_array($ip, ['127.0.0.1', '::1'], true);
    }

    private function get_meta(): array
    {
        $meta = [
            'php_version' => PHP_VERSION,
            'acp_version' => (string)$this->plugin->get_version(),
            'is_network'  => $this->plugin->is_network_active(),
        ];

        if ($this->is_local()) {
            $meta['ip'] = '127.0.0.1';
        }

        return $meta;
    }

}