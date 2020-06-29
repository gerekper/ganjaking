<?php

namespace MailOptin\ElementorConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'), 10, 99);
    }

    public function connection_settings($arg)
    {
        if (Connect::is_connected()) {
            $status        = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
            $settingsArg[] = array(
                'section_title_without_status' => __('Elementor', 'mailoptin'),
                'section_title'                => __('Elementor Connection', 'mailoptin') . " $status",
                'type'                         => AbstractConnect::OTHER_TYPE,
                'elementor_activate'           => array(
                    'type'        => 'arbitrary',
                    'description' => sprintf(
                        __('%sThis integration is enabled because Elementor Pro is currently installed and activated.%s', 'mailoptin'),
                        '<p style="text-align: center">',
                        '</p>'
                    )
                ),
                'disable_submit_button'        => true
            );
        } else {
            $status        = sprintf('<span style="color:#FF0000">(%s)</span>', __('Not Connected', 'mailoptin'));
            $settingsArg[] = array(
                'section_title_without_status' => __('Elementor', 'mailoptin'),
                'section_title'                => __('Elementor Connection', 'mailoptin') . " $status",
                'type'                         => AbstractConnect::OTHER_TYPE,
                'elementor_activate'           => array(
                    'type'        => 'arbitrary',
                    'description' => sprintf(
                        __('%sThis integration is disabled because you currently do not have Elementor Pro installed and activated.%s', 'mailoptin'),
                        '<p style="text-align: center">',
                        '</p>'
                    ),
                ),
                'disable_submit_button'        => true
            );
        }

        return array_merge($arg, $settingsArg);
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