<?php

namespace MailOptin\GEMConnect;

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

        $connected = AbstractGEMConnect::is_connected();
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
            'section_title_without_status'             => __('GoDaddy Email Marketing', 'mailoptin'),
            'section_title' => __('GoDaddy Email Marketing', 'mailoptin') . " $status",
            'type'          => AbstractConnect::EMAIL_MARKETING_TYPE,
            'gem_email'     => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Account Email Address', 'mailoptin'),
                'description'   => sprintf(
                    __('Enter your GoDaddy Email Marketing email address here. %1$sGet it here%2$s.', 'mailoptin'),
                    '<a target="_blank" href="https://gem.godaddy.com/user/edit?account_info_tabs=account_info_personal">',
                    '</a>'
                ),
            ),
            'gem_api_key'   => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Your API Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your account and visit the %1$semail settings%2$s page to get your API Key.', 'mailoptin'),
                    '<a target="_blank" href="https://gem.godaddy.com/user/edit?account_info_tabs=account_info_personal">',
                    '</a>'
                ),
            )
        );

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['gem_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('gem');

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