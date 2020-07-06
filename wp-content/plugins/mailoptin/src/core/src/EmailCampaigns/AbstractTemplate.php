<?php

namespace MailOptin\Core\EmailCampaigns;

use MailOptin\Core\PluginSettings\Settings;
use WP_Post;
use MailOptin\Core\Admin\Customizer\EmailCampaign\Customizer;
use MailOptin\Core\Admin\Customizer\EmailCampaign\AbstractCustomizer;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

/**
 * @method string page_background_color()
 * @method string header_background_color()
 * @method string header_text_color()
 * @method string header_web_version_link_label()
 * @method string header_web_version_link_color()
 * @method string header_text()
 * @method string content_background_color()
 * @method string content_title_font_size()
 * @method string content_body_font_size()
 * @method string content_headline_color()
 * @method string content_text_color()
 * @method string content_ellipsis_button_background_color()
 * @method string content_ellipsis_button_text_color()
 * @method string content_alignment()
 * @method string content_ellipsis_button_alignment()
 * @method string content_ellipsis_button_label()
 * @method string content_remove_ellipsis_button()
 * @method string header_removal()
 * @method string footer_removal()
 * @method string footer_background_color()
 * @method string footer_text_color()
 * @method string footer_font_size()
 * @method string footer_copyright_line()
 * @method string footer_unsubscribe_link_color()
 */
abstract class AbstractTemplate extends AbstractCustomizer implements TemplateInterface
{
    /** @var int ID of email campaign. */
    public $email_campaign_id;

    /**
     * ID of email template.
     *
     * @param int $email_campaign_id
     */
    public function __construct($email_campaign_id)
    {
        // this line must not go under any conditional statement.
        $this->email_campaign_id = $email_campaign_id;

        $this->handles_default_customizer_values();

        $this->template_shortcodes();

        if ( ! empty($_REQUEST['mailoptin_email_campaign_id'])) {
            add_filter('mailoptin_email_campaign_customizer_page_settings', array($this, 'customizer_page_settings'));
            add_filter('mailoptin_email_campaign_customizer_header_settings', array($this, 'customizer_header_settings'));
            add_filter('mailoptin_email_campaign_customizer_content_settings', array($this, 'customizer_content_settings'));
            add_filter('mailoptin_email_campaign_customizer_footer_settings', array($this, 'customizer_footer_settings'));

            add_filter('mailoptin_template_customizer_page_controls', array($this, 'customizer_page_controls'), 10, 4);
            add_filter('mailoptin_template_customizer_header_controls', array($this, 'customizer_header_controls'), 10, 4);
            add_filter('mailoptin_template_customizer_content_controls', array($this, 'customizer_content_controls'), 10, 4);
            add_filter('mailoptin_template_customizer_footer_controls', array($this, 'customizer_footer_controls'), 10, 4);

            add_action('customize_preview_init', function () {

                if (Settings::instance()->switch_customizer_loader() != 'true') {
                    remove_all_actions('customize_preview_init');
                }

                $this->email_template_customizer_javascript();

                do_action('mo_email_campaign_customize_preview_init');

            }, -1);
        }

        parent::__construct($email_campaign_id);
    }

    /**
     * takes care of setting default customizer values for a template.
     */
    public function handles_default_customizer_values()
    {
        // loop over the array of template customizer default values with the key being part of the filter tag.
        foreach ($this->default_customizer_values() as $filter_tag => $filter_value) {
            // if the value of self::default_customizer_values() array is an array, do the needful.
            // not sure we will ever have a filter that needs this for customizer value though.
            if (is_array($filter_value)) {
                add_filter("mailoptin_{$filter_tag}_default", function ($val) use ($filter_value) {
                    foreach ($filter_value as $key => $value) {
                        $val[$key] = $value;
                    }
                });
            } // if not array, simply return the value in the filter hook function.
            else {
                add_filter("mailoptin_{$filter_tag}_default", function () use ($filter_value) {
                    return $filter_value;
                });
            }
        }
    }

    /**
     * Handles automagic call to get a customizer value.
     *
     * @param string $name template setting.
     *
     * @return string
     */
    public function __call($name, $arguments)
    {
        if ( ! method_exists($this, $name)) {
            return $this::get_customizer_value($name);
        }
    }

    /**
     * @return string
     */
    public function footer_description()
    {
        return apply_filters(
            'mailoptin_email_template_footer_description',
            nl2br($this->get_customizer_value('footer_description')),
            $this->email_campaign_id,
            $this
        );
    }

    /**
     * @return string
     */
    public function footer_unsubscribe_line()
    {
        return apply_filters(
            'mailoptin_email_template_footer_unsubscribe_line',
            nl2br($this->get_customizer_value('footer_unsubscribe_line')),
            $this->email_campaign_id,
            $this
        );
    }

    /**
     * @return string
     */
    public function footer_unsubscribe_link_label()
    {
        return apply_filters(
            'mailoptin_email_template_footer_unsubscribe_link_label',
            nl2br($this->get_customizer_value('footer_unsubscribe_link_label')),
            $this->email_campaign_id,
            $this
        );
    }

    /**
     * Shortcodes for the various template customizer values.
     */
    public function template_shortcodes()
    {
        add_shortcode('mo_page_background_color', function () {
            return $this->page_background_color();
        });
        add_shortcode('mo_header_background_color', function () {
            return $this->header_background_color();
        });
        add_shortcode('mo_header_text_color', function () {
            return $this->header_text_color();
        });
        add_shortcode('mo_header_web_version_link_label', function () {
            return $this->header_web_version_link_label();
        });
        add_shortcode('mo_header_web_version_link_color', function () {
            return $this->header_web_version_link_color();
        });
        add_shortcode('mo_header_text', function () {
            return $this->header_text();
        });
        add_shortcode('mo_header_logo_text', function () {
            return $this->get_template_logo_header_text();
        });
        add_shortcode('mo_content_background_color', function () {
            return $this->content_background_color();
        });
        add_shortcode('mo_content_title_font_size', function () {
            return $this->content_title_font_size();
        });
        add_shortcode('mo_content_body_font_size', function () {
            return $this->content_body_font_size();
        });
        add_shortcode('mo_content_headline_color', function () {
            return $this->content_headline_color();
        });
        add_shortcode('mo_content_text_color', function () {
            return $this->content_text_color();
        });
        add_shortcode('mo_content_ellipsis_button_background_color', function () {
            return $this->content_ellipsis_button_background_color();
        });
        add_shortcode('mo_content_ellipsis_button_text_color', function () {
            return $this->content_ellipsis_button_text_color();
        });
        add_shortcode('mo_content_alignment', function () {
            return $this->content_alignment();
        });
        add_shortcode('mo_content_ellipsis_button_alignment', function () {
            return $this->content_ellipsis_button_alignment();
        });
        add_shortcode('mo_content_ellipsis_button_label', function () {
            return $this->content_ellipsis_button_label();
        });
        add_shortcode('mo_footer_background_color', function () {
            return $this->footer_background_color();
        });
        add_shortcode('mo_footer_text_color', function () {
            return $this->footer_text_color();
        });
        add_shortcode('mo_footer_font_size', function () {
            return $this->footer_font_size();
        });
        add_shortcode('mo_footer_copyright_line', function () {
            return $this->footer_copyright_line();
        });
        add_shortcode('mo_footer_description', function () {
            return $this->footer_description();
        });
        add_shortcode('mo_footer_unsubscribe_line', function () {
            return $this->footer_unsubscribe_line();
        });
        add_shortcode('mo_footer_unsubscribe_link_label', function () {
            return $this->footer_unsubscribe_link_label();
        });
        add_shortcode('mo_footer_unsubscribe_link_color', function () {
            return $this->footer_unsubscribe_link_color();
        });
    }

    /**
     * All customization made to an email template automatically get populated here.
     */
    public function get_template_core_css()
    {
        $page_background_color = $this->page_background_color();

        $header_text_color             = $this->header_text_color();
        $header_background_color       = $this->header_background_color();
        $header_web_version_link_color = $this->header_web_version_link_color();

        $content_background_color                 = $this->content_background_color();
        $content_alignment                        = $this->content_alignment();
        $content_title_font_size                  = absint($this->content_title_font_size());
        $content_ellipsis_button_alignment        = $this->content_ellipsis_button_alignment();
        $content_ellipsis_button_background_color = $this->content_ellipsis_button_background_color();
        $content_ellipsis_button_text_color       = $this->content_ellipsis_button_text_color();
        $content_body_font_size                   = absint($this->content_body_font_size());
        $content_text_color                       = $this->content_text_color();
        $content_headline_color                   = $this->content_headline_color();

        $footer_removal                = ($this->footer_removal() === true) ? 'display:none' : '';
        $header_removal                = ($this->header_removal() === true) ? 'display:none' : '';
        $ellipsis_button_removal       = ($this->content_remove_ellipsis_button() === true) ? 'display:none' : '';
        $footer_background_color       = absint($this->footer_background_color());
        $footer_font_size              = absint($this->footer_font_size());
        $footer_text_color             = $this->footer_text_color();
        $footer_unsubscribe_link_color = $this->footer_unsubscribe_link_color();

        $css = <<<CSS
    .mo-page-bg-color {background-color: $page_background_color;}
    .mo-header-bg-color {background-color: $header_background_color;}
    .mo-header-text-color {color: $header_text_color;}
    .mo-header-web-version-color {color: $header_web_version_link_color;}
    .mo-content-background-color {background-color: $content_background_color;}
    .mo-content-title-font-size {font-size: {$content_title_font_size}px;}
    .mo-content-body-font-size {font-size: {$content_body_font_size}px;}
    .mo-content-text-color {color: $content_text_color !important;}
    .mo-content-headline-color {color: $content_headline_color !important;}
    .mo-content-button-background-color {background-color: $content_ellipsis_button_background_color;}
    .mo-content-remove-ellipsis-button {{$ellipsis_button_removal}}
    .mo-content-button-text-color {color: $content_ellipsis_button_text_color;}
    .mo-content-alignment {text-align: $content_alignment;}
    /* must be applied to a DIV so text-align can work for child button which follows where the text is align to */
    div.mo-content-button-alignment {text-align: $content_ellipsis_button_alignment; float: $content_ellipsis_button_alignment}
    /* two enclosing braces are used because the first is assumed to be indicating a variable name but we need {} for css.*/
    .mo-header-container {{$header_removal}}
    /* two enclosing braces are used because the first is assumed to be indicating a variable name but we need {} for css.*/
    .mo-footer-container {{$footer_removal}}
    .mo-footer-bg-color {background-color: $footer_background_color;}
    .mo-footer-text-color {color: $footer_text_color;}
    .mo-footer-font-size {font-size: {$footer_font_size}px;}
    .mo-footer-unsubscribe-link-color {color: $footer_unsubscribe_link_color;}
CSS;

        return apply_filters('mailoptin_email_template_core_style', $css, $this->email_campaign_id, $this);
    }

    /**
     * Enqueue template customizer JavaScript.
     *
     * @return mixed
     */
    public function email_template_customizer_javascript()
    {
        $template_name = ER::get_email_campaign_name($this->email_campaign_id);
        $template_name = preg_replace('/\s+/', '-', $template_name);

        wp_enqueue_script(
            "mailoptin-email-template-customizer-{$template_name}",
            MAILOPTIN_ASSETS_URL . 'js/admin/email-template-customizer.js',
            array('customize-preview', 'jquery'),
            false,
            true
        );

        wp_enqueue_script(
            "mailoptin-newsletter-tinymce-editor",
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/newsletter-tinymce-editor.js',
            array('jquery'),
            false,
            true
        );

        if ($this->get_script()) {
            wp_enqueue_script(
                "mailoptin-template-{$template_name}",
                $this->get_script(),
                array('customize-preview', 'jquery'),
                false,
                true
            );
        }

        $email_campaign_option_prefix = MO_EMAIL_CAMPAIGNS_WP_OPTION_NAME;
        wp_add_inline_script(
            "customize-preview",
            "var mailoptin_email_campaign_id = $this->email_campaign_id;
            var mailoptin_email_campaign_option_prefix = '$email_campaign_option_prefix'"
        );
    }

    /**
     * Return value of a template customizer setting.
     *
     * @param string $template_setting
     *
     * @return string
     */
    public function get_customizer_value($template_setting)
    {
        $default = isset($this->customizer_defaults[$template_setting]) ? $this->customizer_defaults[$template_setting] : '';

        return ER::get_customizer_value($this->email_campaign_id, $template_setting, $default);
    }


    /**
     * Return logo image url of a template.
     *
     * @return false|string
     */
    public function get_template_logo()
    {
        $logo_id = $this->get_customizer_value('header_logo');

        return wp_get_attachment_url($logo_id);
    }


    /**
     * Return logo image url of a template.
     *
     * @return false|string
     */
    public function get_template_logo_header_text()
    {
        $logo_alt = apply_filters(
            'mailoptin_email_template_logo_alt', \MailOptin\Core\site_title(),
            $this->email_campaign_id,
            $this
        );
        $logo_url = $this->get_template_logo();

        return ! $logo_url ? $this->header_text() : "<img src=\"$logo_url\" alt='$logo_alt'>";
    }

    protected function customizer_custom_css()
    {
        return ER::get_customizer_value($this->email_campaign_id, 'custom_css');

    }

    /**
     * Full HTML doctype markup preview of a template.
     *
     * @return string
     */
    public function get_preview_structure()
    {
        $title = ER::get_merged_customizer_value($this->email_campaign_id, 'email_campaign_title');

        ob_start();
        ?>
        <head>
            <title><?php echo $title; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
            <style type="text/css">
                <?php $template_style = apply_filters('mailoptin_email_template_css', $this->get_styles(), $this->email_campaign_id, $this); ?>
                <?php $custom_css_style = apply_filters('mailoptin_email_template_custom_css', $this->customizer_custom_css(), $this->email_campaign_id, $this); ?>
                <?php $combined_style = $template_style . "\r\n". $this->get_template_core_css() . "\r\n" . $custom_css_style; ?>
                <?php echo apply_filters('mailoptin_email_template_style', $combined_style, $this->email_campaign_id, $this); ?>
            </style>
            <?php do_action('mo_get_preview_structure_before_closing_head'); ?>
        </head>
        <body>
        <?php echo do_shortcode($this->get_body()); ?>
        <?php echo $this->branding_attribute(); ?>
        </body>
        <?php
        return ob_get_clean();
    }

    public function branding_attribute()
    {
        if ($this->get_customizer_value('remove_branding') === true) return;
        $remove_branding_text = __('Newsletter Powered by', 'mailoptin');
        $mailoptin_logo_url   = MAILOPTIN_ASSETS_URL . 'images/mailoptin-blue.png';
        $mailoptin_url        = 'https://mailoptin.io/?ref=email-branding';

        return <<<HTML
<table class="mo-powered-by-attribute" width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #ffffff; margin-left: auto; margin-right: auto; table-layout: auto !important" bgcolor="#ffffff">
    <tbody>
    <tr>
        <td class="footer-main-width" width="580" align="center" valign="top" style="width: 580px;">
            <div class="footer-max-main-width" align="center" style="margin-left: auto; margin-right: auto; max-width: 580px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                    <tr>
                        <td class="footer-layout" align="center" valign="top" style="padding: 16px 0px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody>

                                <tr>
                                    <td class="footer-text" align="center" valign="top" style="color: #5d5d5d; font-family: Verdana,Geneva,sans-serif; font-size: 12px; padding: 4px 0px;">
                                        <table width="220" cellpadding="0" cellspacing="0" border="0">
                                            <tbody>
                                            <tr>
                                                <td class="footer-cta-text" align="left" valign="top" style="color: #5d5d5d; font-family: Verdana,Geneva,sans-serif; font-size: 12px; padding-left: 52px;">
                                                    <a href="$mailoptin_url" style="color: #5d5d5d; text-decoration: none" target="_blank">$remove_branding_text</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <a rel="nofollow" href="$mailoptin_url" style="color: #5d5d5d; text-decoration: none" target="_blank">
                                                        <img alt="MailOptin - Best lead Generation, email list building & Newsletter WordPress Plugin." border="0" hspace="0" vspace="0" src="$mailoptin_logo_url" width="211">
                                                    </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
HTML;

    }

    /**
     * Customizer settings for template page.
     *
     * @param mixed $settings
     *
     * @return mixed
     */
    abstract public function customizer_page_settings($settings);

    /**
     * Customizer settings for template header.
     *
     * @param mixed $settings
     *
     * @return mixed
     */
    abstract public function customizer_header_settings($settings);

    /**
     * Customizer settings for template content.
     *
     * @param mixed $settings
     *
     * @return mixed
     */
    abstract public function customizer_content_settings($settings);

    /**
     * Customizer settings for template footer.
     *
     * @param mixed $settings
     *
     * @return mixed
     */
    abstract public function customizer_footer_settings($settings);

    // --------------------------------------------- Template customizer controls. --------------------------------------- //

    /**
     * Customizer controls for template page settings.
     *
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     *
     * @return mixed
     */
    abstract public function customizer_page_controls(
        $controls,
        $wp_customize,
        $option_prefix,
        $customizerClassInstance
    );

    /**
     * Customizer controls for template header settings.
     *
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     *
     * @return mixed
     */
    abstract public function customizer_header_controls(
        $controls,
        $wp_customize,
        $option_prefix,
        $customizerClassInstance
    );

    /**
     * Customizer controls for template content settings.
     *
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     *
     * @return mixed
     */
    abstract public function customizer_content_controls(
        $controls,
        $wp_customize,
        $option_prefix,
        $customizerClassInstance
    );

    /**
     * Customizer controls for template footer settings.
     *
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     *
     * @return mixed
     */
    abstract public function customizer_footer_controls(
        $controls,
        $wp_customize,
        $option_prefix,
        $customizerClassInstance
    );

}