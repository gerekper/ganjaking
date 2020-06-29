<?php

namespace MailOptin\Connections;

// Exit if accessed directly
use MailOptin\Core\Admin\SettingsPage\AbstractSettingsPage;
use MailOptin\Core\Connections\AbstractConnect;

if ( ! defined('ABSPATH')) {
    exit;
}

class GoogleAnalytics extends AbstractSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', [$this, 'ga_settings']);
    }

    public function ga_settings($arg)
    {
        if ( ! apply_filters('mailoptin_enable_google_analytics', false)) {
            $url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=googleanalytics';

            $settingsArg[] = apply_filters('mailoptin_google_analytics_settings_page', [
                'section_title'         => __('Google Analytics', 'mailoptin'),
                'type'                  => AbstractConnect::ANALYTICS_TYPE,
                'activate_ga'           => [
                    'type' => 'arbitrary',
                    'data' => sprintf(
                        '<p style="text-align:center;font-size: 15px;" class="description">%s</p><div class="moBtncontainer"><a target="_blank" href="%s" style="padding:0;margin: 0 auto;" class="mobutton mobtnPush mobtnGreen">%s</a></div>',
                        __('Google Analytics integration is not available on your plan. Upgrade to any of our premium offering to get it.', 'mailoptin'),
                        $url,
                        __('Upgrade Now!', 'mailoptin')
                    )
                ],
                'disable_submit_button' => true
            ]);

            return array_merge($arg, $settingsArg);
        }

        return $arg;
    }

    /**
     * @return GoogleAnalytics
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}