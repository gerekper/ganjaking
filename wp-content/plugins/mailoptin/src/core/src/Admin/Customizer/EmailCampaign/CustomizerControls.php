<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Select_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Controls_Tab_Toggle;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Input_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_EA_CPT_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Email_Schedule_Time_Fields_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Multiple_Checkbox;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Range_Value_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Tinymce_Expanded_Editor;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_View_Tags_Shortcode_Content;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

class CustomizerControls
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

        $this->selective_control_modifications();

        add_action('customize_controls_print_footer_scripts', function () {
            ?>
            <script type="text/javascript">
                var mailoptin_tab_control_config = <?php echo wp_json_encode($this->tab_toggle_controls_config());?>;
            </script>
            <?php
        });
    }

    public function tab_toggle_controls_config()
    {
        return apply_filters('mailoptin_email_campaign_tab_toggle_config',
            [
                'general' => apply_filters('mailoptin_email_campaign_tab_toggle_general_config', [
                    'footer_removal',
                    'footer_copyright_line',
                    'footer_description',
                    'footer_unsubscribe_line',
                    'footer_unsubscribe_link_label',

                    'header_logo',
                    'header_removal',
                    'header_web_version_link_label',
                    'header_text',

                    'content_before_main_content',
                    'content_after_main_content',
                    'content_post_meta',
                    'content_remove_post_link',
                    'content_remove_post_body',
                    'content_remove_feature_image',
                    'default_image_url',
                    'content_remove_ellipsis_button',
                    'content_ellipsis_button_label'
                ]),
                'style'   => apply_filters('mailoptin_email_campaign_tab_toggle_style_config', [
                    'footer_background_color',
                    'footer_text_color',
                    'footer_font_size',
                    'footer_unsubscribe_link_color',

                    'header_background_color',
                    'header_text_color',
                    'header_web_version_link_color',

                    'content_alignment',
                    'content_ellipsis_button_alignment',
                    'content_background_color',
                    'content_text_color',
                    'content_headline_color',
                    'content_title_font_size',
                    'content_body_font_size',
                    'content_ellipsis_button_text_color',
                    'content_ellipsis_button_background_color'
                ]),
                'advance' => apply_filters('mailoptin_email_campaign_tab_toggle_advance_config', [])
            ]);
    }

    /**
     * All code, filer, action to make modification to a control will go here.
     */
    public function selective_control_modifications()
    {
        add_filter('mailoptin_customizer_settings_email_campaign_subject_description',
            function ($description, $campaign_type) {
                if (ER::NEW_PUBLISH_POST == $campaign_type) {
                    $description = sprintf(
                        __('Available placeholders for use in subject line:%s %s %s %s', 'mailoptin'),
                        '<br><strong>{{title}}</strong>: ',
                        __('title of new published post.', 'mailoptin'),
                        '<br><strong>{{date}}</strong>: ',
                        __('date post was published. Accept PHP date format like so {{date format="l jS"}}', 'mailoptin')
                    );
                }

                return $description;
            }, 10, 2);
    }

    public function campaign_settings_controls()
    {
        $saved_connection_service = ER::get_customizer_value(
            $this->customizerClassInstance->email_campaign_id,
            'connection_service'
        );

        // prepend 'Select...' to the array of email list.
        // because select control will be hidden if no choice is found.
        $connection_email_list = ['' => __('Select...', 'mailoptin')] + ConnectionsRepository::connection_email_list($saved_connection_service);

        $campaign_type = $this->customizerClassInstance->email_campaign_type;

        $custom_post_type_options = [];

        if (apply_filters('mailoptin_enable_email_automation_cpt_support', false)) {
            $custom_post_type_options = ControlsHelpers::custom_post_types();
        }

        $campaign_settings_controls = [
            'code_your_own'             => apply_filters('mailoptin_customizer_settings_campaign_code_your_own_args', [
                    'type'     => 'hidden',
                    // simple hack because control won't render if label is empty.
                    'label'    => '&nbsp;',
                    'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings' => $this->option_prefix . '[code_your_own]',
                    // 999 cos we want it to be bottom.
                    'priority' => 5,
                ]
            ),
            'email_campaign_title'      => apply_filters('mo_optin_form_customizer_email_campaign_title_args', [
                    'type'     => 'text',
                    'label'    => __('Automation Title', 'mailoptin'),
                    'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings' => $this->option_prefix . '[email_campaign_title]',
                    'priority' => 10,
                ]
            ),
            'email_campaign_subject'    => new WP_Customize_Custom_Input_Control(
                $this->wp_customize,
                'email_campaign_subject',
                apply_filters('mailoptin_customizer_settings_campaign_subject_args', [
                        'label'           => __('Email Subject', 'mailoptin'),
                        'section'         => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'        => $this->option_prefix . '[email_campaign_subject]',
                        'description'     => __('Enter a subject for the email.', 'mailoptin'),
                        'sub_description' => apply_filters('mailoptin_customizer_settings_email_campaign_subject_description', '', $campaign_type),
                        'priority'        => 20
                    ]
                )
            ),
            'item_number'               => new WP_Customize_Range_Value_Control(
                $this->wp_customize,
                $this->option_prefix . '[item_number]',
                apply_filters('mailoptin_customizer_settings_campaign_item_number_args', [
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[item_number]',
                        'label'       => __('Maximum Number of Posts', 'mailoptin'),
                        'input_attrs' => [
                            'min'  => 1,
                            'max'  => 1000,
                            'step' => 1
                        ],
                        'priority'    => 25
                    ]
                )
            ),
            'post_content_type'         => [
                'label'    => __('Content Type', 'mailoptin'),
                'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                'settings' => $this->option_prefix . '[post_content_type]',
                'type'     => 'select',
                'choices'  => [
                    'post_content' => __('Post Content', 'mailoptin'),
                    'post_excerpt' => __('Post Excerpt', 'mailoptin')
                ],
                'priority' => 28
            ],
            'post_content_length'       => apply_filters('mailoptin_customizer_settings_campaign_post_content_length_args',
                [
                    'type'        => 'number',
                    'input_attrs' => [
                        'min' => 1,
                    ],
                    'label'       => __('Content Length', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings'    => $this->option_prefix . '[post_content_length]',
                    'description' => __('Number of words to limit the post content to. Set to "0" for full post content.', 'mailoptin'),
                    'priority'    => 30
                ]
            ),
            'custom_post_type'          => apply_filters('mo_optin_form_customizer_custom_post_type_args',
                [
                    'type'        => 'select',
                    'label'       => __('Select Post Type', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings'    => $this->option_prefix . '[custom_post_type]',
                    'choices'     => ['post' => __('WordPress Posts', 'mailoptin')] + $custom_post_type_options,
                    'description' => __('By default, automation works with WordPress posts. To make it work with a custom post type instead? Select one.', 'mailoptin'),
                    'priority'    => 32
                ]
            ),
            'custom_post_type_settings' => new WP_Customize_EA_CPT_Control(
                $this->wp_customize,
                $this->option_prefix . '[custom_post_type_settings]',
                apply_filters('mo_optin_form_customizer_custom_post_type_settings_args', [
                        'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[custom_post_type_settings]',
                        'priority' => 33
                    ]
                )
            ),
            'post_categories'           => new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_categories]',
                apply_filters('mo_optin_form_customizer_post_categories_args', [
                        'label'       => __('Restrict to Post Categories', 'mailoptin'),
                        'description' => __('Only include posts that has either of the selected categories.', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[post_categories]',
                        'choices'     => ControlsHelpers::get_categories(),
                        'priority'    => 45
                    ]
                )
            ),
            'post_tags'                 => new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_tags]',
                apply_filters('mo_optin_form_customizer_post_tags_args', [
                        'label'       => __('Restrict to Post Tags', 'mailoptin'),
                        'description' => __('Only include posts that has either of the selected tags.', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[post_tags]',
                        'choices'     => ControlsHelpers::get_tags(),
                        'priority'    => 46
                    ]
                )
            ),
            'post_authors'              => new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_authors]',
                apply_filters('mo_optin_form_customizer_post_tags_args', [
                        'label'       => __('Restrict to Post Authors', 'mailoptin'),
                        'description' => __('Only include posts that are published by selected authors.', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[post_authors]',
                        'choices'     => ControlsHelpers::get_authors(),
                        'priority'    => 47
                    ]
                )
            ),
            'recipient_header'          => new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[recipient_header]',
                apply_filters('mo_optin_form_customizer_recipient_header_args', [
                        'content'     => '<div class="mo-field-header">' . __("Recipient", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[recipient_header]',
                        'priority'    => 49,
                    ]
                )
            ),
            'connection_service'        => apply_filters('mailoptin_customizer_settings_campaign_connection_service_args',
                [
                    'type'        => 'select',
                    'label'       => __('Select Connection', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings'    => $this->option_prefix . '[connection_service]',
                    'choices'     => ConnectionsRepository::get_connections(ConnectionsRepository::EMAIL_CAMPAIGN_TYPE),
                    'description' => __('Choose the email service or connection that newsletter will be sent to.', 'mailoptin'),
                    'priority'    => 50
                ]
            ),
            'connection_email_list'     => apply_filters('mailoptin_customizer_settings_campaign_connection_email_list_args',
                [
                    'type'        => 'select',
                    'label'       => __('Select Email List', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings'    => $this->option_prefix . '[connection_email_list]',
                    'choices'     => $connection_email_list,
                    'description' => __('Email list that newsletter will be sent to.', 'mailoptin'),
                    'priority'    => 60
                ]
            ),
            'schedule_header'           => new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[schedule_header]',
                apply_filters('mo_optin_form_customizer_schedule_header_args', [
                        'content'     => '<div class="mo-field-header">' . __("Schedule", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[schedule_header]',
                        'priority'    => 200,
                    ]
                )
            ),
            'send_immediately'          => new WP_Customize_Toggle_Control(
                $this->wp_customize,
                $this->option_prefix . '[send_immediately]',
                apply_filters('mailoptin_customizer_settings_campaign_send_immediately_args', [
                        'label'       => __('Send Immediately', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings'    => $this->option_prefix . '[send_immediately]',
                        'description' => __('Enable to send newsletter immediately after a post is published.', 'mailoptin'),
                        'priority'    => 300,
                    ]
                )
            ),
            'email_campaign_schedule'   => new WP_Customize_Email_Schedule_Time_Fields_Control(
                $this->wp_customize,
                $this->option_prefix . '[email_campaign_schedule]',
                apply_filters('mailoptin_customizer_settings_campaign_schedule_args', [
                        'label'    => __('Send Email', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => [
                            'schedule_digit' => $this->option_prefix . '[schedule_digit]',
                            'schedule_type'  => $this->option_prefix . '[schedule_type]'
                        ],
                        'priority' => 310
                    ]
                )
            ),
            'email_digest_schedule'     => new WP_Customize_Email_Schedule_Time_Fields_Control(
                $this->wp_customize,
                $this->option_prefix . '[email_digest_schedule]',
                apply_filters('mailoptin_customizer_settings_email_digest_schedule_args', [
                        'label'    => __('When should we send?', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => [
                            'schedule_interval'   => $this->option_prefix . '[schedule_interval]',
                            'schedule_time'       => $this->option_prefix . '[schedule_time]',
                            'schedule_day'        => $this->option_prefix . '[schedule_day]',
                            'schedule_month_date' => $this->option_prefix . '[schedule_month_date]'
                        ],
                        'format'   => ER::POSTS_EMAIL_DIGEST,
                        'priority' => 310
                    ]
                )
            ),
            'ajax_nonce'                => apply_filters('mailoptin_customizer_settings_campaign_ajax_nonce_args', [
                    'type'     => 'hidden',
                    // simple hack because control won't render if label is empty.
                    'label'    => '&nbsp;',
                    'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings' => $this->option_prefix . '[ajax_nonce]',
                    // 999 cos we want it to be bottom.
                    'priority' => 999,
                ]
            )
        ];

        if (apply_filters('mailoptin_enable_email_automation_cpt', false)) {
            unset($campaign_settings_controls['custom_post_type']);
            unset($campaign_settings_controls['custom_post_type_settings']);
        }

        $email_campaign_type = ER::get_email_campaign_type($this->customizerClassInstance->email_campaign_id);

        if ($email_campaign_type !== ER::NEW_PUBLISH_POST) {
            unset($campaign_settings_controls['send_immediately']);
            unset($campaign_settings_controls['email_campaign_schedule']);
        }

        if ($email_campaign_type != ER::POSTS_EMAIL_DIGEST) {
            unset($campaign_settings_controls['item_number']);
            unset($campaign_settings_controls['email_digest_schedule']);
        }

        if ( ! apply_filters('mailoptin_enable_email_automation_post_category_support', false)) {
            unset($campaign_settings_controls['post_categories']);
        }

        if ( ! apply_filters('mailoptin_enable_email_automation_cpt_support', false) && ! ER::is_newsletter($this->customizerClassInstance->email_campaign_id)) {
            unset($campaign_settings_controls['post_tags']);
            unset($campaign_settings_controls['post_authors']);
            $content = sprintf(
                __('Upgrade to %sMailOptin Pro%s to support custom post types and restrict by post categories, tags, authors and custom taxonomies.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=new_post_campaign_settings">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $campaign_settings_controls['email_campaign_settings_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[email_campaign_settings_notice]',
                apply_filters('mo_optin_form_customizer_email_campaign_settings_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[email_campaign_settings_notice]',
                        'priority' => 45,
                    )
                )
            );
        }

        if ( ! apply_filters('mailoptin_enable_email_customizer_connections', false) && ! ER::is_newsletter($this->customizerClassInstance->email_campaign_id)) {

            $content2 = sprintf(
                __('%sUpgrade your MailOptin plan%s to send email campaigns directly to your list in Mailchimp, Campaign Monitor, AWeber, Constant Contact, Drip, MailerLite, ActiveCampaign etc.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=new_post_campaign_settings2">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $campaign_settings_controls['email_campaign_settings_notice2'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[email_campaign_settings_notice2]',
                apply_filters('mo_optin_form_customizer_email_campaign_settings_notice2_args', array(
                        'content'  => $content2,
                        'section'  => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[email_campaign_settings_notice2]',
                        'priority' => 64,
                    )
                )
            );
        }

        if (ER::is_code_your_own_template($this->customizerClassInstance->email_campaign_id)) {
            unset($campaign_settings_controls['post_content_type']);
        }

        if (ER::is_newsletter($this->customizerClassInstance->email_campaign_id)) {
            unset($campaign_settings_controls['email_campaign_subject']);
            unset($campaign_settings_controls['post_content_type']);
            unset($campaign_settings_controls['post_content_length']);
            unset($campaign_settings_controls['custom_post_type']);
            unset($campaign_settings_controls['custom_post_type_settings']);
            unset($campaign_settings_controls['post_categories']);
            unset($campaign_settings_controls['post_tags']);
            unset($campaign_settings_controls['post_authors']);
            unset($campaign_settings_controls['schedule_header']);
            $campaign_settings_controls['email_campaign_title']['label'] = __('Email Subject', 'mailoptin');
        }

        $email_campaign_settings_control_args = apply_filters(
            "mailoptin_email_campaign_customizer_settings_controls",
            $campaign_settings_controls,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_email_campaign_settings_controls',
            $email_campaign_settings_control_args,
            $campaign_type,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($email_campaign_settings_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_email_campaign_settings_controls',
            $email_campaign_settings_control_args,
            $campaign_type,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );
    }

    public function available_tags_control()
    {
        $email_digest_code_example = <<<HTML
[posts-loop]

    <h2>[post-title]</h2>
    
    [post-feature-image]
    
    [post-content]

[/posts-loop]
HTML;

        $control_args = apply_filters(
            "mailoptin_template_customizer_available_tags_control",
            array(
                'email_digest_tag_help'            => new WP_Customize_Custom_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[email_digest_tag_help]',
                    array(
                        'content'        => sprintf(
                            '%1$sEmail digest requires tags must be wrapped between %3$s[posts-loop] .. [/posts-loop]%4$s like so: %5$s%2$s',
                            '<div class="mo-email-digest-tag-help">',
                            '</div>',
                            '<strong>',
                            '</strong>',
                            '<div class="mo-email-digest-tag-help-code">' . nl2br(esc_html($email_digest_code_example)) . '</div>'
                        ),
                        'section'        => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'settings'       => $this->option_prefix . '[email_digest_tag_help]',
                        'no_wrapper_div' => true,
                        'priority'       => 5
                    )
                ),
                'post_tags_header'                 => new WP_Customize_Custom_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_tags_header]',
                    array(
                        'content'     => '<div class="mo-field-header">' . __("Post Tags", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'settings'    => $this->option_prefix . '[post_tags_header]',
                        'priority'    => 10
                    )
                ),
                'post_title_shortcode'             => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_title_shortcode]',
                    array(
                        'label'    => __('Post Title', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-title]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_title_shortcode]',
                        'priority' => 20
                    )
                ),
                'post_content_shortcode'           => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_content_shortcode]',
                    array(
                        'label'    => __('Post Content', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-content]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_content_shortcode]',
                        'priority' => 30
                    )
                ),
                'post_excerpt_shortcode'           => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_excerpt_shortcode]',
                    array(
                        'label'    => __('Post Excerpt', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-excerpt]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_excerpt_shortcode]',
                        'priority' => 40
                    )
                ),
                'post_feature_image_shortcode'     => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_feature_image_shortcode]',
                    array(
                        'label'       => __('Feature Image', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-feature-image]" style="background-color:#fff;" readonly>',
                        'description' => __('HTML image of post\'s featured image.', 'mailoptin'),
                        'settings'    => $this->option_prefix . '[post_feature_image_shortcode]',
                        'priority'    => 50
                    )
                ),
                'post_feature_image_url_shortcode' => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_feature_image_url_shortcode]',
                    array(
                        'label'       => __('Feature Image URL', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-feature-image-url]" style="background-color:#fff;" readonly>',
                        'description' => sprintf(
                            esc_html__('URL of post\'s featured image. You can specify a default image if a post doesn\'t have feature image like so %s', 'mailoptin'),
                            '<strong>[post-feature-image-url default="https://site.com/image.png"]</strong>'),
                        'settings'    => $this->option_prefix . '[post_feature_image_url_shortcode]',
                        'priority'    => 55
                    )
                ),
                'post_url_shortcode'               => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_url_shortcode]',
                    array(
                        'label'    => __('Post URL', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-url]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_url_shortcode]',
                        'priority' => 60
                    )
                ),
                'post_categories_shortcode'        => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_categories_shortcode]',
                    array(
                        'label'       => __('Post Categories', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-categories link=' . esc_attr('"true"') . ']" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[post_categories_shortcode]',
                        'description' => __('Comma-separated list of post categories. Set "link" attribute to false to remove the link.', 'mailoptin'),
                        'priority'    => 70
                    )
                ),
                'post_terms_shortcode'             => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_terms_shortcode]',
                    array(
                        'label'       => __('Post Taxonomy Terms', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-terms tax=' . esc_attr('"taxonomy_name"') . ' link=' . esc_attr('"true"') . ']" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[post_terms_shortcode]',
                        'description' => __('Comma-separated list of post terms of a taxonomy. Set "tax" attribute to the taxonomy name. Set "link" attribute to false to remove the link.', 'mailoptin'),
                        'priority'    => 75
                    )
                ),
                'post_date_shortcode'              => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_date_shortcode]',
                    array(
                        'label'       => __('Post Date', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-date]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[post_date_shortcode]',
                        'description' => sprintf(
                            esc_html__('Publish date of the post in your local time set in WordPress. You can customize or format the date like so %s[post-date format="F j, Y, g:i a"]%s', 'mailoptin'),
                            '<strong>', '</strong>'
                        ),
                        'priority'    => 80
                    )
                ),
                'post_date_gmt_shortcode'          => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_date_gmt_shortcode]',
                    array(
                        'label'       => __('Post Date in GMT', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-date-gmt]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[post_date_shortcode]',
                        'description' => sprintf(
                            esc_html__('Publish date of the post in GMT/UTC. You can customize or format the date like so %s[post-date format="F j, Y, g:i a"]%s', 'mailoptin'),
                            '<strong>', '</strong>'
                        ),
                        'priority'    => 90
                    )
                ),
                'post_meta_shortcode'              => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_meta_shortcode]',
                    array(
                        'label'       => __('Post Meta Value', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-meta key=' . esc_attr('"meta_key"') . ']" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[post_meta_shortcode]',
                        'description' => __('Post meta value of a certain "meta_key".', 'mailoptin'),
                        'priority'    => 95
                    )
                ),
                'post_id_shortcode'                => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_id_shortcode]',
                    array(
                        'label'    => __('Post ID', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-id]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_id_shortcode]',
                        'priority' => 100
                    )
                ),
                'post_author_name_shortcode'       => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_author_name_shortcode]',
                    array(
                        'label'       => __('Author Name', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[post-author-name link=' . esc_attr('"true"') . ']" style="background-color:#fff;" readonly>',
                        'description' => __('Set "link" attribute to false to remove the link to author\'s website.', 'mailoptin'),
                        'settings'    => $this->option_prefix . '[post_author_name_shortcode]',
                        'priority'    => 110
                    )
                ),
                'post_author_website_shortcode'    => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_author_website_shortcode]',
                    array(
                        'label'    => __('Author Website', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-author-website]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_author_website_shortcode]',
                        'priority' => 120
                    )
                ),
                'post_author_email_shortcode'      => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[post_author_email_shortcode]',
                    array(
                        'label'    => __('Author Email Address', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'  => '<input type="text" value="[post-author-email]" style="background-color:#fff;" readonly>',
                        'settings' => $this->option_prefix . '[post_author_email_shortcode]',
                        'priority' => 130
                    )
                ),
                'campaign_tags_header'             => new WP_Customize_Custom_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[campaign_tags_header]',
                    array(
                        'content'     => '<div class="mo-field-header">' . __("Campaign Tags", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'settings'    => $this->option_prefix . '[campaign_tags_header]',
                        'priority'    => 140
                    )
                ),
                'unsubscribe_shortcode'            => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[unsubscribe_shortcode]',
                    array(
                        'label'       => __('Unsubscribe URL', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[unsubscribe]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[unsubscribe_shortcode]',
                        'description' => __('URL to unsubscribe. This must be in your email template.', 'mailoptin'),
                        'priority'    => 150
                    )
                ),
                'web_version_shortcode'            => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[web_version_shortcode]',
                    array(
                        'label'       => __('Web Version URL', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[webversion]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[web_version_shortcode]',
                        'description' => __('URL to the web version.', 'mailoptin'),
                        'priority'    => 160
                    )
                ),
                'company_name_shortcode'           => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_name_shortcode]',
                    array(
                        'label'       => __('Company Name', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-name]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_name_shortcode]',
                        'description' => sprintf(
                            __('Your company name as defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 170
                    )
                ),
                'company_address_shortcode'        => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_address_shortcode]',
                    array(
                        'label'       => __('Company Address', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-address]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_address_shortcode]',
                        'description' => sprintf(
                            __('Your company address as defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 180
                    )
                ),
                'company_address_2_shortcode'      => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_address_2_shortcode]',
                    array(
                        'label'       => __('Company Address 2', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-address2]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_address_2_shortcode]',
                        'description' => sprintf(
                            __('Company address 2 defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 180
                    )
                ),
                'company_city_shortcode'           => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_city_shortcode]',
                    array(
                        'label'       => __('Company City', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-city]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_city_shortcode]',
                        'description' => sprintf(
                            __('Your company city as defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 190
                    )
                ),
                'company_state_shortcode'          => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_state_shortcode]',
                    array(
                        'label'       => __('Company State', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-state]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_state_shortcode]',
                        'description' => sprintf(
                            __('Your company state as defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 200
                    )
                ),
                'company_zip_shortcode'            => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_zip_shortcode]',
                    array(
                        'label'       => __('Company Zip Code', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-zip]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_zip_shortcode]',
                        'description' => sprintf(
                            __('Zip or postal code as defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 210
                    )
                ),
                'company_country_shortcode'        => new WP_Customize_View_Tags_Shortcode_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[company_country_shortcode]',
                    array(
                        'label'       => __('Company Country', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_view_tags_section_id,
                        'content'     => '<input type="text" value="[company-country]" style="background-color:#fff;" readonly>',
                        'settings'    => $this->option_prefix . '[company_country_shortcode]',
                        'description' => sprintf(
                            __('Your company country defined in <a target="_blank" href="%s">settings</a>', 'mailoptin'),
                            MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#email_campaign_settings'
                        ),
                        'priority'    => 220
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ($this->customizerClassInstance->email_campaign_type != ER::POSTS_EMAIL_DIGEST) {
            unset($control_args['email_digest_tag_help']);
        }

        if (ER::is_newsletter($this->customizerClassInstance->email_campaign_id) &&
            ER::is_code_your_own_template($this->customizerClassInstance->email_campaign_id)) {
            foreach ($control_args as $id => $args) {
                if ( ! in_array($id,
                    [
                        'company_country_shortcode',
                        'company_zip_shortcode',
                        'company_state_shortcode',
                        'company_city_shortcode',
                        'company_address_2_shortcode',
                        'company_address_shortcode',
                        'company_name_shortcode',
                        'web_version_shortcode',
                        'unsubscribe_shortcode',
                        'campaign_tags_header'
                    ])) {
                    unset($control_args[$id]);
                }
            }
        }

        foreach ($control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }
    }

    public function preview_control()
    {
        $choices     = ControlsHelpers::get_post_type_posts('post');
        $search_type = 'posts_never_load';
        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $choices     = ControlsHelpers::get_all_post_types_posts();
            $search_type = 'exclusive_post_types_posts_load';
        }

        $choices = ['' => __('Select...', 'mailoptin')] + $choices;

        $control_args = apply_filters(
            "mailoptin_template_customizer_preview_control",
            array(
                'post_as_preview' => new WP_Customize_Chosen_Select_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[post_as_preview]',
                    apply_filters('mo_optin_form_customizer_post_as_preview_args', array(
                            'label'       => __('Preview Post', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->campaign_preview_section_id,
                            'settings'    => $this->option_prefix . '[post_as_preview]',
                            'description' => __('Select a post to use as preview', 'mailoptin'),
                            'search_type' => $search_type,
                            'choices'     => $choices,
                            'is_multiple' => false,
                            'priority'    => 10
                        )
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ($this->customizerClassInstance->email_campaign_type != ER::NEW_PUBLISH_POST) {
            unset($control_args['post_as_preview']);
        }

        foreach ($control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }
    }

    public function page_controls()
    {
        $page_control_args = apply_filters(
            "mailoptin_template_customizer_page_controls",
            array(
                'page_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[page_background_color]',
                    apply_filters('mailoptin_template_customizer_background_color_args', array(
                            'label'    => __('Background Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_page_section_id,
                            'settings' => $this->option_prefix . '[page_background_color]',
                            'priority' => 10
                        )
                    )
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM') && ! ER::is_newsletter($this->customizerClassInstance->email_campaign_id)) {
            $content = sprintf(
                __('Upgrade to %sMailOptin Premium%s to access the Custom CSS feature that will allow you customize this template to your heart content.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=email_automation_custom_css_upgrade">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $page_control_args['custom_css_upgrade_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[custom_css_upgrade_notice]',
                apply_filters('mo_optin_form_customizer_custom_css_upgrade_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->campaign_page_section_id,
                        'settings' => $this->option_prefix . '[custom_css_upgrade_notice]',
                        'priority' => 20,
                    )
                )
            );
        }

        if (ER::is_newsletter($this->customizerClassInstance->email_campaign_id)) {
            $page_control_args['content_background_color'] = new \WP_Customize_Color_Control(
                $this->wp_customize,
                $this->option_prefix . '[content_background_color]',
                apply_filters('mailoptin_template_customizer_content_background_color_args', array(
                        'label'    => __('Content Background Color', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_page_section_id,
                        'settings' => $this->option_prefix . '[content_background_color]',
                        'priority' => 15
                    )
                )
            );

            $page_control_args['content_text_color'] = new \WP_Customize_Color_Control(
                $this->wp_customize,
                $this->option_prefix . '[content_text_color]',
                apply_filters('mailoptin_template_customizer_content_text_color_args', array(
                        'label'    => __('Content Text Color', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_page_section_id,
                        'settings' => $this->option_prefix . '[content_text_color]',
                        'priority' => 18
                    )
                )
            );
        }

        do_action('mailoptin_before_page_controls_addition',
            $page_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($page_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_page_controls_addition',
            $page_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );
    }

    public function header_controls()
    {
        $header_control_args = apply_filters(
            "mailoptin_template_customizer_header_controls",
            array(
                'header_controls_tab_toggle'    => new WP_Customize_Controls_Tab_Toggle(
                    $this->wp_customize,
                    $this->option_prefix . '[header_controls_tab_toggle]',
                    apply_filters('mailoptin_template_customizer_header_controls_tab_toggle_args', array(
                            'section'  => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_controls_tab_toggle]',
                            'priority' => 2
                        )
                    )
                ),
                'header_removal'                => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_removal]',
                    apply_filters('mailoptin_template_customizer_header_removal_args', array(
                            'label'    => esc_html__('Remove Header', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_removal]',
                            'type'     => 'light',// light, ios, flat
                            'priority' => 10
                        )
                    )
                ),
                'header_logo'                   => new \WP_Customize_Cropped_Image_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_logo]',
                    apply_filters('mailoptin_template_customizer_header_logo_args', array(
                            'label'         => __('Logo', 'mailoptin'),
                            'section'       => $this->customizerClassInstance->campaign_header_section_id,
                            'settings'      => $this->option_prefix . '[header_logo]',
                            'flex_width'    => true,
                            'flex_height'   => true,
                            'button_labels' => array(
                                'select'       => __('Select Logo', 'mailoptin'),
                                'change'       => __('Change Logo', 'mailoptin'),
                                'default'      => __('Default', 'mailoptin'),
                                'remove'       => __('Remove', 'mailoptin'),
                                'placeholder'  => __('No logo selected', 'mailoptin'),
                                'frame_title'  => __('Select Logo', 'mailoptin'),
                                'frame_button' => __('Choose Logo', 'mailoptin'),
                            ),
                            'priority'      => 20
                        )
                    )
                ),
                'header_background_color'       => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_background_color]',
                    apply_filters('mailoptin_template_customizer_header_background_color_args', array(
                            'label'    => __('Background Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_background_color]',
                            'priority' => 30
                        )
                    )
                ),
                'header_text_color'             => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_text_color]',
                    apply_filters('mailoptin_template_customizer_header_text_color_args', array(
                            'label'    => __('Text Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_text_color]',
                            'priority' => 40
                        )
                    )
                ),
                'header_text'                   => apply_filters('mailoptin_template_customizer_header_text_args',
                    array(
                        'label'       => __('Header Text', 'mailoptin'),
                        'description' => __('This is used when template logo is not set.', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_header_section_id,
                        'type'        => 'text',
                        'settings'    => $this->option_prefix . '[header_text]',
                        'priority'    => 50
                    )
                ),
                'header_web_version_link_label' => apply_filters('mailoptin_template_customizer_header_web_version_link_label_args',
                    array(
                        'label'    => __('Web Version Link Label', 'mailoptin'),
                        'type'     => 'text',
                        'section'  => $this->customizerClassInstance->campaign_header_section_id,
                        'settings' => $this->option_prefix . '[header_web_version_link_label]',
                        'priority' => 60
                    )
                ),
                'header_web_version_link_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_web_version_link_color]',
                    apply_filters('mailoptin_template_customizer_header_web_version_link_color_args', array(
                            'label'    => __('Web Version Link Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_web_version_link_color]',
                            'priority' => 70
                        )
                    )
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_header_controls_addition',
            $header_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($header_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_header_controls_addition',
            $header_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

    }


    public function content_controls()
    {
        $content_control_args = apply_filters(
            "mailoptin_template_customizer_content_controls",
            array(
                'content_controls_tab_toggle'              => new WP_Customize_Controls_Tab_Toggle(
                    $this->wp_customize,
                    $this->option_prefix . '[content_controls_tab_toggle]',
                    apply_filters('mailoptin_template_customizer_header_content_controls_tab_toggle_args', array(
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_controls_tab_toggle]',
                            'priority' => 2
                        )
                    )
                ),
                'content_before_main_content'              => new WP_Customize_Tinymce_Expanded_Editor(
                    $this->wp_customize,
                    $this->option_prefix . '[content_before_main_content]',
                    apply_filters('mailoptin_template_customizer_content_before_main_content_args', array(
                            'label'    => __('Before Main Content', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_before_main_content]',
                            'priority' => 8
                        )
                    )
                ),
                'content_after_main_content'               => new WP_Customize_Tinymce_Expanded_Editor(
                    $this->wp_customize,
                    $this->option_prefix . '[content_after_main_content]',
                    apply_filters('mailoptin_template_customizer_content_after_main_content_args', array(
                            'label'    => __('After Main Content', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_after_main_content]',
                            'priority' => 9
                        )
                    )
                ),
                'content_background_color'                 => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_background_color]',
                    apply_filters('mailoptin_template_customizer_content_background_color_args', array(
                            'label'    => __('Background Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_background_color]',
                            'priority' => 10
                        )
                    )
                ),
                'content_headline_color'                   => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_headline_color]',
                    apply_filters('mailoptin_template_customizer_content_headline_color_args', array(
                            'label'    => __('Post Title Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_headline_color]',
                            'priority' => 15
                        )
                    )
                ),
                'content_text_color'                       => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_text_color]',
                    apply_filters('mailoptin_template_customizer_content_text_color_args', array(
                            'label'    => __('Text Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_text_color]',
                            'priority' => 20
                        )
                    )
                ),
                'content_post_meta'                        => new WP_Customize_Multiple_Checkbox(
                    $this->wp_customize,
                    $this->option_prefix . '[content_post_meta]',
                    apply_filters('mailoptin_template_customizer_content_post_meta_args', array(
                            'label'    => esc_html__('Post Meta Data', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_post_meta]',
                            'choices'  => array(
                                'author'   => __('Author', 'mailoptin'),
                                'category' => __('Categories', 'mailoptin'),
                                'date'     => __('Date', 'mailoptin'),
                            ),
                            'priority' => 23
                        )
                    )
                ),
                'content_remove_post_link'                 => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_remove_post_link]',
                    array(
                        'label'    => esc_html__('Remove Title & Image Link to Post', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[content_remove_post_link]',
                        'priority' => 24
                    )
                ),
                'content_remove_post_body'                 => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_remove_post_body]',
                    apply_filters('mailoptin_template_customizer_content_remove_post_body_args', array(
                            'label'    => esc_html__('Remove Post Content', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_remove_post_body]',
                            'priority' => 25
                        )
                    )
                ),
                'content_remove_feature_image'             => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_remove_feature_image]',
                    apply_filters('mailoptin_template_customizer_content_remove_feature_image_args', array(
                            'label'    => esc_html__('Remove Featured Image', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_remove_feature_image]',
                            'priority' => 30
                        )
                    )
                ),
                'default_image_url'                        => apply_filters('mailoptin_customizer_settings_campaign_default_image_url_args',
                    array(
                        'type'        => 'text',
                        'label'       => __('Fallback Featured Image', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->campaign_content_section_id,
                        'settings'    => $this->option_prefix . '[default_image_url]',
                        'description' => __('Enter URL of an image to use when a post lacks a feature image.', 'mailoptin'),
                        'priority'    => 40
                    )
                ),
                'content_title_font_size'                  => new WP_Customize_Range_Value_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_title_font_size]',
                    apply_filters('mailoptin_template_customizer_content_title_font_size_args', array(
                            'label'       => __('Title Font Size', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->campaign_content_section_id,
                            'settings'    => $this->option_prefix . '[content_title_font_size]',
                            'input_attrs' => array(
                                'min'    => 10,
                                'max'    => 50,
                                'step'   => 1,
                                'suffix' => 'px', //optional suffix
                            ),
                            'priority'    => 60
                        )
                    )
                ),
                'content_body_font_size'                   => new WP_Customize_Range_Value_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_body_font_size]',
                    apply_filters('mailoptin_template_customizer_content_body_font_size_args', array(
                            'label'       => __('Body Font Size', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->campaign_content_section_id,
                            'settings'    => $this->option_prefix . '[content_body_font_size]',
                            'input_attrs' => array(
                                'min'    => 10,
                                'max'    => 50,
                                'step'   => 1,
                                'suffix' => 'px'
                            ),
                            'priority'    => 80
                        )
                    )
                ),
                'content_alignment'                        => array(
                    'label'    => __('Content Alignment', 'mailoptin'),
                    'section'  => $this->customizerClassInstance->campaign_content_section_id,
                    'settings' => $this->option_prefix . '[content_alignment]',
                    'type'     => 'select',
                    'choices'  => array(
                        'left'   => __('Left', 'mailoptin'),
                        'center' => __('Center', 'mailoptin'),
                        'right'  => __('Right', 'mailoptin'),
                    ),
                    'priority' => 100
                ),
                'content_remove_ellipsis_button'           => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_remove_ellipsis_button]',
                    apply_filters('mailoptin_template_customizer_content_remove_ellipsis_button_args', array(
                            'label'    => esc_html__('Remove Read More Button', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_remove_ellipsis_button]',
                            'type'     => 'light',// light, ios, flat
                            'priority' => 120
                        )
                    )
                ),
                'content_ellipsis_button_alignment'        => array(
                    'label'    => __('Read More Button Alignment', 'mailoptin'),
                    'section'  => $this->customizerClassInstance->campaign_content_section_id,
                    'settings' => $this->option_prefix . '[content_ellipsis_button_alignment]',
                    'type'     => 'select',
                    'choices'  => array(
                        'left'   => __('Left', 'mailoptin'),
                        'center' => __('Center', 'mailoptin'),
                        'right'  => __('Right', 'mailoptin'),
                    ),
                    'priority' => 140
                ),
                'content_ellipsis_button_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_ellipsis_button_background_color]',
                    array(
                        'label'    => __('Read More Button Background Color', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[content_ellipsis_button_background_color]',
                        'priority' => 160
                    )
                ),
                'content_ellipsis_button_text_color'       => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_ellipsis_button_text_color]',
                    array(
                        'label'    => __('Read More Button Text Color', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[content_ellipsis_button_text_color]',
                        'priority' => 180
                    )
                ),
                'content_ellipsis_button_label'            => array(
                    'label'    => __('Read More Button Label', 'mailoptin'),
                    'type'     => 'text',
                    'section'  => $this->customizerClassInstance->campaign_content_section_id,
                    'settings' => $this->option_prefix . '[content_ellipsis_button_label]',
                    'priority' => 200
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if (ER::is_newsletter($this->customizerClassInstance->email_campaign_id)) {
            unset($content_control_args['content_background_color']);
            unset($content_control_args['content_text_color']);
            unset($content_control_args['content_headline_color']);
        }

        do_action('mailoptin_before_content_controls_addition',
            $content_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($content_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_content_controls_addition',
            $content_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

    }

    public function newsletter_content_control()
    {
        $controls = apply_filters(
            "mailoptin_template_newsletter_content_controls",
            array(
                'email_newsletter_content' => new EmailContentBuilder\Customizer_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[email_newsletter_content]',
                    array(
                        'section'  => $this->customizerClassInstance->newsletter_content_section_id,
                        'settings' => $this->option_prefix . '[email_newsletter_content]',
                        'priority' => 10
                    )
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($controls as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }
    }

    public function footer_controls()
    {
        $footer_control_args = apply_filters(
            "mailoptin_template_customizer_footer_controls",
            array(
                'footer_controls_tab_toggle'    => new WP_Customize_Controls_Tab_Toggle(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_controls_tab_toggle]',
                    apply_filters('mailoptin_template_customizer_footer_controls_tab_toggle_args', array(
                            'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_controls_tab_toggle]',
                            'priority' => 2
                        )
                    )
                ),
                'footer_removal'                => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_removal]',
                    apply_filters('mailoptin_template_customizer_footer_removal_args', array(
                            'label'    => esc_html__('Remove Footer', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_removal]',
                            'type'     => 'light',// light, ios, flat
                            'priority' => 10
                        )
                    )
                ),
                'footer_background_color'       => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_background_color]',
                    apply_filters('mailoptin_template_customizer_footer_background_color_args', array(
                            'label'    => __('Background Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_background_color]',
                            'priority' => 20
                        )
                    )
                ),
                'footer_text_color'             => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_text_color]',
                    apply_filters('mailoptin_template_customizer_footer_text_color_args', array(
                            'label'    => __('Text Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_text_color]',
                            'priority' => 30
                        )
                    )
                ),
                'footer_font_size'              => new WP_Customize_Range_Value_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_font_size]',
                    apply_filters('mailoptin_template_customizer_footer_font_size_args', array(
                            'label'       => __('Footer Font Size', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings'    => $this->option_prefix . '[footer_font_size]',
                            'input_attrs' => array(
                                'min'    => 10,
                                'max'    => 40,
                                'step'   => 1,
                                'suffix' => 'px'
                            ),
                            'priority'    => 40
                        )
                    )
                ),
                'footer_copyright_line'         => apply_filters('mailoptin_template_customizer_footer_copyright_line_args',
                    array(
                        'label'    => __('Copyright Line', 'mailoptin'),
                        'type'     => 'text',
                        'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_copyright_line]',
                        'priority' => 50
                    )
                ),
                'footer_description'            => apply_filters('mailoptin_template_customizer_footer_description_args',
                    array(
                        'label'    => __('Mailing Address', 'mailoptin'),
                        'type'     => 'textarea',
                        'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_description]',
                        'priority' => 60
                    )
                ),
                'footer_unsubscribe_line'       => apply_filters('mailoptin_template_customizer_footer_unsubscribe_line_args',
                    array(
                        'label'    => __('Unsubscribe Line', 'mailoptin'),
                        'type'     => 'text',
                        'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_unsubscribe_line]',
                        'priority' => 70
                    )
                ),
                'footer_unsubscribe_link_label' => apply_filters('mailoptin_template_customizer_footer_unsubscribe_link_color_args',
                    array(
                        'label'    => __('Unsubscribe Link Label', 'mailoptin'),
                        'type'     => 'text',
                        'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_unsubscribe_link_label]',
                        'priority' => 80
                    )
                ),
                'footer_unsubscribe_link_color' => apply_filters('mailoptin_template_customizer_footer_unsubscribe_link_color_args',
                    new \WP_Customize_Color_Control(
                        $this->wp_customize,
                        $this->option_prefix . '[footer_unsubscribe_link_color]',
                        array(
                            'label'    => __('Unsubscribe Link Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_unsubscribe_link_color]',
                            'priority' => 90
                        )
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            unset($footer_control_args['footer_removal']);
        }

        do_action('mailoptin_before_footer_controls_addition',
            $footer_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($footer_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_footer_controls_addition',
            $footer_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

    }
}