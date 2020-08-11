<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

class CustomizerSettings extends AbstractCustomizer
{
    /** @var \WP_Customize_Manager */
    private $wp_customize;

    /** @var Customizer */
    private $customizerClassInstance;

    /** @var string DB option name prefix */
    private $option_prefix;

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function __construct($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $this->wp_customize            = $wp_customize;
        $this->customizerClassInstance = $customizerClassInstance;
        $this->option_prefix           = $option_prefix;

        parent::__construct($customizerClassInstance->email_campaign_id);
    }

    public function available_tags_settings()
    {
        $settings_args = apply_filters("mailoptin_email_campaign_customizer_available_tags_settings", array(
                'email_digest_tag_help'            => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_tags_header'                 => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'campaign_tags_header'             => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_id_shortcode'                => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_title_shortcode'             => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_feature_image_shortcode'     => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_feature_image_url_shortcode' => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_excerpt_shortcode'           => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_content_shortcode'           => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_categories_shortcode'        => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_terms_shortcode'             => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_date_shortcode'              => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_date_gmt_shortcode'          => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_url_shortcode'               => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_author_name_shortcode'       => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_author_website_shortcode'    => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_author_email_shortcode'      => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_meta_shortcode'              => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'unsubscribe_shortcode'            => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'web_version_shortcode'            => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_name_shortcode'           => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_address_shortcode'        => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_address_2_shortcode'      => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_city_shortcode'           => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_state_shortcode'          => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_zip_shortcode'            => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'company_country_shortcode'        => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                )
            )
        );

        foreach ($settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }
    }

    public function preview_settings()
    {
        $settings_args = apply_filters("mailoptin_email_campaign_customizer_preview_settings", array(
                'post_as_preview' => array(
                    'type'      => 'option',
                    'transport' => 'refresh',
                )
            )
        );

        foreach ($settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }
    }

    /**
     * Customize setting for email campaign setup.
     */
    public function campaign_settings()
    {
        $email_campaign_settings_args = apply_filters("mailoptin_email_campaign_customizer_page_settings", [
                'settings_controls_tab_toggle'    => [
                    'default'   => 'general',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'email_campaign_title'            => [
                    'default'   => $this->customizer_defaults['email_campaign_title'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'email_campaign_subject'          => [
                    'default'   => $this->customizer_defaults['email_campaign_subject'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'item_number'                     => [
                    'default'           => $this->customizer_defaults['item_number'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint',
                ],
                'post_content_type'               => [
                    'default'   => $this->customizer_defaults['post_content_type'],
                    'type'      => 'option',
                    'transport' => 'refresh'
                ],
                'post_content_length'             => [
                    'default'           => $this->customizer_defaults['post_content_length'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint',
                ],
                'custom_post_type'                => [
                    'default'   => $this->customizer_defaults['custom_post_type'],
                    'type'      => 'option',
                    'transport' => 'postMessage'
                ],
                'custom_post_type_settings'       => [
                    'default'   => $this->customizer_defaults['custom_post_type_settings'],
                    'type'      => 'option',
                    'transport' => 'postMessage'
                ],
                'post_categories'                 => [
                    'default'   => $this->customizer_defaults['post_categories'],
                    'type'      => 'option',
                    'transport' => 'postMessage'
                ],
                'post_tags'                       => [
                    'default'   => $this->customizer_defaults['post_tags'],
                    'type'      => 'option',
                    'transport' => 'postMessage'
                ],
                'post_authors'                    => [
                    'default'   => $this->customizer_defaults['post_authors'],
                    'type'      => 'option',
                    'transport' => 'postMessage'
                ],
                'recipient_header'                => [
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'connection_service'              => [
                    'default'   => $this->customizer_defaults['connection_service'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ],
                'connection_email_list'           => [
                    'default'   => $this->customizer_defaults['connection_email_list'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'schedule_header'                 => [
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'send_immediately'                => [
                    'default'   => $this->customizer_defaults['send_immediately'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'schedule_type'                   => [
                    'default'   => $this->customizer_defaults['schedule_type'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'schedule_digit'                  => [
                    'default'           => $this->customizer_defaults['schedule_digit'],
                    'type'              => 'option',
                    'transport'         => 'postMessage',
                    'sanitize_callback' => 'absint',
                ],
                'schedule_interval'               => [
                    'default'   => $this->customizer_defaults['schedule_interval'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'schedule_time'                   => [
                    'default'   => $this->customizer_defaults['schedule_time'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'schedule_day'                    => [
                    'default'   => $this->customizer_defaults['schedule_day'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'schedule_month_date'             => [
                    'default'   => $this->customizer_defaults['schedule_month_date'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'email_campaign_settings_notice'  => [
                    'default'   => false,
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'email_campaign_settings_notice2' => [
                    'default'   => false,
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'ajax_nonce'                      => [
                    'default'   => wp_create_nonce('customizer-fetch-email-list'),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ],
                'code_your_own'                   => [
                    'default'           => $this->customizer_defaults['code_your_own'],
                    'type'              => 'option',
                    'transport'         => 'postMessage',
                    'validate_callback' => function ($validity, $value) {
                        if (empty($value)) {
                            $validity->add('template_empty', __('Email template cannot be empty.', 'mailoptin'));

                            return $validity;
                        }

                        return $validity;
                    }
                ],
            ]
        );

        if ($this->email_campaign_type == ER::POSTS_EMAIL_DIGEST) {
            $email_campaign_settings_args['custom_post_type']['transport']          = 'refresh';
            $email_campaign_settings_args['custom_post_type_settings']['transport'] = 'refresh';
            $email_campaign_settings_args['post_categories']['transport']           = 'refresh';
            $email_campaign_settings_args['post_tags']['transport']                 = 'refresh';
            $email_campaign_settings_args['post_authors']['transport']              = 'refresh';
        }

        foreach ($email_campaign_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting(new EC_Customizer_Setting(
                    $this->wp_customize,
                    $this->option_prefix . '[' . $id . ']',
                    $args
                )
            );
        }
    }


    /**
     * Customize setting for all template page controls.
     */
    public function page_settings()
    {
        $page_settings_args = apply_filters("mailoptin_email_campaign_customizer_page_settings", array(
                'page_background_color'     => array(
                    'default'           => $this->customizer_defaults['page_background_color'],
                    'type'              => 'option',
                    'sanitize_callback' => 'sanitize_hex_color',
                    'transport'         => 'postMessage',
                ),
                'custom_css_upgrade_notice' => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
            )
        );

        foreach ($page_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }
    }

    /**
     * Customize setting for all template header controls.
     */
    public function header_settings()
    {
        $header_settings_args = apply_filters("mailoptin_email_campaign_customizer_header_settings", array(
                'header_controls_tab_toggle'    => array(
                    'default'   => 'general',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'header_removal'                => array(
                    'default'   => $this->customizer_defaults['header_removal'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'header_logo'                   => array(
                    'default' => $this->customizer_defaults['header_logo'],
                    'type'    => 'option',
                ),
                'header_background_color'       => array(
                    'default'   => $this->customizer_defaults['header_background_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'header_text_color'             => array(
                    'default'   => $this->customizer_defaults['header_text_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'header_text'                   => array(
                    'default'   => $this->customizer_defaults['header_text'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'header_web_version_link_label' => array(
                    'default'   => $this->customizer_defaults['header_web_version_link_label'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'header_web_version_link_color' => array(
                    'default'   => $this->customizer_defaults['header_web_version_link_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
            )
        );

        foreach ($header_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }
    }


    /**
     * Customize setting for all template content controls.
     */
    public function content_settings()
    {
        $content_settings_args = apply_filters("mailoptin_email_campaign_customizer_content_settings", array(
                'content_controls_tab_toggle'              => array(
                    'default'   => 'general',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_before_main_content'              => array(
                    'default'   => $this->customizer_defaults['content_before_main_content'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_after_main_content'               => array(
                    'default'   => $this->customizer_defaults['content_after_main_content'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'default_image_url'                        => array(
                    'default'   => $this->customizer_defaults['default_image_url'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_background_color'                 => array(
                    'default'   => $this->customizer_defaults['content_background_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_text_color'                       => array(
                    'default'   => $this->customizer_defaults['content_text_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_headline_color'                   => array(
                    'default'   => $this->customizer_defaults['content_headline_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_alignment'                        => array(
                    'default'   => $this->customizer_defaults['content_alignment'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_post_meta'                        => array(
                    'default'   => $this->customizer_defaults['content_post_meta'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'content_remove_post_link'                 => array(
                    'default'   => $this->customizer_defaults['content_remove_post_link'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'content_remove_post_body'                 => array(
                    'default'   => $this->customizer_defaults['content_remove_post_body'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'content_remove_feature_image'             => array(
                    'default'   => $this->customizer_defaults['content_remove_feature_image'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'content_remove_ellipsis_button'           => array(
                    'default'   => $this->customizer_defaults['content_remove_ellipsis_button'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'content_ellipsis_button_alignment'        => array(
                    'default'   => $this->customizer_defaults['content_ellipsis_button_alignment'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_ellipsis_button_background_color' => array(
                    'default'   => $this->customizer_defaults['content_ellipsis_button_background_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_ellipsis_button_text_color'       => array(
                    'default'   => $this->customizer_defaults['content_ellipsis_button_text_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_title_font_size'                  => array(
                    'default'   => $this->customizer_defaults['content_title_font_size'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_body_font_size'                   => array(
                    'default'   => $this->customizer_defaults['content_body_font_size'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'content_ellipsis_button_label'            => array(
                    'default'   => $this->customizer_defaults['content_ellipsis_button_label'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'newsletter_editor_content'                => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'email_newsletter_content'                 => array(
                    'default'   => $this->customizer_defaults['email_newsletter_content'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                )
            )
        );

        foreach ($content_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }
    }

    /**
     * Customize setting for all template footer controls.
     */
    public function footer_settings()
    {
        $footer_settings_args = apply_filters("mailoptin_email_campaign_customizer_footer_settings", array(
            'footer_controls_tab_toggle'    => array(
                'default'   => 'general',
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_removal'                => array(
                'default'   => $this->customizer_defaults['footer_removal'],
                'type'      => 'option',
                'transport' => 'refresh',
            ),
            'footer_background_color'       => array(
                'default'   => $this->customizer_defaults['footer_background_color'],
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_text_color'             => array(
                'default'   => $this->customizer_defaults['footer_text_color'],
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_font_size'              => array(
                'default'   => apply_filters('footer_font_size', '12'),
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_copyright_line'         => array(
                'default'   => $this->customizer_defaults['footer_copyright_line'],
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_unsubscribe_line'       => array(
                'default'   => $this->customizer_defaults['footer_unsubscribe_line'],
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_unsubscribe_link_label' => array(
                'default'   => $this->customizer_defaults['footer_unsubscribe_link_label'],
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_unsubscribe_link_color' => array(
                'default'   => $this->customizer_defaults['footer_unsubscribe_link_color'],
                'type'      => 'option',
                'transport' => 'postMessage',
            ),
            'footer_description'            => array(
                'default'   => $this->customizer_defaults['footer_description'],
                'type'      => 'option',
                'transport' => 'refresh',
            )
        ));

        foreach ($footer_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }
    }
}