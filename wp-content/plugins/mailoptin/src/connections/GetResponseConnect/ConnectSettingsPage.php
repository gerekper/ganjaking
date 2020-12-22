<?php

namespace MailOptin\GetResponseConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'));
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);

        add_action('mailoptin_aconnection_settingsfter_connections_settings_page', [$this, 'toggle_js_Script']);
    }

    public function toggle_js_Script()
    {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                function is_checked() {
                    return $('#getresponse_is_360').is(':checked');
                }

                $('#getresponse360_registered_domain_row').toggle(is_checked());
                $('#getresponse360_country_row').toggle(is_checked());

                $('#getresponse_is_360').change(function () {
                    $('#getresponse360_registered_domain_row').toggle(this.checked);
                    $('#getresponse360_country_row').toggle(this.checked);
                })
            });
        </script>
        <?php
    }

    public function connection_settings($arg)
    {
        $connected = AbstractGetResponseConnect::is_connected(true);
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
            'section_title_without_status'     => __('GetResponse', 'mailoptin'),
            'section_title'                    => __('GetResponse Connection', 'mailoptin') . " $status",
            'type'                             => AbstractConnect::EMAIL_MARKETING_TYPE,
            'getresponse_api_key'              => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Enter API Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your %sGetResponse account%s to get your API Key.', 'mailoptin'),
                    '<a target="_blank" href="https://app.getresponse.com/manage_api.html">',
                    '</a>'
                ),
            ),
            'getresponse_is_360'               => array(
                'type'        => 'checkbox',
                'label'       => __('GetResponse MAX Account', 'mailoptin'),
                'description' => __('Check this only if you are a GetResponse MAX customer.', 'mailoptin'),
            ),
            'getresponse360_registered_domain' => array(
                'type'        => 'text',
                'label'       => __('GetResponse MAX Domain', 'mailoptin'),
                'description' => __('Enter your GetResponse MAX account registered domain.', 'mailoptin')
            ),
            'getresponse360_country'           => array(
                'type'        => 'select',
                'label'       => __('GetResponse MAX Country', 'mailoptin'),
                'options'     => [
                    'none'   => __('Select...', 'mailoptin'),
                    'poland' => __('Poland', 'mailoptin'),
                    'others' => __('Others', 'mailoptin'),
                ],
                'description' => __('Select country your GetResponse MAX account is associated with.', 'mailoptin'),
            )
        );

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a getresponse connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['getresponse_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('getresponse');

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