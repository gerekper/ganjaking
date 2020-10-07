<?php

namespace MailOptin\WeMailConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage extends AbstractWeMailConnect
{
    public function __construct()
    {
        parent::__construct();

        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'), 10, 99);
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);
    }

    public function connection_settings($arg)
    {
        $connected = AbstractWeMailConnect::is_connected(true);
        if (true === $connected) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg = '';
            if (is_string($connected)) {
                $msg = esc_html(" &mdash; $connected");
            }
            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        //check if WeMail Class Exists
        if (class_exists('WeDevs\WeMail\WeMail')) {
            $description = __('To find your API key go to <code>weMail > Settings > API Keys</code> on your WordPress dashboard.', 'mailoptin');
        } else {
            $description = sprintf(__('weMail Plugin is not activated, please install %sweMail%s and activate to get your API keys.', 'mailoptin'),
                '<a target="_blank" href="https://wordpress.org/plugins/wemail/">', '</a>');
        }

        $settingsArg[] =
            [
                'section_title_without_status' => __('weMail', 'mailoptin'),
                'section_title'                => __('weMail', 'mailoptin') . " $status",
                'type'                         => AbstractConnect::EMAIL_MARKETING_TYPE,
                'wemail_api_key'               => [
                    'type'          => 'text',
                    'obfuscate_val' => true,
                    'label'         => __('API Key', 'mailoptin'),
                    'description'   => $description,
                ],
            ];

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a wemail connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['wemail_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('wemail');

    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}