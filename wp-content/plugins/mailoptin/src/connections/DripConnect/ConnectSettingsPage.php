<?php

namespace MailOptin\DripConnect;

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
        $connected = AbstractDripConnect::is_connected( true );
        if ( true === $connected ) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg    = '';
            if( is_string($connected)){
                $msg = esc_html(" &mdash; $connected");
            }
            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        $settingsArg[] = array(
            'section_title_without_status'             => __('Drip', 'mailoptin'),
            'section_title' => __('Drip Connection', 'mailoptin') . " $status",
            'type' => AbstractConnect::EMAIL_MARKETING_TYPE,
            'drip_api_token' => array(
                'type' => 'text',
                'obfuscate_val' => true,
                'label' => __('Enter API Token', 'mailoptin'),
                'description' => sprintf(
                    __('Log in to your %sDrip account%s to get your api token.', 'mailoptin'),
                    '<a target="_blank" href="https://www.getdrip.com/user/edit">',
                    '</a>'
                ),
            ),
            'drip_account_id' => array(
                'type' => 'text',
                'label' => __('Enter Account ID', 'mailoptin'),
                'description' => sprintf(
                    __('Log in to your %sDrip account%s to get your "account id" at "General Info" settings.', 'mailoptin'),
                    '<a target="_blank" href="https://www.getdrip.com/signin">',
                    '</a>'
                ),
            )
        );

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a drip connection section
        if( MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || !isset($args['drip_api_token'])){
            return;
        }
        
        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('drip');

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