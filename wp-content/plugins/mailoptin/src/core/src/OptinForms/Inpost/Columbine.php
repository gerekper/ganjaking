<?php

namespace MailOptin\Core\OptinForms\Inpost;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Tinymce_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Columbine extends AbstractOptinTheme
{
    public $optin_form_name = 'Columbine';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],
                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#91a6bf',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name...", 'mailoptin'),
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email...", 'mailoptin'),
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Subscribe To Newsletter", 'mailoptin'),
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Lora',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Lora',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#54C3A5',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Lora',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Palatino Linotype, Book Antiqua, serif',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Palatino Linotype, Book Antiqua, serif',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => '<em>' . __('Give it a try. You can unsubscribe at any time.', 'mailoptin') . '</em>',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Lora',
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Columbine',
                    'optin_type'  => 'inpost'
                ]
            ]
        );

        add_filter('mo_get_optin_form_headline_font', function ($font, $font_type) {
            if ($font_type == 'headline_font' && $font == 'Lora') {
                $font .= ':400,700,400italic';
            }

            return $font;
        }, 10, 2);

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', function () {
            return '#555555';
        });

        add_action('mo_optin_customize_preview_init', function () {
            add_action('wp_footer', [$this, 'customizer_preview_js']);
        });

        parent::__construct($optin_campaign_id);
    }

    public function features_support()
    {
        return [
            self::CTA_BUTTON_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_design_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_design_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_headline_settings($settings, $CustomizerSettingsInstance)
    {
        $settings['mini_headline'] = array(
            'default'           => __('DON\'T MISS OUT!', 'mailoptin'),
            'type'              => 'option',
            'transport'         => 'refresh',
            'sanitize_callback' => [$CustomizerSettingsInstance, '_remove_paragraph_from_headline'],
        );

        $settings['hide_mini_headline'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage'
        );

        $settings['mini_headline_font_color'] = array(
            'default'   => '#54C3A5',
            'type'      => 'option',
            'transport' => 'postMessage'
        );

        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_headline_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        add_filter('mailoptin_tinymce_customizer_control_count', function ($count) {
            return ++$count;
        });

        $controls['mini_headline'] = new WP_Customize_Tinymce_Control(
            $wp_customize,
            $option_prefix . '[mini_headline]',
            apply_filters('mo_optin_form_customizer_mini_headline_args', array(
                    'label'         => __('Mini Headline', 'mailoptin'),
                    'section'       => $customizerClassInstance->headline_section_id,
                    'settings'      => $option_prefix . '[mini_headline]',
                    'editor_id'     => 'mini_headline',
                    'quicktags'     => true,
                    'editor_height' => 50,
                    'priority'      => 4
                )
            )
        );

        $controls['hide_mini_headline'] = new WP_Customize_Toggle_Control(
            $wp_customize,
            $option_prefix . '[hide_mini_headline]',
            apply_filters('mo_optin_form_customizer_hide_mini_headline_args', array(
                    'label'    => __('Hide Mini Headline', 'mailoptin'),
                    'section'  => $customizerClassInstance->headline_section_id,
                    'settings' => $option_prefix . '[hide_mini_headline]',
                    'type'     => 'light',
                    'priority' => 2,
                )
            )
        );

        $controls['mini_headline_font_color'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[mini_headline_font_color]',
            apply_filters('mo_optin_form_customizer_headline_mini_headline_font_color_args', array(
                    'label'    => __('Mini Headline Color', 'mailoptin'),
                    'section'  => $customizerClassInstance->headline_section_id,
                    'settings' => $option_prefix . '[mini_headline_font_color]',
                    'priority' => 3
                )
            )
        );

        return $controls;
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_description_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_description_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_note_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_note_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    /**
     * @param mixed $fields_settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_fields_settings($fields_settings, $CustomizerSettingsInstance)
    {
        // change toggling of nae field to 'refresh' transport to handle switching different styling
        // for when name field is available or not.
        $fields_settings['hide_name_field']['transport']     = 'refresh';
        $fields_settings['display_only_button']['transport'] = 'refresh';

        return $fields_settings;
    }

    /**
     * @param array $fields_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_fields_controls($fields_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $fields_controls;
    }

    /**
     * @param mixed $configuration_settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_configuration_settings($configuration_settings, $CustomizerSettingsInstance)
    {
        return $configuration_settings;
    }


    /**
     * @param array $configuration_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_configuration_controls($configuration_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $configuration_controls;
    }

    /**
     * @param mixed $output_settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_output_settings($output_settings, $CustomizerSettingsInstance)
    {
        return $output_settings;
    }


    /**
     * @param array $output_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_output_controls($output_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $output_controls;
    }

    /**
     * Default description content.
     *
     * @return string
     */
    private function _description_content()
    {
        return __('Receive top education news, lesson ideas, teaching tips and more!', 'mailoptin');
    }

    /**
     * Fulfil interface contract.
     */
    public function optin_script()
    {
        ob_start();
        ?>
        <script type="text/javascript">
            // ensure doAction() is called/loaded after page is ready.
            (function () {
                if (document.readyState === "complete" ||
                    (document.readyState !== "loading" && !document.documentElement.doScroll)) {
                    doAction();
                } else {
                    document.addEventListener("DOMContentLoaded", doAction);
                }

                function doAction() {
                    jQuery(function ($) {
                        if ($('input#<?php echo $this->optin_campaign_uuid; ?>_inpost_name_field').css('display') === 'none') {
                            $('div#<?php echo $this->optin_campaign_uuid; ?>_inpost #columbine-email-field').removeClass().addClass('columbine-two-col1');
                            $('div#<?php echo $this->optin_campaign_uuid; ?>_inpost #columbine-submit-button').removeClass().addClass('columbine-two-col2');
                        }
                    });
                }
            })();
        </script>
        <?php

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function customizer_preview_js()
    {
        if ( ! \MailOptin\Core\is_mailoptin_customizer_preview()) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][mini_headline_font_color]', function (value) {
                        value.bind(function (to) {
                            $('.columbine-miniText').css('color', to);
                        });
                    });

                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_mini_headline]', function (value) {
                        value.bind(function (to) {
                            $('.columbine-miniText').toggle(!to);
                        });
                    });
                })
            })(jQuery)
        </script>
        <?php
    }

    /**
     * Template body.
     *
     * @return string
     */
    public function optin_form()
    {
        $mini_header = $this->get_customizer_value('mini_headline', __("Don't miss out!", 'mailoptin'));

        $mini_header_block = '<div class="columbine-miniText">' . $mini_header . '</div>';

        return <<<HTML
[mo-optin-form-wrapper class="columbine-container"]
    $mini_header_block
    [mo-optin-form-headline tag="div" class="columbine-heading"]
    [mo-optin-form-description class="columbine-caption"]
    <div class="columbine-form">
    [mo-optin-form-error]
    [mo-optin-form-fields-wrapper]
    <div id="columbine-name-field" class="columbine-three-col1">[mo-optin-form-name-field class="columbine-input"]</div>
    <div id="columbine-email-field" class="columbine-three-col2">[mo-optin-form-email-field class="columbine-input"]</div>
    [mo-optin-form-custom-fields tag_start='<div class="columbine-column">' tag_end='</div>']
    <div id="columbine-submit-button" class="columbine-three-col3">[mo-optin-form-submit-button class="columbine-submit"]</div>
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-cta-button]
    </div>
    [mo-mailchimp-interests]
    [mo-optin-form-note class="columbine-note"]
[/mo-optin-form-wrapper]
HTML;
    }


    /**
     * Template CSS styling.
     *
     * @return string
     */
    public function optin_form_css()
    {
        $optin_css_id = $this->optin_css_id;
        $optin_uuid   = $this->optin_campaign_uuid;

        $mini_headline_font_color = $this->get_customizer_value('mini_headline_font_color', '#54C3A5');

        $is_mini_headline_display = '';
        if ($this->get_customizer_value('hide_mini_headline', false)) {
            $is_mini_headline_display = 'display:none;';
        }

        // mini headline must share same font as headline.
        $mini_headline_font_family = $this->_construct_font_family($this->get_customizer_value('headline_font'));

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.columbine-container {
         background: #fff;
         border: 3px solid #91a6bf;
         -webkit-border-radius: 5px;
         -moz-border-radius: 5px;
         border-radius: 5px;
         margin: 10px auto;
         text-align: center;
         width: 100%;
         padding: 20px 30px;
         color: #555;
         -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
         box-sizing: border-box;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-miniText {
         font-size: 1em;
         line-height: 28px;
         text-transform: uppercase;
         color: $mini_headline_font_color;
         font-weight: bold;
         font-family: $mini_headline_font_family;
         $is_mini_headline_display
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-heading {
         font-weight: bold;
         line-height: 1.5;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-caption {
         margin-top: 12px;
         font-style: italic;
         font-size: 18px;
         line-height: 28px;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container .columbine-form {
         overflow: hidden;
         margin-top: 20px;
     }
html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-three-col1 {
         float: left;
         width: 33.333%;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-three-col2 {
         float: left;
         width: 33.333%;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-three-col3 {
         float: left;
         width: 33.333%;
     }
html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-two-col1 {
         float: left;
         width: 66.333%;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-two-col2 {
         float: right;
         width: 33.333%;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container input.columbine-input,
html div#$optin_uuid div#$optin_css_id.columbine-container input.mo-optin-form-custom-field.date-field,
html div#$optin_uuid div#$optin_css_id.columbine-container input.mo-optin-form-custom-field.text-field,
html div#$optin_uuid div#$optin_css_id.columbine-container input.mo-optin-form-custom-field.password-field,
html div#$optin_uuid div#$optin_css_id.columbine-container select.mo-optin-form-custom-field,
html div#$optin_uuid div#$optin_css_id.columbine-container textarea.mo-optin-form-custom-field {
         background-color: #ffffff;
         width: 100%;
         display: block;
         -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
         box-sizing: border-box;
         -webkit-border-radius: 0;
         -moz-border-radius: 0;
         border-radius: 0;
         padding: 11px 17px;
         font-size: 16px;
         line-height: 16px;
         text-align: left;
         border: 1px solid #ccc;
         color: #555;
         outline: none;
         margin: 0;
     }

     html div#$optin_uuid div#$optin_css_id.columbine-container input.columbine-input,
html div#$optin_uuid div#$optin_css_id.columbine-container input.mo-optin-form-custom-field.date-field,
html div#$optin_uuid div#$optin_css_id.columbine-container input.mo-optin-form-custom-field.text-field,
html div#$optin_uuid div#$optin_css_id.columbine-container input.mo-optin-form-custom-field.password-field,
html div#$optin_uuid div#$optin_css_id.columbine-container textarea.mo-optin-form-custom-field.textarea-field {
        -webkit-appearance: none;
     }
html div#$optin_uuid div#$optin_css_id.columbine-container input.columbine-submit,
html div#$optin_uuid div#$optin_css_id.columbine-container input[type="submit"].mo-optin-form-cta-button {
         display: block;
         -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
         box-sizing: border-box;
         -webkit-appearance: none;
         border: 0;
         background: #54C3A5;
         padding: 13px 10px;
         font-size: 16px;
         line-height: 16px;
         text-align: center;
         color: #fff;
         outline: none;
         cursor: pointer;
         font-weight: 700;
         width: 100%;
         margin: 0;
         border-radius: 0;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-note {
         margin-top: 10px;
         line-height: normal;
     }

html div#$optin_uuid div#$optin_css_id.columbine-container div.mo-optin-error {
         display: none;
         background: #FF0000;
         color: white;
         text-align: center;
         padding: .2em;
         margin: 0;
         width: 100%;
         font-size: 16px;
         -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
         box-sizing: border-box;
         border: 1px solid #FF0000;
     }

@media only screen and (max-width: 650px) {

    html div#$optin_uuid div#$optin_css_id.columbine-container div.mo-optin-error {
             margin-bottom: -10px;
     }
    html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-two-col1,
         html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-two-col2,
              html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-three-col1,
                   html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-three-col2,
                        html div#$optin_uuid div#$optin_css_id.columbine-container div.columbine-three-col3 {
                                 float: none;
                                 width: 100%;
                                 margin-right: 0;
                                 margin-top: 10px;
                             }

}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-column,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-two-col1,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-two-col2,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-three-col1,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-three-col2,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-three-col3 {
   float: none;
   width: 100%;
   margin-right: 0;
   margin-top: 10px;
}


html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.columbine-container div.columbine-column textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
}
CSS;

    }
}