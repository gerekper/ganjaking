<?php

namespace MailOptin\FacebookCustomAudienceConnect;

use MailOptin\Core\Connections\AbstractConnect;

class ConnectSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'), 10, 99);
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);
    }

    public function connection_settings($arg)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=fb_custom_audience';

            $settingsArg[] = [
                'section_title'         => __('Facebook Custom Audience', 'mailoptin'),
                'type'                  => AbstractConnect::SOCIAL_TYPE,
                'fbca_app_id'           => [
                    'type' => 'arbitrary',
                    'data' => sprintf(
                        '<p style="text-align:center;font-size: 15px;" class="description">%s</p><div class="moBtncontainer"><a target="_blank" href="%s" style="padding:0;margin: 0 auto;" class="mobutton mobtnPush mobtnGreen">%s</a></div>',
                        __('This integration saves your leads to a Facebook custom audience so you can retarget them later.', 'mailoptin'),
                        $url,
                        __('Upgrade to MailOptin Premium to Get It!', 'mailoptin')
                    )
                ],
                'disable_submit_button' => true
            ];

            return array_merge($arg, $settingsArg);
        }

        $connected = AbstractFacebookCustomAudienceConnect::is_connected(true);
        if (true === $connected) {
            $status = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
        } else {
            $msg = '';

            if (is_string($connected)) $msg = esc_html(" &mdash; $connected");

            $status = sprintf("<span style='color:#FF0000'>(%s$msg) </span>", __('Not Connected', 'mailoptin'));
        }

        $learn_more_link = 'https://mailoptin.io/article/connect-mailoptin-facebook-custom-audience/';

        $settings = [
            'section_title_without_status' => __('Facebook Custom Audience', 'mailoptin'),
            'section_title'                => __('Facebook Custom Audience', 'mailoptin') . " $status",
            'type'                         => AbstractConnect::SOCIAL_TYPE,
            'fbca_app_id'                  => [
                'type'        => 'text',
                'label'       => __('Facebook App ID', 'mailoptin'),
                'description' => sprintf(
                    __('Enter your Facebook application ID. %sLearn more%s', 'mailoptin'),
                    '<a target="_blank" href="' . $learn_more_link . '">', '</a>'
                )
            ],
            'fbca_app_secret'              => [
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Facebook App Secret', 'mailoptin'),
                'description'   => sprintf(
                    __('Enter your Facebook application secret. %sLearn more%s', 'mailoptin'),
                    '<a target="_blank" href="' . $learn_more_link . '">', '</a>'
                )
            ],
            'fbca_app_access_token'        => [
                'type'          => 'text',
                'obfuscate_val' => true,
                'label'         => __('Facebook App Access Token', 'mailoptin'),
                'description'   => sprintf(
                    __('Enter your Facebook application access token. %sLearn more%s', 'mailoptin'),
                    '<a target="_blank" href="' . $learn_more_link . '">', '</a>'
                )
            ],
            'fbca_adaccount_id'            => [
                'type'        => 'text',
                'label'       => __('Ad Account ID', 'mailoptin'),
                'description' => sprintf(
                    __('Enter your Facebook Ad account ID. %sLearn more%s', 'mailoptin'),
                    '<a target="_blank" href="' . $learn_more_link . '">', '</a>'
                )
            ]
        ];


        if (AbstractFacebookCustomAudienceConnect::is_connected()) {
            ob_start();
            require dirname(__FILE__) . '/create-audience-tmpl.php';
            $template = ob_get_clean();

            $settings['fbca_create_custom_audience'] = [
                'type'        => 'custom_field_block',
                'data'        => sprintf('<a id="mo-create-fb-custom-audience" href="#" class="button">%s</a>', esc_html__('Create new Facebook Custom Audience', 'mailoptin') . $template),
                'description' => esc_html__('Anytime you want to create a new custom audience in your Facebook ad account, Use the button above.', 'mailoptin')
            ];
        }

        $settingsArg[] = $settings;

        return array_merge($arg, $settingsArg);
    }

    public function output_error_log_link($option, $args)
    {
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['fbca_app_id'])) {
            return;
        }

        //Output error log link if  there is one
        echo AbstractConnect::get_optin_error_log_link('facebookcustomaudience');
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