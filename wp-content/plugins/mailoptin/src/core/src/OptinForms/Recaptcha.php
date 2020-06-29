<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use WP_Error;

class Recaptcha
{
    public function __construct()
    {
        add_filter('mailoptin_settings_page', [$this, 'settings_page'], 2222);

        add_filter('mo_optin_form_custom_field_output', [$this, 'render_field'], 10, 4);

        add_filter('mo_subscription_form_error', [$this, 'validate_submission'], 10, 2);
    }

    public function enqueue_script()
    {
        $site_key    = Settings::instance()->recaptcha_site_key();
        $site_secret = Settings::instance()->recaptcha_site_secret();

        if (empty($site_key) || empty($site_secret)) return;

        $type = Settings::instance()->recaptcha_type();
        $src  = 'https://www.google.com/recaptcha/api.js?onload=moFormRecaptchaLoadCallback&render=explicit';
        if ($type === 'v3') {
            $site_key = Settings::instance()->recaptcha_site_key();
            $src      = 'https://www.google.com/recaptcha/api.js?onload=moFormRecaptchaLoadCallback&render=' . $site_key;
        }

        wp_enqueue_script('mo-recaptcha-script', $src, ['mailoptin'], MAILOPTIN_VERSION_NUMBER, true);
    }

    public function validate_submission($response, ConversionDataBuilder $conversion_data)
    {
        $site_key       = Settings::instance()->recaptcha_site_key();
        $site_secret    = Settings::instance()->recaptcha_site_secret();
        $recaptcha_type = Settings::instance()->recaptcha_type();

        if (empty($site_key) || empty($site_secret)) return $response;

        $optin_campaign_id = $conversion_data->optin_campaign_id;
        $fields            = OptinCampaignsRepository::form_custom_fields($optin_campaign_id);
        $has_recaptcha     = false;
        foreach ($fields as $field) {
            if (in_array($field['field_type'], ['recaptcha_v2', 'recaptcha_v3'])) {
                $has_recaptcha = true;
                break;
            }
        }

        if ( ! $has_recaptcha) return $response;

        if (empty($conversion_data->payload['g-recaptcha-response'])) {
            return new WP_Error('mo-empty-captcha', __('reCAPTCHA is required.', 'mailoptin'));
        }

        $request = [
            'body' => [
                'secret'   => $site_secret,
                'response' => $conversion_data->payload['g-recaptcha-response'],
                'remoteip' => \MailOptin\Core\get_ip_address(),
            ],
        ];

        $result        = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', $request);
        $response_code = wp_remote_retrieve_response_code($result);

        if (200 !== (int)$response_code) {
            /* translators: %d: Response code. */
            return new WP_Error('mo-captcha-cant-connect', sprintf(esc_html__('Can not connect to the reCAPTCHA server (%d).', 'mailoptin'), $response_code));
        }

        $body = json_decode(wp_remote_retrieve_body($result), true);

        if ( ! isset($body['success']) || ! $body['success']) {
            return new WP_Error('mo-empty-captcha', esc_html__('Google reCAPTCHA verification failed, please try again.', 'mailoptin'));
        }

        if ($recaptcha_type == 'v3') {
            $score           = $body['score'];
            $threshold_score = Settings::instance()->recaptcha_score();
            if (empty($threshold_score)) {
                $threshold_score = '0.5';
            }
            if ($score < $threshold_score) {
                return new WP_Error('mo-empty-captcha', esc_html__('Google reCAPTCHA verification failed, please try again.', 'mailoptin'));
            }
        }

        return $response;
    }

    public function render_field($output, $field_type, $field, $atts)
    {
        if ( ! in_array($field_type, ['recaptcha_v2', 'recaptcha_v3'])) return $output;

        $recaptcha_style = ! empty($field['recaptcha_v2_style']) ? $field['recaptcha_v2_style'] : 'light';
        $recaptcha_size  = ! empty($field['recaptcha_v2_size']) ? $field['recaptcha_v2_size'] : 'normal';

        $site_key    = Settings::instance()->recaptcha_site_key();
        $site_secret = Settings::instance()->recaptcha_site_secret();

        $output .= $atts['tag_start'];
        if (\MailOptin\Core\current_user_has_privilege() && (empty($site_key) || empty($site_secret))) {
            $output .= '<div style="margin:5px 0;color:#31708f;background-color: #d9edf7;border-color: #bcdff1;">' . esc_html__('To use reCAPTCHA, you need to add the API Key and complete the setup process in Dashboard > MailOptin > Settings > reCAPTCHA.', 'mailoptin') . '</div>';
        } elseif ($field_type == 'recaptcha_v2') {
            $output .= "<div style='margin: 5px 0' class=\"mo-g-recaptcha mo-optin-form-custom-field\" data-type=\"v2\" data-sitekey=\"$site_key\" data-theme='$recaptcha_style' data-size='$recaptcha_size'></div>";
        } else {
            $output .= "<div style='margin: 5px 0' class=\"mo-g-recaptcha mo-optin-form-custom-field\" data-type=\"v3\" data-sitekey=\"$site_key\"></div>";
        }

        $output .= $atts['tag_end'];

        return $output;
    }

    public function settings_page($settings)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {

            $settings['recaptcha_settings'] = [
                'tab_title' => __('reCAPTCHA', 'mailoptin'),
                [
                    'section_title'         => __('reCAPTCHA Settings', 'mailoptin'),
                    'recaptcha_type'        => [
                        'label' => __('Type', 'mailoptin'),
                        'type'  => 'arbitrary',
                        'data'  => sprintf('<p style="text-align: center">%s</p><div class="moBtncontainer mobtnUpgrade"><a target="_blank" href="%s" class="mobutton mobtnPush mobtnGreen">%s</a></div>',
                            esc_html__('Do you want to stop spam bots from filling out your form? You can add reCAPTCHA to your forms to protects against spam and other types of automated abuse.', 'mailoptin'),
                            'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=recaptcha_unlock',
                            esc_html__('Upgrade to Unlock', 'mailoptin')
                        )
                    ],
                    'disable_submit_button' => true,
                ]
            ];

            return $settings;
        }

        $value = Settings::instance()->recaptcha_type();

        $html = sprintf(
            '<label><input class="mo-recaptcha-type" type="radio" name="mailoptin_settings[recaptcha_type]" value="v2" %s>%s</label>&nbsp;&nbsp;',
            checked($value, 'v2', false),
            __('reCAPTCHA v2', 'mailoptin')
        );

        $html .= sprintf(
            '<label><input class="mo-recaptcha-type" type="radio" name="mailoptin_settings[recaptcha_type]" value="v3" %s>%s</label>',
            checked($value, 'v3', false),
            __('reCAPTCHA v3', 'mailoptin')
        );

        $html .= '<script type="text/javascript">
jQuery(function($) {
$("input.mo-recaptcha-type").on("change", function() {
   var type = $("input[name=\'mailoptin_settings[recaptcha_type]\']:checked").val();
   if(type === "v3") {
       $("#recaptcha_score_row").show();
}
   else {
       $("#recaptcha_score_row").hide();
   }
}).change();
});
</script>';

        $settings['recaptcha_settings'] = [
            'tab_title' => __('reCAPTCHA', 'mailoptin'),
            [
                'section_title'         => __('reCAPTCHA Settings', 'mailoptin'),
                'recaptcha_type'        => [
                    'label' => __('Type', 'mailoptin'),
                    'type'  => 'custom_field_block',
                    'data'  => $html
                ],
                'recaptcha_site_key'    => [
                    'type'  => 'text',
                    'label' => __('Site Key', 'mailoptin')
                ],
                'recaptcha_site_secret' => [
                    'type'  => 'text',
                    'label' => __('Site Secret', 'mailoptin')
                ],
                'recaptcha_score'       => [
                    'type'        => 'text',
                    'label'       => __('Score Threshold', 'mailoptin'),
                    'value'       => '0.5',
                    'description' => __('The score at which users will fail reCAPTCHA v3 verification. Scores can range from from 0.0 (very likely a bot) to 1.0 (very likely a human). Default is 0.5', 'mailoptin')
                ]
            ]
        ];

        return $settings;
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