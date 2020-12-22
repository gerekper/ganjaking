<?php

namespace MailOptin\SendGridConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage extends AbstractSendGridConnect
{
    public function __construct()
    {
        parent::__construct();

        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'));
        add_filter('mailoptin_email_campaign_customizer_page_settings', array($this, 'campaign_customizer_settings'));
        add_filter('mailoptin_email_campaign_customizer_settings_controls', array($this, 'campaign_customizer_controls'), 10, 4);
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);
    }

    public function campaign_customizer_settings($settings)
    {
        $settings['SendGridConnect_suppression_group'] = [
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        ];

        return $settings;
    }

    public function campaign_customizer_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $suppression_groups = ['' => esc_html__('Select...', 'mailoptin')];

        try {
            $response = $this->sendgrid_instance()->make_request(
                'asm/groups'
            );

            $suppression_groups = array_reduce($response['body'], function ($carry, $item) {
                $carry[$item['id']] = $item['name'];

                return $carry;
            }, $suppression_groups);

        } catch (\Exception $e) {

        }

        // always prefix with the name of the connect/connection service.
        $controls['SendGridConnect_suppression_group'] = [
            'type'        => 'select',
            'choices'     => $suppression_groups,
            'label'       => __('Suppression Group', 'mailoptin'),
            'section'     => $customizerClassInstance->campaign_settings_section_id,
            'settings'    => $option_prefix . '[SendGridConnect_suppression_group]',
            'description' => __("Select a Suppression Group to allow recipients to unsubscribe.", 'mailoptin'),
            'priority'    => 199
        ];

        return $controls;
    }

    public function get_senders()
    {
        $senders = get_transient('mailoptin_sendgrid_sender_list');

        if ($senders === false) {

            $senders = ['' => esc_html__('Select...', 'mailoptin')];

            try {

                $response = $this->sendgrid_instance()->make_request('marketing/senders');

                $senders = array_reduce($response['body'], function ($carry, $item) {
                    $carry[$item['id']] = sprintf('%s (%s)', $item['nickname'], $item['from']['email']);

                    return $carry;
                }, $senders);

                if (self::is_http_code_success($response['status_code'])) {
                    set_transient('mailoptin_sendgrid_sender_list', $senders, 5 * MINUTE_IN_SECONDS);
                }

            } catch (\Exception $e) {
            }
        }

        return $senders;
    }

    public function connection_settings($arg)
    {
        $connected = AbstractSendGridConnect::is_connected(true);
        if (true === $connected) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg = '';
            if (is_string($connected)) {
                $msg = esc_html(" &mdash; $connected");
            }
            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        $settings = [
            [
                'section_title_without_status' => __('SendGrid Email Marketing', 'mailoptin'),
                'section_title'                => __('SendGrid Email Marketing Connection', 'mailoptin') . " $status",
                'type'                         => AbstractConnect::EMAIL_MARKETING_TYPE,
                'sendgrid_api_key'             => [
                    'type'          => 'text',
                    'obfuscate_val' => true,
                    'label'         => __('API Key', 'mailoptin'),
                    'description'   => sprintf(
                        __('Log in to your %sSendGrid account%s to generate or get your API key.', 'mailoptin'),
                        '<a target="_blank" href="https://app.sendgrid.com/settings/api_keys">',
                        '</a>'
                    ),
                ],
                'sendgrid_sender'              => [
                    'type'        => 'select',
                    'label'       => __('Verified Sender', 'mailoptin'),
                    'options'     => $this->get_senders(),
                    'description' => esc_html__('Select a verified sender that will be used for sending email automation and newsletters to your SendGrid Email Marketing contacts.', 'mailoptin'),
                ]
            ]
        ];

        if ( ! self::is_connected()) {
            unset($settings[0]['sendgrid_sender']);
        }

        return array_merge($arg, $settings);
    }

    public function output_error_log_link($option, $args)
    {
        //Not a sendgrid connection section
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['sendgrid_api_key'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('sendgrid');

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