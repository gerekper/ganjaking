<?php

namespace MailOptin\Core\OptinForms\Lightbox;

// Exit if accessed directly
if ( ! defined('ABSPATH')) exit;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Elegance extends AbstractOptinTheme
{
    public $optin_form_name = 'Elegance';

    public function __construct($optin_campaign_id)
    {
        // remove default closeIcon
        add_filter('mo_optin_campaign_icon_close', function ($val, $optin_class, $optin_type) {
            if ($optin_class == 'Elegance' && $optin_type == 'lightbox') $val = false;

            return $val;
        }, 10, 3);

        $this->init_config_filters([
            // -- default for design sections -- //
            [
                'name'        => 'mo_optin_form_width_default',
                'value'       => '600',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],
            [
                'name'        => 'mo_optin_form_background_color_default',
                'value'       => '#ffffff',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_border_color_default',
                'value'       => '#ffffff',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            // -- default for headline sections -- //
            [
                'name'        => 'mo_optin_form_headline_default',
                'value'       => __("Subscribe For Latest Updates", 'mailoptin'),
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_headline_font_color_default',
                'value'       => '#000000',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_headline_font_default',
                'value'       => 'Courgette',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            // -- default for description sections -- //
            [
                'name'        => 'mo_optin_form_description_font_default',
                'value'       => 'Titillium+Web',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_description_default',
                'value'       => $this->_description_content(),
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_description_font_color_default',
                'value'       => '#777777',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            // -- default for fields sections -- //
            [
                'name'        => 'mo_optin_form_name_field_color_default',
                'value'       => '#000',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_email_field_color_default',
                'value'       => '#000',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_submit_button_color_default',
                'value'       => '#ffffff',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_submit_button_background_default',
                'value'       => '#2785C8',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_submit_button_font_default',
                'value'       => 'Titillium+Web',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_name_field_font_default',
                'value'       => 'Palatino Linotype, Book Antiqua, serif',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_email_field_font_default',
                'value'       => 'Palatino Linotype, Book Antiqua, serif',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            // -- default for note sections -- //
            [
                'name'        => 'mo_optin_form_note_font_color_default',
                'value'       => '#000000',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_note_default',
                'value'       => '<em>' . __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin') . '</em>',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_note_font_default',
                'value'       => 'Titillium+Web',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_description_font_size_desktop_default',
                'value'       => 20,
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ],

            [
                'name'        => 'mo_optin_form_description_font_size_tablet_default',
                'value'       => 20,
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox'
            ]
        ]);

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', function () {
            return 'inline';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', function () {
            return 'center';
        });

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
        return __('Sign up to best of business news, informed analysis and opinions on what matters to you.', 'mailoptin');
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
[mo-optin-form-wrapper class="moEleganceModal"]
    [mo-close-optin class="moEleganceModalclose"][/mo-close-optin]
    [mo-optin-form-headline class="moElegance_header"]
    [mo-optin-form-description class="moElegance_description"]
    [mo-optin-form-error]
    [mo-optin-form-fields-wrapper]
        [mo-optin-form-name-field class="moEleganceModal_input_fields"]
        [mo-optin-form-email-field class="moEleganceModal_input_fields"]
        [mo-optin-form-custom-fields]
        [mo-mailchimp-interests]
        [mo-optin-form-submit-button class="moEleganceModal_button"]
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-cta-button class="moEleganceModal_button"]
    [mo-optin-form-note class="moElegance_note"]
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

        $image_asset_url = MAILOPTIN_OPTIN_THEMES_ASSETS_URL;

        $form_width = $this->get_customizer_value('form_width');

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.moEleganceModal {
  border: 3px solid #fff;
  width: 100%;
  max-width: {$form_width}px;
  position: relative;
  margin: auto;
  border-radius: 10px;
  background: #fff;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  padding: 1.5em 2.5em;
  box-shadow: 8px 0 20px 14px rgba(0, 0, 0, 0.08)
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal h2.moElegance_header {
  color: #000;
  margin: 0;
  line-height: 1.5;
  text-align: center;
  text-transform: capitalize
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .moElegance_description {
  line-height: 1.5;
  text-align: center;
  color: #777;
  margin-bottom: 2em;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .moElegance_note {
  line-height: 1.5;
  text-align: center;
  color: #000;
  margin-top: 10px
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal input.moEleganceModal_input_fields,
html div#$optin_uuid div#$optin_css_id.moEleganceModal input.mo-optin-form-custom-field.text-field,
html div#$optin_uuid div#$optin_css_id.moEleganceModal input.mo-optin-form-custom-field.password-field,
html div#$optin_uuid div#$optin_css_id.moEleganceModal input.mo-optin-form-custom-field.date-field,
html div#$optin_uuid div#$optin_css_id.moEleganceModal select.mo-optin-form-custom-field,
html div#$optin_uuid div#$optin_css_id.moEleganceModal textarea.mo-optin-form-custom-field.textarea-field {
  display: block;
  width: 100%;
  max-width: 100%;
  padding: 10px;
  margin: 0.5em auto 0;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  font-size: 16px;
  border: 1px solid #e3e3e3;
  outline: 1px solid #e3e3e3;
  background-color: #ffffff;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal input.moEleganceModal_button {
  display: block;
  margin: 10px auto 0;
  text-decoration: none;
  text-align: center;
  padding: 0.5em 0;
  font-size: 18px;
  text-transform: uppercase;
  background: #2785C8;
  color: #ffffff;
  line-height: normal;
  border: 0 none;
  border-radius: 0;
  width: 100%;
  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .moEleganceModal_button:hover {
  background: #52A9E7;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .moEleganceModal_button:active {
  background: #fff;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .moEleganceModalclose {
  position: absolute;
  right: 12px;
  top: 16px;
  width: 24px;
  height: 24px;
  background-repeat: no-repeat;
  cursor: pointer
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .mo-optin-error {
  display: none;
  background: #FF0000;
  color: #ffffff;
  text-align: center;
  padding: .2em;
  margin: 0 auto -9px;
  width: 100%;
  font-size: 16px;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box; 
  border: 1px solid #FF0000;
  outline: 1px solid #FF0000;
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal a.moEleganceModalclose {
  background-image: url({$image_asset_url}/elegance-close.png);
  font-size: 9px;
  margin: auto;
  display: block;
  text-align: center
}

html div#$optin_uuid div#$optin_css_id.moEleganceModal .mo-optin-fields-wrapper .list_subscription-field:not(select),
html div#$optin_uuid div#$optin_css_id.moEleganceModal .mo-optin-fields-wrapper .mo-optin-form-custom-field.checkbox-field,
html div#$optin_uuid div#$optin_css_id.moEleganceModal .mo-optin-fields-wrapper .mo-optin-form-custom-field.radio-field {
    margin-top: 0.5em;
}
CSS;

    }
}