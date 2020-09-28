<?php

namespace MailOptin\SendFoxConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage extends AbstractSendFoxConnect
{
    public function __construct()
    {
        parent::__construct();

        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'));
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);
    }

    public function get_me()
    {
        $senders = get_transient('mailoptin_sendfox_sender');

        if ($senders === false) {

            $senders = ['' => esc_html__('Your Account...', 'mailoptin')];

            try {

                $response = $this->sendfox_instance()->make_request('me');

                $senders['name'] = $response['name'];
                $senders['email'] = $response['email'];

                set_transient('mailoptin_sendfox_sender', $senders, 5 * MINUTE_IN_SECONDS);

            } catch (\Exception $e) {
            }
        }

        return $senders;
    }

    public function connection_settings($arg)
    {
        $connected = AbstractSendFoxConnect::is_connected(true);
        if (true === $connected) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg = '';
            if (is_string($connected)) {
                $msg = esc_html(" &mdash; $connected");
            }
            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        $settingsArg[] = 
            [
                'section_title_without_status' => __('SendFox', 'mailoptin'),
                'section_title'                => __('SendFox', 'mailoptin') . " $status",
                'type'                         => AbstractConnect::EMAIL_MARKETING_TYPE,
                'sendfox_api_key'             => [
                    'type'          => 'text',
                    'obfuscate_val' => true,
                    'label'         => __('Personal Access Token', 'mailoptin'),
                    'description'   => sprintf(
                        __('Log in to your %sSendFox account%s to create or get your personal access token.', 'mailoptin'),
                        '<a target="_blank" href="https://sendfox.com/account/oauth">',
                        '</a>'
                    ),
                ],
        ];

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a sendfox connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['sendfox_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('sendfox');

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