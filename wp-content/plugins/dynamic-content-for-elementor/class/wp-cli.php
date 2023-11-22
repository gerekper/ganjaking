<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class WpCli extends \WP_CLI_Command
{
    public function version()
    {
        \WP_CLI::line('Version ' . DCE_VERSION);
    }
    public function license($args)
    {
        switch ($args[0]) {
            case 'activate':
                if (empty($args[1])) {
                    \WP_CLI::line('Missing license key');
                    return;
                }
                $response = Plugin::instance()->license_system->activate_new_license_key($args[1]);
                \WP_CLI::line($response[1]);
                return;
            case 'deactivate':
                $response = Plugin::instance()->license_system->deactivate_license();
                \WP_CLI::line($response[1]);
                return;
            case 'check':
                if (Plugin::instance()->license_system->is_license_active()) {
                    \WP_CLI::line('The license is active');
                } else {
                    \WP_CLI::line(Plugin::instance()->license_system->get_license_error());
                }
        }
    }
}
