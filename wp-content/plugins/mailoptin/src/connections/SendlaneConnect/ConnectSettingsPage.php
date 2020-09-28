<?php

namespace MailOptin\SendlaneConnect;

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
        $connected = AbstractSendlaneConnect::is_connected(true);
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
            'section_title_without_status' => __('Sendlane', 'mailoptin'),
            'section_title'                => __('Sendlane Connection', 'mailoptin') . " $status",
            'type'                         => AbstractConnect::EMAIL_MARKETING_TYPE,
            'sendlane_api_key'             => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Enter API Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your %sSendlane account%s, go to "Account Settings" to get your API key.', 'mailoptin'),
                    '<a target="_blank" href="https://sendlane.com/login">',
                    '</a>'
                ),
            ),
            'sendlane_hash_key'            => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Enter Hash Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your %sSendlane account%s, go to "Account Settings" to get your hash key.', 'mailoptin'),
                    '<a target="_blank" href="https://sendlane.com/login">',
                    '</a>'
                ),
            ),
            'sendlane_domain'              => array(
                'type'        => 'text',
                'label'       => __('Enter Sendlane Domain', 'mailoptin'),
                'description' => sprintf(
                    __('Log in to your %sSendlane account%s, go to "Account Settings" to get your sendlane domain.', 'mailoptin'),
                    '<a target="_blank" href="https://sendlane.com/login">',
                    '</a>'
                ),
            )
        );

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a sendlane connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['sendlane_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('sendlane');

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