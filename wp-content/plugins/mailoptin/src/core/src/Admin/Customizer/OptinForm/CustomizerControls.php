<?php

namespace MailOptin\Core\Admin\Customizer\OptinForm;

use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Ace_Editor_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Fields_Repeater_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Font_Size_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Google_Font_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Select_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Integration_Repeater_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Range_Value_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Tinymce_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\OptinForms\AbstractOptinForm;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class CustomizerControls
{
    /** @var \WP_Customize_Manager */
    private $wp_customize;

    /** @var Customizer */
    private $customizerClassInstance;

    /** @var string DB option name prefix */
    private $option_prefix;

    /** @var string DB option name prefix */
    private $optin_class_instance;

    /** @var string default image URL for form_image partial */
    private $default_form_image;

    /** @var string default image URL for form_background_image partial */
    private $default_form_background_image;

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     * @param null|AbstractOptinForm $optin_class_instance
     */
    public function __construct($wp_customize, $option_prefix, $customizerClassInstance, $optin_class_instance = null)
    {
        $this->wp_customize            = $wp_customize;
        $this->customizerClassInstance = $customizerClassInstance;
        $this->option_prefix           = $option_prefix;
        $this->optin_class_instance    = $optin_class_instance;

        $this->optin_campaign_id = $customizerClassInstance->optin_campaign_id;
    }

    public function design_controls()
    {
        $page_control_args = apply_filters(
            "mo_optin_form_customizer_design_controls",
            [],
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ($this->customizerClassInstance->optin_campaign_type != 'bar') {
            $form_width_input_attrs = [
                'min'    => 100,
                'max'    => 2000,
                'step'   => 10,
                'suffix' => 'px'
            ];

            if ($this->customizerClassInstance->optin_campaign_type == 'sidebar') {
                $form_width_input_attrs = [
                    'min'    => 100,
                    'max'    => 1000,
                    'step'   => 5,
                    'suffix' => 'px'
                ];
            }

            if (in_array($this->customizerClassInstance->optin_campaign_type, ['inpost'])) {
                $form_width_input_attrs = [
                    'min'    => 1,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => '%'
                ];
            }

            $page_control_args['form_width'] = new WP_Customize_Range_Value_Control(
                $this->wp_customize,
                $this->option_prefix . '[form_width]',
                apply_filters('mo_optin_form_customizer_form_width_args', array(
                        'section'     => $this->customizerClassInstance->design_section_id,
                        'settings'    => $this->option_prefix . '[form_width]',
                        'label'       => __('Optin Width', 'mailoptin'),
                        'input_attrs' => $form_width_input_attrs,
                        'priority'    => 5,
                    )
                )
            );
        }

        if (apply_filters('mo_optin_form_enable_form_image', false)) {

            if (apply_filters('mo_optin_form_enable_hide_form_image', false)) {
                $page_control_args['hide_form_image'] = new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[hide_form_image]',
                    apply_filters('mo_optin_form_customizer_hide_form_image_args', array(
                            'label'    => __('Hide Image', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->design_section_id,
                            'settings' => $this->option_prefix . '[hide_form_image]',
                            'type'     => 'light',
                            'priority' => 10,
                        )
                    )
                );
            }

            $this->default_form_image = apply_filters('mo_optin_form_partial_default_image', '');

            if (isset($this->wp_customize->selective_refresh)) {
                $this->wp_customize->selective_refresh->add_partial($this->option_prefix . '[form_image]', array(
                    // Whether to refresh the entire preview in case a partial cannot be refreshed.
                    // A partial render is considered a failure if the render_callback returns false.
                    'fallback_refresh'    => true,
                    'selector'            => '.mo-optin-form-image-wrapper',
                    // determines if change will apply to container / wrapper element.
                    'container_inclusive' => apply_filters('mo_optin_form_image_partial_container_inclusive', false),
                    'render_callback'     => apply_filters('mo_optin_form_image_render_callback', function () {
                        return do_shortcode("[mo-optin-form-image default='{$this->default_form_image}']");
                    })
                ));
            } else {
                // if selective refresh not supported, fallback to 'refresh' transport.
                $this->wp_customize->get_setting($this->option_prefix . '[form_image]')->transport = 'refresh';
            }

            $page_control_args['form_image'] = new \WP_Customize_Cropped_Image_Control(
                $this->wp_customize,
                $this->option_prefix . '[form_image]',
                apply_filters('mo_optin_form_customizer_form_image_args', array(
                        'width'       => 220,
                        'height'      => 35,
                        'flex_width'  => true,
                        'flex_height' => true,
                        'label'       => __('Image', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->design_section_id,
                        'settings'    => $this->option_prefix . '[form_image]',
                        'priority'    => 11,
                    )
                )
            );
        }

        if (apply_filters('mo_optin_form_enable_form_background_image', false)) {

            $this->default_form_background_image = apply_filters('mo_optin_form_partial_default_background_image', '');

            if (apply_filters('mo_optin_form_enable_selective_refresh_form_background_image', true) && isset($this->wp_customize->selective_refresh)) {
                $this->wp_customize->selective_refresh->add_partial($this->option_prefix . '[form_background_image]', array(
                    // Whether to refresh the entire preview in case a partial cannot be refreshed.
                    // A partial render is considered a failure if the render_callback returns false.
                    'fallback_refresh'    => true,
                    'selector'            => apply_filters('mo_optin_form_background_image_partial_selector', '.mo-optin-form-background-image-wrapper'),
                    // determines if change will apply to container / wrapper element.
                    'container_inclusive' => apply_filters('mo_optin_form_image_partial_container_inclusive', false),
                    'render_callback'     => apply_filters('mo_optin_form_image_render_callback', false)
                ));
            } else {
                // if selective refresh not supported, fallback to 'refresh' transport.
                $this->wp_customize->get_setting($this->option_prefix . '[form_background_image]')->transport = 'refresh';
            }

            $page_control_args['form_background_image'] = new \WP_Customize_Image_Control(
                $this->wp_customize,
                $this->option_prefix . '[form_background_image]',
                apply_filters('mo_optin_form_customizer_form_background_image_args', array(
                        'label'    => __('Background Image', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->design_section_id,
                        'settings' => $this->option_prefix . '[form_background_image]',
                        'priority' => 20,
                    )
                )
            );
        }

        $page_control_args['form_background_color'] = new \WP_Customize_Color_Control(
            $this->wp_customize,
            $this->option_prefix . '[form_background_color]',
            apply_filters('mailoptin_optin_customizer_form_background_color_args', array(
                    'label'    => __('Background Color', 'mailoptin'),
                    'section'  => $this->customizerClassInstance->design_section_id,
                    'settings' => $this->option_prefix . '[form_background_color]',
                    'priority' => 20,
                )
            )
        );

        $page_control_args['form_border_color'] = new \WP_Customize_Color_Control(
            $this->wp_customize,
            $this->option_prefix . '[form_border_color]',
            apply_filters('mo_optin_form_customizer_form_border_color_args', array(
                    'label'    => __('Border Color', 'mailoptin'),
                    'section'  => $this->customizerClassInstance->design_section_id,
                    'settings' => $this->option_prefix . '[form_border_color]',
                    'priority' => 40,
                )
            )
        );

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $content = sprintf(
                '<div class="mo-pro"><a href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=custom_css_notice" target="_blank">%s</a></div>',
                __('Premium Version Available', 'mailoptin')
            );

            $content .= sprintf(
                __('Upgrade to %sMailOptin Premium%s for more customization options including feature to add your own custom CSS.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=custom_css_notice">',
                '</a>'
            );

            // always prefix with the name of the connect/connection service.
            $page_control_args['custom_css_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[custom_css_notice]',
                apply_filters('mo_optin_form_customizer_custom_css_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->design_section_id,
                        'settings' => $this->option_prefix . '[custom_css_notice]',
                        'priority' => 199,
                    )
                )
            );
        }

        do_action('mailoptin_before_design_controls_addition');

        foreach ($page_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_design_controls_addition');
    }

    public function headline_controls()
    {
        $headline_control_args = apply_filters(
            "mo_optin_form_customizer_headline_controls",
            array(
                'hide_headline'              => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[hide_headline]',
                    apply_filters('mo_optin_form_customizer_hide_headline_args', array(
                            'label'    => __('Hide Headline', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->headline_section_id,
                            'settings' => $this->option_prefix . '[hide_headline]',
                            'type'     => 'light',
                            'priority' => 5,
                        )
                    )
                ),
                'headline'                   => new WP_Customize_Tinymce_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[headline]',
                    apply_filters('mo_optin_form_customizer_headline_args', array(
                            'label'         => __('Headline', 'mailoptin'),
                            'section'       => $this->customizerClassInstance->headline_section_id,
                            'settings'      => $this->option_prefix . '[headline]',
                            'editor_id'     => 'headline',
                            'editor_height' => 50,
                            'quicktags'     => true,
                            'priority'      => 10
                        )
                    )
                ),
                'headline_font_color'        => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[headline_color]',
                    apply_filters('mo_optin_form_customizer_headline_font_color_args', array(
                            'label'    => __('Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->headline_section_id,
                            'settings' => $this->option_prefix . '[headline_font_color]',
                            'priority' => 20
                        )
                    )
                ),
                'headline_font_size_desktop' => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[headline_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->headline_section_id,
                        'settings' => $this->option_prefix . '[headline_font_size_desktop]',
                        'priority' => 30
                    )
                ),
                'headline_font_size_tablet'  => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[headline_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->headline_section_id,
                        'settings' => $this->option_prefix . '[headline_font_size_tablet]',
                        'priority' => 31
                    )
                ),
                'headline_font_size_mobile'  => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[headline_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->headline_section_id,
                        'settings' => $this->option_prefix . '[headline_font_size_mobile]',
                        'priority' => 32
                    )
                ),
                'headline_font'              => new WP_Customize_Google_Font_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[headline_font]',
                    apply_filters('mo_optin_form_customizer_headline_font_args', array(
                            'label'    => __('Font Family', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->headline_section_id,
                            'settings' => $this->option_prefix . '[headline_font]',
                            'count'    => 300,
                            'priority' => 40
                        )
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_headline_controls_addition');

        if ( ! empty($headline_control_args)) {
            foreach ($headline_control_args as $id => $args) {
                if (is_object($args)) {
                    $this->wp_customize->add_control($args);
                } else {
                    $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
                }
            }

            do_action('mailoptin_after_headline_controls_addition');
        }
    }

    public function description_controls()
    {
        $description_controls_args = apply_filters(
            "mo_optin_form_customizer_description_controls",
            array(
                'hide_description'              => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[hide_description]',
                    apply_filters('mo_optin_form_customizer_hide_description_args', array(
                            'label'    => __('Hide Description', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->description_section_id,
                            'settings' => $this->option_prefix . '[hide_description]',
                            'type'     => 'light',
                            'priority' => 10,
                        )
                    )
                ),
                'description'                   => new WP_Customize_Tinymce_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[description]',
                    apply_filters('mo_optin_form_customizer_description_args', array(
                            'label'     => __('Description', 'mailoptin'),
                            'section'   => $this->customizerClassInstance->description_section_id,
                            'settings'  => $this->option_prefix . '[description]',
                            'editor_id' => 'description',
                            'quicktags' => true,
                            'priority'  => 20
                        )
                    )
                ),
                'description_font_color'        => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[description_font_color]',
                    apply_filters('mo_optin_form_customizer_description_font_color_args', array(
                            'label'    => __('Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->description_section_id,
                            'settings' => $this->option_prefix . '[description_font_color]',
                            'priority' => 30
                        )
                    )
                ),
                'description_font'              => new WP_Customize_Google_Font_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[description_font]',
                    apply_filters('mo_optin_form_customizer_description_font_args', array(
                            'label'    => __('Font Family', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->description_section_id,
                            'settings' => $this->option_prefix . '[description_font]',
                            'count'    => 300,
                            'priority' => 15
                        )
                    )
                ),
                'description_font_size_desktop' => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[description_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->description_section_id,
                        'settings' => $this->option_prefix . '[description_font_size_desktop]',
                        'priority' => 40
                    )
                ),
                'description_font_size_tablet'  => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[description_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->description_section_id,
                        'settings' => $this->option_prefix . '[description_font_size_tablet]',
                        'priority' => 41
                    )
                ),
                'description_font_size_mobile'  => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[description_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->description_section_id,
                        'settings' => $this->option_prefix . '[description_font_size_mobile]',
                        'priority' => 42
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_description_controls_addition');

        foreach ($description_controls_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_description_controls_addition');
    }


    public function note_controls()
    {
        $note_controls_args = apply_filters(
            "mo_optin_form_customizer_note_controls",
            array(
                'hide_note'                => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[hide_note]',
                    apply_filters('mo_optin_form_customizer_hide_note_args', array(
                            'label'    => __('Hide Note', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->note_section_id,
                            'settings' => $this->option_prefix . '[hide_note]',
                            'type'     => 'light',
                            'priority' => 5,
                        )
                    )
                ),
                'note'                     => new WP_Customize_Tinymce_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note]',
                    apply_filters('mo_optin_form_customizer_note_args', array(
                            'label'         => __('Note', 'mailoptin'),
                            'section'       => $this->customizerClassInstance->note_section_id,
                            'settings'      => $this->option_prefix . '[note]',
                            'editor_id'     => 'note',
                            'editor_height' => 50,
                            'quicktags'     => true,
                            'priority'      => 10
                        )
                    )
                ),
                'note_font_color'          => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_font_color]',
                    apply_filters('mo_optin_form_customizer_note_font_color_args', array(
                            'label'    => __('Color', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->note_section_id,
                            'settings' => $this->option_prefix . '[note_font_color]',
                            'priority' => 30
                        )
                    )
                ),
                'note_font'                => new WP_Customize_Google_Font_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_font]',
                    apply_filters('mo_optin_form_customizer_note_font_args', array(
                            'label'    => __('Font Family', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->note_section_id,
                            'settings' => $this->option_prefix . '[note_font]',
                            'count'    => 300,
                            'priority' => 20
                        )
                    )
                ),
                'note_font_size_desktop'   => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->note_section_id,
                        'settings' => $this->option_prefix . '[note_font_size_desktop]',
                        'priority' => 50
                    )
                ),
                'note_font_size_tablet'    => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->note_section_id,
                        'settings' => $this->option_prefix . '[note_font_size_tablet]',
                        'priority' => 51
                    )
                ),
                'note_font_size_mobile'    => new WP_Customize_Font_Size_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_font_size]',
                    array(
                        'label'    => esc_attr__('Font Size', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->note_section_id,
                        'settings' => $this->option_prefix . '[note_font_size_mobile]',
                        'priority' => 52
                    )
                ),
                'note_close_optin_onclick' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_close_optin_onclick]',
                    apply_filters('mo_optin_form_customizer_note_close_optin_onclick_args', array(
                            'label'       => __('Close Optin on Click', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->note_section_id,
                            'settings'    => $this->option_prefix . '[note_close_optin_onclick]',
                            'description' => sprintf(
                                __('Activate if you want a click on "note" to close the optin form. Particularly useful if close icon is hidden. %sLearn More%s', 'mailoptin'),
                                '<a href="https://mailoptin.io/article/text-link-closes-popup-optin-form/" target="_blank">', '</a>'
                            ),
                            'priority'    => 55,
                        )
                    )
                ),
                'note_acceptance_checkbox' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[note_acceptance_checkbox]',
                    apply_filters('mo_optin_form_customizer_note_acceptance_checkbox_onclick_args', array(
                            'label'       => __('Enable Acceptance Checkbox', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->note_section_id,
                            'settings'    => $this->option_prefix . '[note_acceptance_checkbox]',
                            'description' => sprintf(
                                __('Activate to display an acceptance checkbox that users have to check before they are subscribed. %sLearn More%s', 'mailoptin'),
                                '<a href="https://mailoptin.io/article/acceptance-checkbox-terms-privacy-policy/" target="_blank">', '</a>'
                            ),
                            'priority'    => 60,
                        )
                    )
                ),
                'note_acceptance_error'    => apply_filters('mo_optin_form_customizer_note_acceptance_error_args',
                    array(
                        'type'        => 'text',
                        'label'       => __('Checkbox Error Message', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->note_section_id,
                        'settings'    => $this->option_prefix . '[note_acceptance_error]',
                        'description' => __('Error message displayed when the acceptance checkbox is not checked.', 'mailoptin'),
                        'priority'    => 65
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_note_controls_addition');

        foreach ($note_controls_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_note_controls_addition');

    }

    public function fields_controls()
    {
        $optin_class_instance = $this->optin_class_instance;

        $cta_button_action_description = '';
        $cta_button_action_choices     = [
            'reveal_optin_form' => __('Reveal Optin Form', 'mailoptin'),
            'navigate_to_url'   => __('Navigate to URL', 'mailoptin'),
        ];

        if ( ! in_array($this->customizerClassInstance->optin_campaign_type, ['sidebar', 'inpost'])) {
            $cta_button_action_choices['close_optin']             = __('Close optin', 'mailoptin');
            $cta_button_action_choices['close_optin_reload_page'] = __('Close optin and reload page', 'mailoptin');
        }

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {

            $cta_button_action_description = sprintf(
                __('Upgrade to %sMailOptin Premium%s to have the option to close, close and reload or reveal optin form when CTA button is clicked.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cta_button_action">',
                '</a>'
            );

            unset($cta_button_action_choices['reveal_optin_form']);
            unset($cta_button_action_choices['close_optin']);
            unset($cta_button_action_choices['close_optin_reload_page']);
        }

        $field_controls_args = apply_filters(
            "mo_optin_form_customizer_fields_controls",
            array(
                'use_custom_html'          => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[use_custom_html]',
                    apply_filters('mo_optin_form_customizer_use_custom_html_args', array(
                            'label'       => __('Use Custom HTML', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->fields_section_id,
                            'settings'    => $this->option_prefix . '[use_custom_html]',
                            'description' => __('Activate to hide opt-in form and display custom content instead.', 'mailoptin'),
                            'type'        => 'light',
                            'priority'    => 2,
                        )
                    )
                ),
                'custom_html_content'      => new WP_Customize_Ace_Editor_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[custom_html_content]',
                    apply_filters('mo_optin_form_customizer_custom_html_content_args', array(
                            'editor_id'   => 'custom-css',
                            'language'    => 'html',
                            'type'        => 'textarea',
                            'label'       => __('Custom HTML', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->fields_section_id,
                            'settings'    => $this->option_prefix . '[custom_html_content]',
                            'description' => __('Type or paste your HTML here. Shortcodes are supported.', 'mailoptin'),
                            'priority'    => 3
                        )
                    )
                ),
                'fields'                   => new WP_Customize_Fields_Repeater_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[fields]',
                    apply_filters('mo_optin_form_customizer_fields_args', array(
                            'section'                 => $this->customizerClassInstance->fields_section_id,
                            'settings'                => $this->option_prefix . '[fields]',
                            'default_values'          => (new AbstractCustomizer($this->optin_campaign_id))->customizer_defaults['fields'],
                            'customizerClassInstance' => $this->customizerClassInstance,
                            'optin_campaign_id'       => $this->optin_campaign_id,
                            'optin_class_instance'    => $optin_class_instance,
                            'priority'                => 20
                        )
                    )
                ),
                'submit_button_header'     => new WP_Customize_Custom_Content(
                    $this->wp_customize,
                    $this->option_prefix . '[submit_button_header]',
                    apply_filters('mo_optin_form_customizer_submit_button_header_args', array(
                            'content'     => '<div class="mo-field-header">' . __("Submit Button", 'mailoptin') . '</div>',
                            'block_class' => 'mo-field-header-wrapper',
                            'section'     => $this->customizerClassInstance->fields_section_id,
                            'settings'    => $this->option_prefix . '[submit_button_header]',
                            'priority'    => 78,
                        )
                    )
                ),
                'submit_button'            => apply_filters('mo_optin_form_customizer_submit_button_args',
                    array(
                        'type'        => 'text',
                        'label'       => __('Button Label', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->fields_section_id,
                        'settings'    => $this->option_prefix . '[submit_button]',
                        'priority'    => 80,
                        'description' => __('The value/label of the submit button.', 'mailoptin'),
                    )
                ),
                'submit_button_color'      => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[submit_button_color]',
                    apply_filters('mo_optin_form_customizer_submit_button_color_args', array(
                            'label'       => __('Button Color', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->fields_section_id,
                            'settings'    => $this->option_prefix . '[submit_button_color]',
                            'priority'    => 90,
                            'description' => __('The text color for the submit button field.', 'mailoptin'),
                        )
                    )
                ),
                'submit_button_background' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[submit_button_background]',
                    apply_filters('mo_optin_form_customizer_submit_button_background_args', array(
                            'label'       => __('Button Background', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->fields_section_id,
                            'settings'    => $this->option_prefix . '[submit_button_background]',
                            'priority'    => 100,
                            'description' => __('The background color of the submit button.', 'mailoptin'),
                        )
                    )
                ),
                'submit_button_font'       => new WP_Customize_Google_Font_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[submit_button_font]',
                    apply_filters('mo_optin_form_customizer_submit_button_font_args', array(
                            'label'       => __('Button Font'),
                            'section'     => $this->customizerClassInstance->fields_section_id,
                            'settings'    => $this->option_prefix . '[submit_button_font]',
                            'description' => __('The font family for the submit button field.', 'mailoptin'),
                            'count'       => 300,
                            'priority'    => 110
                        )
                    )
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if (in_array($optin_class_instance::CTA_BUTTON_SUPPORT, $optin_class_instance->features_support())) {

            $field_controls_args['display_only_button'] = new WP_Customize_Toggle_Control(
                $this->wp_customize,
                $this->option_prefix . '[display_only_button]',
                apply_filters('mo_optin_form_customizer_display_only_button_args', array(
                        'label'       => __('Display Only CTA Button', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->fields_section_id,
                        'settings'    => $this->option_prefix . '[display_only_button]',
                        'description' => __('Activate to hide opt-in form and display a call-to-action button instead.', 'mailoptin'),
                        'type'        => 'light',
                        'priority'    => 5,
                    )
                )
            );

            $field_controls_args['cta_button_header'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[cta_button_header]',
                apply_filters('mo_optin_form_customizer_cta_button_header_args', array(
                        'content'     => '<div class="mo-field-header">' . __("Call-to-action Button", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizerClassInstance->fields_section_id,
                        'settings'    => $this->option_prefix . '[cta_button_header]',
                        'priority'    => 120,
                    )
                )
            );

            $field_controls_args['cta_button_action'] = apply_filters('mo_optin_form_customizer_cta_button_action_args', array(
                    'type'        => 'select',
                    'description' => $cta_button_action_description,
                    'choices'     => $cta_button_action_choices,
                    'label'       => __('Action After Button Click', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->fields_section_id,
                    'settings'    => $this->option_prefix . '[cta_button_action]',
                    'priority'    => 125,
                )
            );

            $field_controls_args['cta_button_navigation_url'] = apply_filters('mo_optin_form_customizer_cta_button_navigation_url_args', array(
                    'type'        => 'text',
                    'label'       => __('Enter URL', 'mailoptin'),
                    'description' => __('URL should begin with http or https.', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->fields_section_id,
                    'settings'    => $this->option_prefix . '[cta_button_navigation_url]',
                    'input_attrs' => ['placeholder' => 'https://'],
                    'priority'    => 127,
                )
            );

            $field_controls_args['cta_button'] = apply_filters('mo_optin_form_customizer_cta_button_args',
                array(
                    'type'        => 'text',
                    'label'       => __('Button Label', 'mailoptin'),
                    'section'     => $this->customizerClassInstance->fields_section_id,
                    'settings'    => $this->option_prefix . '[cta_button]',
                    'priority'    => 130,
                    'description' => __('The value/label of the call-to-action button.', 'mailoptin'),
                )
            );

            $field_controls_args['cta_button_color'] = new \WP_Customize_Color_Control(
                $this->wp_customize,
                $this->option_prefix . '[cta_button_color]',
                apply_filters('mo_optin_form_customizer_cta_button_color_args', array(
                        'label'       => __('Button Color', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->fields_section_id,
                        'settings'    => $this->option_prefix . '[cta_button_color]',
                        'priority'    => 140,
                        'description' => __('The text color for the call-to-action button field.', 'mailoptin'),
                    )
                )
            );

            $field_controls_args['cta_button_background'] = new \WP_Customize_Color_Control(
                $this->wp_customize,
                $this->option_prefix . '[cta_button_background]',
                apply_filters('mo_optin_form_customizer_cta_button_background_args', array(
                        'label'       => __('Button Background', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->fields_section_id,
                        'settings'    => $this->option_prefix . '[cta_button_background]',
                        'priority'    => 150,
                        'description' => __('The background color of the call-to-action button.', 'mailoptin'),
                    )
                )
            );

            $field_controls_args['cta_button_font'] = new WP_Customize_Google_Font_Control(
                $this->wp_customize,
                $this->option_prefix . '[cta_button_font]',
                apply_filters('mo_optin_form_customizer_cta_button_font_args', array(
                        'label'       => __('Button Font'),
                        'section'     => $this->customizerClassInstance->fields_section_id,
                        'settings'    => $this->option_prefix . '[cta_button_font]',
                        'description' => __('The font family for the call-to-action button field.', 'mailoptin'),
                        'count'       => 300,
                        'priority'    => 160
                    )
                )
            );
        }

        do_action('mailoptin_before_fields_controls_addition');

        foreach ($field_controls_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_fields_controls_addition');
    }

    public function configuration_controls()
    {
        $content_control_args = apply_filters(
            "mo_optin_form_customizer_configuration_controls",
            array(
                'split_test_note'            => apply_filters('mo_optin_form_customizer_split_test_note_args', array(
                        'type'        => 'textarea',
                        'label'       => __('Split Test Note', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->configuration_section_id,
                        'settings'    => $this->option_prefix . '[split_test_note]',
                        'description' => __('Useful for keeping track of changes between each split test you create.', 'mailoptin'),
                        'priority'    => 13,
                    )
                ),
                'inpost_form_optin_position' => apply_filters('mo_optin_form_customizer_inpost_form_optin_position_args', array(
                        'type'        => 'select',
                        'label'       => __('Optin Form Position', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->configuration_section_id,
                        'settings'    => $this->option_prefix . '[inpost_form_optin_position]',
                        'description' => __('Select position within your post the optin form will be displayed.', 'mailoptin'),
                        'choices'     => [
                            'before_content' => __('Before Content', 'mailoptin'),
                            'after_content'  => __('After Content', 'mailoptin'),
                        ],
                        'priority'    => 15,
                    )
                ),
                'slidein_position'           => apply_filters('mo_optin_form_customizer_slidein_position_args', array(
                        'type'     => 'select',
                        'choices'  => ['bottom_right' => __('Bottom Right', 'mailoptin'), 'bottom_left' => __('Bottom Left', 'mailoptin')],
                        'label'    => __('Slide-in Position', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->configuration_section_id,
                        'settings' => $this->option_prefix . '[slidein_position]',
                        'priority' => 20,
                    )
                ),
                'bar_position'               => apply_filters('mo_optin_form_customizer_bar_position_args', array(
                        'type'     => 'select',
                        'choices'  => ['top' => __('Top', 'mailoptin'), 'bottom' => __('Bottom', 'mailoptin')],
                        'label'    => __('Bar Position', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->configuration_section_id,
                        'settings' => $this->option_prefix . '[bar_position]',
                        'priority' => 30,
                    )
                ),
                'bar_sticky'                 => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[bar_sticky]',
                    apply_filters('mo_optin_form_customizer_bar_sticky_args', array(
                            'label'       => __('Sticky Bar?', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->configuration_section_id,
                            'settings'    => $this->option_prefix . '[bar_sticky]',
                            'description' => __('Check to make bar sticky.', 'mailoptin'),
                            'type'        => 'light',
                            'priority'    => 40,
                        )
                    )
                ),
                'hide_close_button'          => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[hide_close_button]',
                    apply_filters('mo_optin_form_customizer_hide_close_button_args', array(
                            'label'    => __('Hide Close Button', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->configuration_section_id,
                            'settings' => $this->option_prefix . '[hide_close_button]',
                            'type'     => 'light',
                            'priority' => 45,
                        )
                    )
                ),
                'close_backdrop_click'       => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[close_backdrop_click]',
                    apply_filters('mo_optin_form_customizer_close_backdrop_click_args', array(
                            'label'    => __('Close on Overlay Click', 'mailoptin'),
                            'section'  => $this->customizerClassInstance->configuration_section_id,
                            'settings' => $this->option_prefix . '[close_backdrop_click]',
                            'type'     => 'light',
                            'priority' => 48,
                        )
                    )
                ),
                'cookie'                     => apply_filters('mo_optin_form_customizer_cookie_args', array(
                        'type'        => 'text',
                        'label'       => __('Cookie Duration', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->configuration_section_id,
                        'settings'    => $this->option_prefix . '[cookie]',
                        'priority'    => 80,
                        'description' => sprintf(
                            __('The length of time before this optin will display again to the user once they exit or close this campaign (defaults to 30 days). %sSet to 0 to prevent cookies from being set.%s', 'mailoptin'),
                            '<strong>', '</strong>'
                        )
                    )
                ),
                'success_cookie'             => apply_filters('mo_optin_form_customizer_success_cookie_args', array(
                        'type'        => 'text',
                        'label'       => __('Success Cookie Duration', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->configuration_section_id,
                        'settings'    => $this->option_prefix . '[success_cookie]',
                        'priority'    => 90,
                        'description' => sprintf(
                            __('The length of time before the optin will display again to the user once they successfully opt in to this campaign (defaults to value of exit cookie above). %sSet to 0 to prevent cookies from being set.%s', 'mailoptin'),
                            '<strong>', '</strong>'
                        )
                    )
                ),
                'remove_branding'            => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[remove_branding]',
                    apply_filters('mo_optin_form_customizer_remove_branding_args', array(
                            'label'       => __('Remove MailOptin Branding', 'mailoptin'),
                            'description' => sprintf(
                                __('%sSet your affiliate link%s and make money with branding.', 'mailoptin'),
                                '<a href="' . MAILOPTIN_SETTINGS_SETTINGS_PAGE . '#mailoptin_affiliate_url_row" target="_blank">',
                                '</a>'
                            ),
                            'section'     => $this->customizerClassInstance->configuration_section_id,
                            'settings'    => $this->option_prefix . '[remove_branding]',
                            'type'        => 'light',
                            'priority'    => 110,
                        )
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ( ! OptinCampaignsRepository::is_split_test_variant($this->optin_campaign_id)) {
            unset($content_control_args['split_test_note']);
        }

        if ($this->customizerClassInstance->optin_campaign_type !== 'lightbox') {
            unset($content_control_args['close_backdrop_click']);
        }

        do_action('mailoptin_before_configuration_controls_addition');

        foreach ($content_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_configuration_controls_addition');
    }

    public function integration_controls()
    {
        $email_providers = ConnectionsRepository::get_connections();

        $integration_control_args = apply_filters(
            "mo_optin_form_customizer_integration_controls",
            array(
                'integrations'          => new WP_Customize_Integration_Repeater_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[integrations]',
                    apply_filters('mo_optin_form_customizer_integrations_args', array(
                            'section'                 => $this->customizerClassInstance->integration_section_id,
                            'settings'                => $this->option_prefix . '[integrations]',
                            'default_values'          => (new AbstractCustomizer($this->optin_campaign_id))->customizer_defaults['integrations'],
                            'customizerClassInstance' => $this->customizerClassInstance,
                            'optin_campaign_id'       => $this->optin_campaign_id,
                            'priority'                => 15
                        )
                    )
                ),
                'custom_field_mappings' => apply_filters('mo_optin_form_customizer_custom_field_mappings_args', array(
                        'type'     => 'hidden',
                        // simple hack because control won't render if label is empty.
                        'label'    => '&nbsp;',
                        'section'  => $this->customizerClassInstance->integration_section_id,
                        'settings' => $this->option_prefix . '[custom_field_mappings]',
                        // 999 cos we want it to be bottom.
                        'priority' => 20,
                    )
                ),
                'ajax_nonce'            => apply_filters('mo_optin_form_customizer_ajax_nonce_args', array(
                        'type'     => 'hidden',
                        // simple hack because control won't render if label is empty.
                        'label'    => '&nbsp;',
                        'section'  => $this->customizerClassInstance->integration_section_id,
                        'settings' => $this->option_prefix . '[ajax_nonce]',
                        // 999 cos we want it to be bottom.
                        'priority' => 999,
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_integration_controls_addition');

        if ( ! apply_filters('mailoptin_enable_leadbank', false)) {
            $content = sprintf(
                __('To store leads or subscribers in MailOptin without requiring an email service provider like Mailchimp, %sUpgrade to premium%s now.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/lead-generation-wordpress/#leadbank">',
                '</a>'
            );

            $integration_control_args['leadbank_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[leadbank_notice]',
                apply_filters('mo_optin_form_customizer_leadbank_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->integration_section_id,
                        'settings' => $this->option_prefix . '[leadbank_notice]',
                        'priority' => 202,
                    )
                )
            );
        }

        if (count($email_providers) === 1 && key($email_providers) == '') {
            $content = sprintf(
                __('No integration or email provider has been connected to MailOptin. %sClick here%s to do that now.', 'mailoptin'),
                '<a target="_blank" href="' . MAILOPTIN_CONNECTIONS_SETTINGS_PAGE . '">',
                '</a>'
            );

            $integration_control_args['no_integration_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[no_integration_notice]',
                apply_filters('mo_optin_form_customizer_no_integration_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->integration_section_id,
                        'settings' => $this->option_prefix . '[no_integration_notice]',
                        'priority' => 200,
                    )
                )
            );
        }

        foreach ($integration_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_integration_controls_addition');
    }

    /**
     * @param CustomizerControls $instance
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function after_conversion_controls()
    {
        $success_control_choices = [
            'success_message'         => __('Display success message.', 'mailoptin'),
            'close_optin'             => __('Close optin', 'mailoptin'),
            'close_optin_reload_page' => __('Close optin and reload page', 'mailoptin'),
            'redirect_url'            => __('Redirect to URL', 'mailoptin')
        ];

        if (in_array($this->customizerClassInstance->optin_campaign_type, ['inpost', 'sidebar'])) {
            unset($success_control_choices['close_optin']);
            unset($success_control_choices['close_optin_reload_page']);
        }

        $success_controls_args = apply_filters(
            "mo_optin_form_customizer_success_controls",
            array(
                'success_action' => apply_filters('mo_optin_form_customizer_success_action_args', array(
                        'type'        => 'select',
                        'choices'     => $success_control_choices,
                        'label'       => __('Success Action', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->success_section_id,
                        'settings'    => $this->option_prefix . '[success_action]',
                        'description' => __('What to do after users subscribe.', 'mailoptin'),
                        'priority'    => 10,
                    )
                ),

                'success_message'    => apply_filters('mo_optin_form_customizer_success_message_args', array(
                        'type'     => 'textarea',
                        'label'    => __('Optin Success Message', 'mailoptin'),
                        'section'  => $this->customizerClassInstance->success_section_id,
                        'settings' => $this->option_prefix . '[success_message]',
                        'priority' => 15,
                    )
                ),
                'redirect_url_value' => apply_filters('mo_optin_form_customizer_redirect_url_value_args', array(
                        'type'        => 'text',
                        'label'       => __('Redirect URL', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->success_section_id,
                        'settings'    => $this->option_prefix . '[redirect_url_value]',
                        'priority'    => 20,
                        'description' => __('Specify a URL to redirect users to after opt-in. Must begin with http or https.', 'mailoptin')
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $content = sprintf(
                '<div class="mo-pro"><a href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=quick_setup_panel" target="_blank">%s</a></div>',
                __('Premium Version Available', 'mailoptin')
            );
            $content .= sprintf(
                __('Upgrade to %sMailOptin Premium%s for autoresponder, pass lead data to redirect URL, send email notification and trigger success script after conversion.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=quick_setup_panel">',
                '</a>'
            );

            // always prefix with the name of the connect/connection service.
            $success_controls_args['after_conversion_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[after_conversion_notice]',
                apply_filters('mo_optin_form_customizer_after_conversion_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->success_section_id,
                        'settings' => $this->option_prefix . '[after_conversion_notice]',
                        'priority' => 199,
                    )
                )
            );
        }

        do_action('mailoptin_before_success_controls_addition');

        foreach ($success_controls_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_success_controls_addition');
    }

    /**
     * Page filter display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function page_filter_display_rule_controls()
    {
        $page_filter_control_args = array(
            'load_optin_globally'             => new WP_Customize_Toggle_Control(
                $this->wp_customize,
                $this->option_prefix . '[load_optin_globally]',
                apply_filters('mo_optin_form_customizer_load_optin_globally_args', array(
                        'label'       => __('Globally show optin', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[load_optin_globally]',
                        'description' => sprintf(
                            __('The optin will be shown on all pages of your website if activated. %sDo not activate%s if you want to show optin on specific areas of your site using the settings below.', 'mailoptin'),
                            '<strong>',
                            '</strong>'
                        ),
                        'type'        => 'light',
                        'priority'    => 20
                    )
                )
            ),
            'exclusive_post_types_posts_load' => new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[exclusive_post_types_posts_load]',
                apply_filters('mo_optin_form_customizer_exclusive_post_types_posts_load_args', array(
                        'label'       => __('Show optin specifically on:', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[exclusive_post_types_posts_load]',
                        'description' => __('Display the optin only on the selected posts and/or pages.', 'mailoptin'),
                        'search_type' => 'exclusive_post_types_posts_load',
                        'choices'     => ControlsHelpers::get_all_post_types_posts(),
                        'priority'    => 35
                    )
                )
            )
        );

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $page_filter_control_args['load_optin_index']          = new WP_Customize_Toggle_Control(
                $this->wp_customize,
                $this->option_prefix . '[load_optin_index]',
                apply_filters('mo_optin_form_customizer_load_optin_index_args', array(
                        'label'       => __('Front Page, Archive and Search Pages', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[load_optin_index]',
                        'description' => __('Display the optin on home front page, archive and search pages', 'mailoptin'),
                        'priority'    => 30,
                        'type'        => 'light',
                    )
                )
            );
            $page_filter_control_args['post_categories_load']      = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_categories_load]',
                apply_filters('mo_optin_form_customizer_post_categories_load_args', array(
                        'label'       => __('Show on post categories:'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[post_categories_load]',
                        'description' => __('Display the optin on posts that are in any of the selected categories.', 'mailoptin'),
                        'choices'     => ControlsHelpers::get_categories(),
                        'priority'    => 40
                    )
                )
            );
            $page_filter_control_args['post_tags_load']            = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_tags_load]',
                apply_filters('mo_optin_form_customizer_post_tags_load_args', array(
                        'label'       => __('Show on post tags:'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[post_tags_load]',
                        'description' => __('Display the optin on posts that are in any of the selected tags.', 'mailoptin'),
                        'choices'     => ControlsHelpers::get_tags(),
                        'priority'    => 45
                    )
                )
            );
            $page_filter_control_args['exclusive_post_types_load'] = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[exclusive_post_types_load]',
                apply_filters('mo_optin_form_customizer_exclusive_post_types_load_args', array(
                        'label'       => __('Show optin on post types:', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[exclusive_post_types_load]',
                        'description' => __('Display the optin only on the selected post types.', 'mailoptin'),
                        'choices'     => ControlsHelpers::get_post_types(),
                        'priority'    => 50
                    )
                )
            );
            $page_filter_control_args['posts_never_load']          = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[posts_never_load]',
                apply_filters('mo_optin_form_customizer_posts_never_load_args', array(
                        'label'       => __('Never show optin on these posts:'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[posts_never_load]',
                        'description' => __('Select the posts this optin should never be loaded on.', 'mailoptin'),
                        'search_type' => 'posts_never_load',
                        'choices'     => ControlsHelpers::get_post_type_posts('post'),
                        'priority'    => 60
                    )
                )
            );
            $page_filter_control_args['post_categories_hide']      = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_categories_hide]',
                apply_filters('mo_optin_form_customizer_post_categories_hide_args', array(
                        'label'       => __('Never show on these post categories:'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[post_categories_hide]',
                        'description' => __('Hide the optin on posts that are in any of the selected categories.', 'mailoptin'),
                        'choices'     => ControlsHelpers::get_categories(),
                        'priority'    => 65
                    )
                )
            );
            $page_filter_control_args['pages_never_load']          = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[pages_never_load]',
                apply_filters('mo_optin_form_customizer_pages_never_load_args', array(
                        'label'       => __('Never show optin on these pages:'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[pages_never_load]',
                        'description' => __('Select the pages this optin should never be loaded on.', 'mailoptin'),
                        'search_type' => 'pages_never_load',
                        'choices'     => ControlsHelpers::get_post_type_posts('page'),
                        'priority'    => 70
                    )
                )
            );
            $page_filter_control_args['cpt_never_load']            = new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[cpt_never_load]',
                apply_filters('mo_optin_form_customizer_cpt_never_load_args', array(
                        'label'       => __('Never show optin on these CPT posts:'),
                        'section'     => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[cpt_never_load]',
                        'description' => __('Select "custom post type" posts this optin should never be loaded on.', 'mailoptin'),
                        'search_type' => 'cpt_never_load',
                        'choices'     => ControlsHelpers::get_all_post_types_posts(array('post', 'page')),
                        'priority'    => 80
                    )
                )
            );
        } else {
            $content = sprintf(
                '<div class="mo-pro"><a href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=display_rules_panel_lite" target="_blank">%s</a></div>',
                __('Premium Version Available', 'mailoptin')
            );

            $content .= sprintf(
                __('Upgrade to %sMailOptin Premium%s to embed with shortcodes, get optin triggers such as %3$sExit Intent%4$s, %3$sPage views%4$s, %3$sTime on Site%4$s, %3$sAdBlock detection%4$s, %3$sReferral Detection%4$s, %3$sScroll trigger%4$s, powerful page-level targeting and display rules proven to boost conversions.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=display_rules_panel_lite2">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $page_filter_control_args['optin_trigger_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[optin_trigger_notice]',
                apply_filters('mo_optin_form_customizer_optin_trigger_notice_args', array(
                        'content'  => $content,
                        'section'  => $this->customizerClassInstance->page_filter_display_rule_section_id,
                        'settings' => $this->option_prefix . '[optin_trigger_notice]',
                        'priority' => 199,
                    )
                )
            );
        }

        $page_filter_control_args = apply_filters(
            "mo_optin_form_customizer_page_filter_controls",
            $page_filter_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_page_filter_controls_addition');

        foreach ($page_filter_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_page_filter_controls_addition');

    }

    /**
     * Page filter display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function query_filter_display_rule_controls()
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {

            $query_filter_control_args = apply_filters(
                "mo_optin_form_customizer_query_filter_controls",

                array(

                    'filter_query_action' => apply_filters('mo_optin_form_customizer_filter_query_action', array(
                            'type'        => 'select',
                            'label'       => __('Query String', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->query_filter_display_rule_section_id,
                            'settings'    => $this->option_prefix . '[filter_query_action]',
                            'priority'    => 10,
                            'choices'     => [
                                '0'    => __('Select Action', 'mailoptin'),
                                'show' => __('Only show on matching pages', 'mailoptin'),
                                'hide' => __('Hide on matching pages', 'mailoptin'),
                            ],
                            'description' => __('Specify whether to display or hide the opt-in if the conditions below are met.', 'mailoptin')
                        )
                    ),

                    'filter_query_string' => apply_filters('mo_optin_form_customizer_filter_query_string', array(
                            'type'        => 'text',
                            'label'       => __('Query String Name', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->query_filter_display_rule_section_id,
                            'settings'    => $this->option_prefix . '[filter_query_string]',
                            'priority'    => 20,
                            'description' => __('Specify the query string where this opt-in should show/hide.', 'mailoptin')
                        )
                    ),

                    'filter_query_value' => apply_filters('mo_optin_form_customizer_filter_query_value', array(
                            'type'        => 'text',
                            'label'       => __('Query String Value', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->query_filter_display_rule_section_id,
                            'settings'    => $this->option_prefix . '[filter_query_value]',
                            'priority'    => 30,
                            'description' => __('Leave blank if you want to match the query string irrespective of its value.', 'mailoptin')
                        )
                    )
                ),
                $this->wp_customize,
                $this->option_prefix,
                $this->customizerClassInstance
            );

            do_action('mailoptin_before_user_filter_controls_addition');

            foreach ($query_filter_control_args as $id => $args) {
                if (is_object($args)) {
                    $this->wp_customize->add_control($args);
                } else {
                    $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
                }
            }

            do_action('mailoptin_after_user_filter_controls_addition');

        }
    }

    /**
     * Page filter display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function user_filter_display_rule_controls()
    {
        $user_filter_control_args = apply_filters(
            "mo_optin_form_customizer_user_filter_controls",
            array(
                'who_see_optin'            => apply_filters('mo_optin_form_customizer_who_see_optin_args', array(
                        'type'        => 'select',
                        'label'       => __('Who should see this optin?', 'mailoptin'),
                        'section'     => $this->customizerClassInstance->user_targeting_display_rule_section_id,
                        'settings'    => $this->option_prefix . '[who_see_optin]',
                        'description' => __('Decide who are able to see this optin.', 'mailoptin'),
                        'choices'     => [
                            'show_all'           => __('Show to all visitors and users', 'mailoptin'),
                            'show_logged_in'     => __('Show to only logged-in users', 'mailoptin'),
                            'show_non_logged_in' => __('Show to only users not logged-in', 'mailoptin'),
                            'show_to_roles'      => __('Show to specific user roles', 'mailoptin'),
                        ],
                        'priority'    => 10,
                    )
                ),
                'show_to_roles'            => new WP_Customize_Chosen_Select_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[show_to_roles]',
                    apply_filters('mo_optin_form_customizer_show_to_roles_args', array(
                            'label'       => __('Restrict to User Role', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->user_targeting_display_rule_section_id,
                            'settings'    => $this->option_prefix . '[show_to_roles]',
                            'description' => __('The opt-in form will only be shown to users with any of the roles you select here.', 'mailoptin'),
                            'choices'     => ControlsHelpers::get_roles(),
                            'priority'    => 11
                        )
                    )
                ),
                'prefill_logged_user_data' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[prefill_logged_user_data]',
                    apply_filters('mo_optin_form_customizer_prefill_logged_user_data_args', array(
                            'label'       => __('Prefill Form with User Data', 'mailoptin'),
                            'section'     => $this->customizerClassInstance->user_targeting_display_rule_section_id,
                            'settings'    => $this->option_prefix . '[prefill_logged_user_data]',
                            'description' => __('Enable to prefill form with the name and email address of logged in users.', 'mailoptin'),
                            'type'        => 'flat',// light, ios, flat
                            'priority'    => 12
                        )
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_user_filter_controls_addition');

        foreach ($user_filter_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_user_filter_controls_addition');

    }
}