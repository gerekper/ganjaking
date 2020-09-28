<?php

namespace MailOptin\EmmaConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'));
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);
    }

    public function connection_settings($arg)
    {
        $connected = AbstractEmmaConnect::is_connected(true);
        if (true === $connected) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg = '';
            if (is_string($connected)) {
                $msg = esc_html(" &mdash; $connected");
            }
            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        $settingsArg[] = array(
            'section_title_without_status' => __('Emma', 'mailoptin'),
            'section_title'                => __('Emma Connection', 'mailoptin') . " $status",
            'type'                         => AbstractConnect::EMAIL_MARKETING_TYPE,
            'emma_public_api_key'          => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Public Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your %sEmma account%s and go to the "API KEY" section in Profile settings for your Public key.', 'mailoptin'),
                    '<a target="_blank" href="https://settings.e2ma.net/profile/api-key">',
                    '</a>'
                ),
            ),
            'emma_private_api_key'         => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Private Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your %sEmma account%s and go to the "API KEY" section in Profile settings for your Private key.', 'mailoptin'),
                    '<a target="_blank" href="https://settings.e2ma.net/profile/api-key">',
                    '</a>'
                ),
            ),
            'emma_account_id'              => array(
                'type'        => 'text',
                'label'       => __('Account ID', 'mailoptin'),
                'description' => sprintf(
                    __('Log in to your %sEmma account%s and go to the "API KEY" section in Profile settings for your Account ID.', 'mailoptin'),
                    '<a target="_blank" href="https://settings.e2ma.net/profile/api-key">',
                    '</a>'
                ),
            )
        );

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a emma connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['emma_public_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('emma');

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