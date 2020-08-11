<?php

namespace MailOptin\Core\OptinForms\Inpost;

use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class BareMetal extends AbstractOptinTheme
{
    public $optin_form_name = 'Bare Metal';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#f0f0f0',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#00cc77',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Get a little acid in your inbox", 'mailoptin'),
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#222222',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Glegoo',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Satisfy',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#222222',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#222222',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#0073b7',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Helvetica+Neue',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Consolas, Lucida Console, monospace',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Consolas, Lucida Console, monospace',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => '<em>' . __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin') . '</em>',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Source+Sans+Pro',
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 20,
                    'optin_class' => 'BareMetal',
                    'optin_type'  => 'inpost'
                ]
            ]
        );

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', function () {
            return '#000000';
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
        return '<p style="text-align: center;">Exclusive special offers that you won\'t ever find on our blog·</p>';
    }

    /**
     * Fulfil interface contract.
     */
    public function optin_script()
    {
    }

    /**
     * Template body.
     *
     * @return string
     */
    public function optin_form()
    {
        return <<<HTML
[mo-optin-form-wrapper class="mo-baremetal-container"]
    [mo-optin-form-headline]
    [mo-optin-form-description class="mo-baremetal-description"]
    [mo-optin-form-error]
    [mo-optin-form-fields-wrapper]
    [mo-optin-form-name-field]
    [mo-optin-form-email-field]
    [mo-optin-form-custom-fields]
    [mo-mailchimp-interests]
    [mo-optin-form-submit-button]
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-cta-button]
    [mo-optin-form-note class="mo-baremetal-note"]
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

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container {
text-align:center;
background: #f0f0f0;
border: 4px solid #dd3333;
border-radius: 5px;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
-webkit-border-radius: 5px;
-o-border-radius: 5px;
-moz-border-radius: 5px;
padding: 1.5em;
margin: 10px auto;
width: 100%;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container h2.mo-optin-form-headline {
color: #222222;
margin: 0 0 10px;
font-weight: bold;
text-align: center;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container .mo-baremetal-description {
color: #000000;
font-weight: normal;
line-height: 1.6;
margin-bottom: 20px;
text-rendering: optimizeLegibility;
}

html div#$optin_uuid div#$optin_css_id .mo-baremetal-note {
  color:#000000;
  margin: 5px auto;
  text-align: center;
  font-style: italic;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container #{$optin_css_id}_name_field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container #{$optin_css_id}_email_field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input.mo-optin-form-custom-field.text-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input.mo-optin-form-custom-field.password-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input.mo-optin-form-custom-field.date-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container select.mo-optin-form-custom-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container textarea.mo-optin-form-custom-field.textarea-field {
-webkit-border-radius: 0;
border-radius: 0;
background: #fff;
border: 1px solid #ccc;
-webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
margin: 0;
padding: 8px;
height: 37px;
width: 100%;
max-width: 100%;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
-webkit-transition: -webkit-box-shadow 0.45s, border-color 0.45s ease-in-out;
-moz-transition: -moz-box-shadow 0.45s, border-color 0.45s ease-in-out;
transition: box-shadow 0.45s, border-color 0.45s ease-in-out;
-webkit-transition: all 0.15s linear;
-moz-transition: all 0.15s linear;
-o-transition: all 0.15s linear;
font-size: 16px;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container #{$optin_css_id}_name_field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container #{$optin_css_id}_email_field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input.mo-optin-form-custom-field.text-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input.mo-optin-form-custom-field.password-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input.mo-optin-form-custom-field.date-field,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container textarea.mo-optin-form-custom-field.textarea-field {
-webkit-appearance: none;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input[type="submit"].mo-optin-form-submit-button,
html div#$optin_uuid div#$optin_css_id.mo-baremetal-container input[type="submit"].mo-optin-form-cta-button {
border: none;
line-height: normal;
letter-spacing: normal;
margin: 16px 0 0;
position: relative;
text-decoration: none;
text-align: center;
text-transform: uppercase;
text-shadow: none;
box-shadow: none;
height: auto;
min-width: initial;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
outline: 0;
display: inline-block;
padding: 16px 32px 17px;
font-size: 16px;
background: #0073b7;
color: #ffffff;
-webkit-transition: background-color 1s;
-moz-transition: background-color 1s;
-o-transition: background-color 1s;
transition: background-color 1s;
width: 100%;
-webkit-border-radius: 3px;
border-radius: 3px;
float: initial;
cursor: pointer;
font-weight: 700;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container .mo-optin-error {
display: none;
background: #FF0000;
color: #ffffff;
text-align: center;
padding: .2em;
margin: 0;
width: 100%;
font-size: 16px;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box; 
}

html div#$optin_uuid div#$optin_css_id ul {
    margin: 0 0 1.6em 1.3333em;
}

html div#$optin_uuid div#$optin_css_id.mo-baremetal-container .mo-optin-fields-wrapper .list_subscription-field:not(select) {
    margin-top: 5px;
}
CSS;

    }
}