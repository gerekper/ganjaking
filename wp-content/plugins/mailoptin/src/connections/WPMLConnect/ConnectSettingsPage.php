<?php

namespace MailOptin\WPMLConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'), 10, 99);
    }

    public function connection_settings($arg)
    {
        $status = sprintf('<span style="color:#FF0000">(%s)</span>', __('Not Connected', 'mailoptin'));
        if (Connect::is_connected()) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        }

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=wpml';

            $settingsArg[] = [
                'section_title'         => __('WPML', 'mailoptin'),
                'type'                  => AbstractConnect::OTHER_TYPE,
                'wpml_activate'     => [
                    'type' => 'arbitrary',
                    'data' => sprintf(
                        '<p style="text-align:center;font-size: 15px;" class="description">%s</p><div class="moBtncontainer"><a target="_blank" href="%s" style="padding:0;margin: 0 auto;" class="mobutton mobtnPush mobtnGreen">%s</a></div>',
                        __('WPML integration is a paid feature of MailOptin.', 'mailoptin'),
                        $url,
                        __('Upgrade Now!', 'mailoptin')
                    )
                ],
                'disable_submit_button' => true
            ];

            return array_merge($arg, $settingsArg);
        }

        if (defined('ICL_LANGUAGE_CODE')) {
            $settingsArg[] = array(
                'section_title_without_status' => __('WPML', 'mailoptin'),
                'section_title'                => __('WPML Connection', 'mailoptin') . " $status",
                'type'                         => AbstractConnect::OTHER_TYPE,
                'wpml_activate'            => array(
                    'type'        => 'arbitrary',
                    'description' => sprintf(
                        __('%sThis integration is enabled because WPML plugin is currently installed and activated.%s', 'mailoptin'),
                        '<p style="text-align: center">',
                        '</p>'
                    )
                ),
                'disable_submit_button'        => true,
            );
        } else {
            $settingsArg[] = array(
                'section_title_without_status'             => __('WPML', 'mailoptin'),
                'section_title'         => __('WPML Connection', 'mailoptin') . " $status",
                'type'                  => AbstractConnect::OTHER_TYPE,
                'wpml_activate'     => array(
                    'type'        => 'arbitrary',
                    'description' => sprintf(
                        __('%sThis integration is disabled because you currently do not have WPML plugin installed and activated.%s', 'mailoptin'),
                        '<p style="text-align: center">',
                        '</p>'
                    ),
                ),
                'disable_submit_button' => true,
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