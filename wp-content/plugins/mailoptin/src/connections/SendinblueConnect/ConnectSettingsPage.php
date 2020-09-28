<?php

namespace MailOptin\SendinblueConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'), 1);
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);
    }

    public function connection_settings($arg)
    {
        $connected = AbstractSendinblueConnect::is_connected(true);
        if (true === $connected) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg = '';
            if (is_string($connected)) {
                $msg = esc_html(" &mdash; $connected");
            }
            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        $support_id_description = sprintf(
            __('%sClick here to get it%s. This is only required to unlock our premium integration with Elementor Forms.', 'mailoptin'),
            '<a target="_blank" href="https://bit.ly/2ZTmjgH">',
            '</a>'
        );

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $support_id_description = sprintf(__('To get it, %sclick here%s.', 'mailoptin'),
                '<a target="_blank" href="https://bit.ly/2ZTmjgH">',
                '</a>'
            );
        }

        $settingsArg[] = array(
            'section_title_without_status' => __('Sendinblue', 'mailoptin'),
            'section_title'                => __('Sendinblue Connection', 'mailoptin') . " $status",
            'type'                         => AbstractConnect::EMAIL_MARKETING_TYPE,
            'sendinblue_api_key'           => array(
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('API Key', 'mailoptin'),
                'description'   => sprintf(
                    __('Log in to your %sSendinblue account%s to get your API v3 key.', 'mailoptin'),
                    '<a target="_blank" href="https://bit.ly/3kBrmu2">',
                    '</a>'
                ),
            ),
            'sendinblue_support_id'        => array(
                'type'        => 'text',
                'label'       => __('Support ID', 'mailoptin'),
                'description' => $support_id_description,
            ),
            'sendinblue_create_acc_cta'    => array(
                'type'  => 'custom_field_block',
                'label' => '',
                'data'  => sprintf(
                    '<a href="%s" target="_blank" class="button">%s</a>',
                    'https://bit.ly/33O2EQq',
                    esc_html__("Don't have a SendinBlue account? Create one", 'mailoptin')
                )
            )
        );

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a sendinblue connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['sendinblue_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('sendinblue');

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