<?php

namespace MailOptin\Core\OptinForms;


use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use function MailOptin\Core\moVar;

abstract class AbstractOptinTheme extends AbstractOptinForm
{
    public function __construct($optin_campaign_id)
    {
        add_shortcode('mo-optin-form-wrapper', [$this, 'shortcode_optin_form_wrapper']);
        add_shortcode('mo-optin-form-fields-wrapper', [$this, 'shortcode_optin_form_fields_wrapper']);
        add_shortcode('mo-optin-form-cta-wrapper', [$this, 'shortcode_optin_form_cta_wrapper']);
        add_shortcode('mo-optin-form-headline', [$this, 'shortcode_optin_form_headline']);
        add_shortcode('mo-close-optin', [$this, 'shortcode_optin_form_close_button']);
        add_shortcode('mo-optin-form-image', [$this, 'shortcode_optin_form_image']);
        add_shortcode('mo-optin-form-background-image', [$this, 'shortcode_optin_form_background_image']);
        add_shortcode('mo-optin-form-description', [$this, 'shortcode_optin_form_description']);
        add_shortcode('mo-optin-form-error', [$this, 'shortcode_optin_form_error']);
        add_shortcode('mo-optin-form-name-field', [$this, 'shortcode_optin_form_name_field']);
        add_shortcode('mo-optin-form-email-field', [$this, 'shortcode_optin_form_email_field']);
        add_shortcode('mo-optin-form-custom-fields', [$this, 'shortcode_optin_form_custom_fields']);
        add_shortcode('mo-optin-form-submit-button', [$this, 'shortcode_optin_form_submit_button']);
        add_shortcode('mo-optin-form-cta-button', [$this, 'shortcode_optin_form_cta_button']);
        add_shortcode('mo-optin-form-note', [$this, 'shortcode_optin_form_note']);

        do_action('mo_optin_theme_shortcodes_add', $optin_campaign_id);

        parent::__construct($optin_campaign_id);
    }

    public function video_embed_html($html)
    {
        return '<div class="mailoptin-video-container">' . $html . '</div>';
    }

    public function sanitize_font_stack_family($font)
    {
        $system_fonts = ControlsHelpers::get_system_font_stack();

        if ( ! in_array($font, $system_fonts)) {
            // for some odd reasons, self::_construct_font_family didn't work correctly here.
            // don't ever switched to _construct_font_family in future.
            $font = self::_replace_plus_with_space($font);
        }

        return $font;
    }

    public function embed_shortcode_parser($body)
    {
        global $wp_embed;

        add_filter('embed_oembed_html', [$this, 'video_embed_html'], 10, 3);
        add_filter('video_embed_html', [$this, 'video_embed_html']);
        add_filter('wp_video_shortcode', [$this, 'video_embed_html']);

        // this has to come before do_shortcode.
        $content = $wp_embed->run_shortcode($body);
        $content = do_shortcode($content);

        remove_filter('embed_oembed_html', [$this, 'video_embed_html'], 10);
        remove_filter('video_embed_html', [$this, 'video_embed_html']);
        remove_filter('wp_video_shortcode', [$this, 'video_embed_html']);

        return $content;
    }

    /**
     * Headline styles.
     *
     * @return string
     */
    public function headline_styles()
    {
        $font_family = $this->get_customizer_value('headline_font');

        $style_arg = ['color' => $this->get_customizer_value('headline_font_color')];

        if ($font_family != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style_arg['font-family'] = $this->_construct_font_family($font_family);
        }

        if ($this->get_customizer_value('hide_headline')) {
            $style_arg['display'] = 'none';
        }

        $style_arg = apply_filters('mo_optin_form_headline_styles', $style_arg,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        $style = '';

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Description styles.
     *
     * @return string
     */
    public function description_styles()
    {
        $font = $this->get_customizer_value('description_font');

        $style_arg = ['color' => $this->get_customizer_value('description_font_color')];

        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style_arg['font-family'] = $this->_construct_font_family($font);
        }

        if ($this->get_customizer_value('hide_description')) {
            $style_arg['display'] = 'none';
        }

        $style_arg = apply_filters('mo_optin_form_description_styles', $style_arg,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        $style = '';
        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Note styles.
     *
     * @return string
     */
    public function note_styles()
    {
        $font = $this->get_customizer_value('note_font');

        $style = ['color' => $this->get_customizer_value('note_font_color')];

        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style['font-family'] = $this->_construct_font_family($font);
        }

        if ($this->get_customizer_value('note_close_optin_onclick')) {
            $style['text-decoration'] = 'underline';
            $style['cursor']          = 'pointer';
        }

        if ($this->get_customizer_value('hide_note')) {
            $style['display'] = 'none';
        }

        $style_arg = apply_filters('mo_optin_form_note_styles', $style, $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid);

        $style = '';

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Name field styles.
     *
     * @return string
     */
    public function name_field_styles()
    {
        $font = $this->sanitize_font_stack_family(
            $this->get_customizer_value('name_field_font')
        );

        $style_arg = [
            'color'            => $this->get_customizer_value('name_field_color'),
            'background-color' => $this->get_customizer_value('name_field_background'),
            'height'           => 'auto'
        ];

        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style_arg['font-family'] = $font;
        }

        if ($this->get_customizer_value('hide_name_field')) {
            $style_arg['display'] = 'none';
        }


        $style_arg = apply_filters('mo_optin_form_name_field_styles', $style_arg,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        $style = '';

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Email field styles.
     *
     * @return string
     */
    public function email_field_styles()
    {
        $font = $this->sanitize_font_stack_family(
            $this->get_customizer_value('email_field_font')
        );

        $style_arg = [
            'color'            => $this->get_customizer_value('email_field_color'),
            'background-color' => $this->get_customizer_value('email_field_background'),
            'height'           => 'auto'
        ];


        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style_arg['font-family'] = $font;
        }


        $style = '';

        $style_arg = apply_filters('mo_optin_form_email_field_styles', $style_arg,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Custom field styles.
     *
     * @return string
     */
    public function custom_field_styles($field_saved_values)
    {
        $font_family = ! isset($field_saved_values['font']) || empty($field_saved_values['font']) ? 'inherit' : sanitize_text_field($field_saved_values['font']);
        $font        = $this->sanitize_font_stack_family($font_family);

        $style_arg = ['height' => 'auto'];

        if ( ! empty($field_saved_values['color'])) {
            $style_arg['color'] = sanitize_text_field($field_saved_values['color']);
        }

        if ( ! empty($field_saved_values['background'])) {
            $style_arg['background-color'] = sanitize_text_field($field_saved_values['background']);
        }


        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style_arg['font-family'] = $font;
        }


        $style = '';

        $style_arg = apply_filters('mo_optin_form_custom_field_styles', $style_arg,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Submit button styles.
     *
     * @return string
     */
    public function submit_button_styles()
    {
        $font = $this->get_customizer_value('submit_button_font');

        $style_arg = [
            'background'  => $this->get_customizer_value('submit_button_background'),
            'color'       => $this->get_customizer_value('submit_button_color'),
            'height'      => 'auto',
            'text-shadow' => 'none'
        ];

        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style_arg['font-family'] = $this->_construct_font_family($font);
        }

        $style_arg = apply_filters('mo_optin_form_submit_button_styles', $style_arg,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        $style = '';

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * CTA button styles.
     *
     * @return string
     */
    public function cta_button_styles()
    {
        $font = $this->get_customizer_value('cta_button_font');

        $style = [
            'background'  => $this->get_customizer_value('cta_button_background'),
            'color'       => $this->get_customizer_value('cta_button_color'),
            'height'      => 'auto',
            'text-shadow' => 'none'
        ];

        if ($font != 'inherit' && ! self::is_set_font_families_to_inherit()) {
            $style['font-family'] = $this->_construct_font_family($font);
        }

        if ($this->get_customizer_value('display_only_button') !== true) {
            $style['display'] = 'none';
        }

        $style_arg = apply_filters('mo_optin_form_cta_button_styles', $style,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );


        $style = '';

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Form container styles.
     *
     * @return string
     */
    public function form_container_styles()
    {
        $style_arg = apply_filters('mo_optin_form_container_styles',
            [
                'position'     => 'relative',
                'margin-right' => 'auto',
                'margin-left'  => 'auto',
                'background'   => $this->get_customizer_value('form_background_color'),
                'border-color' => $this->get_customizer_value('form_border_color'),
                'line-height'  => 'normal'
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        if ($this->optin_campaign_type == 'slidein') {
            $style_arg['margin-right'] = '0';
            $style_arg['margin-left']  = '0';
        }

        $style = '';

        foreach ($style_arg as $key => $value) {
            if ( ! empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Optin form wrapper shortcode.
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function shortcode_optin_form_wrapper($atts, $content)
    {
        $optin_campaign_uuid        = $this->optin_campaign_uuid;
        $optin_css_id               = $this->optin_css_id;
        $form_container_styles      = $this->form_container_styles();
        $name_email_class_indicator = $this->get_customizer_value('hide_name_field') === true ? 'mo-has-email' : 'mo-has-name-email';

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-wrapper $name_email_class_indicator $class";

        $style = esc_html($atts['style']);
        $style = "$form_container_styles $style";

        $html = "<div id=\"$optin_css_id\" class=\"$class\" style=\"$style\">";
        $html .= apply_filters('mo_optin_form_before_form_tag', '', $this->optin_campaign_id, $this->optin_campaign_type, $optin_campaign_uuid, $optin_css_id);

        if ( ! $this->get_customizer_value('use_custom_html')) {
            $html .= "<form method=\"post\" class='mo-optin-form' id='{$optin_css_id}_form' style='margin:0;'>";
        } else {
            // remove text alignment to center set in lightbox modal div container.
            $html .= "<style type=\"text/css\">#{$optin_campaign_uuid}.moOptinForm.moModal{text-align:initial !important;}</style>";
        }

        $html .= do_shortcode($content);

        // Don't change type from text to email to prevent "An invalid form control with name='text' is not focusable." error
        $html .= "<input id='{$this->optin_css_id}_honeypot_email_field' type='text' name='mo-hp-email' value='' style='display:none'/>";
        $html .= '<input id="' . $this->optin_css_id . '_honeypot_website_field" type="text" name="mo-hp-website" value="" style="display:none" />';

        $html .= apply_filters('mo_optin_form_before_closing_form_tag', '', $this->optin_campaign_id, $this->optin_campaign_type, $optin_campaign_uuid, $optin_css_id);
        if ( ! $this->get_customizer_value('use_custom_html')) {
            $html .= '</form>';
        }
        $html .= apply_filters('mo_optin_form_after_form_tag', '', $this->optin_campaign_id, $this->optin_campaign_type, $optin_campaign_uuid, $optin_css_id);

        $html .= $this->processing_success_structure();

        if ( ! $this->customizer_defaults['mo_optin_branding_outside_form'] && $this->optin_campaign_type != 'lightbox') {
            $html .= $this->branding_attribute();
        }

        $html .= "</div>";

        if ($this->customizer_defaults['mo_optin_branding_outside_form'] || $this->optin_campaign_type == 'lightbox') {
            $html .= $this->branding_attribute();
        }

        return $html;
    }

    /**
     * Optin form fields wrapper shortcode.
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function shortcode_optin_form_fields_wrapper($atts, $content)
    {
        if ($this->get_customizer_value('use_custom_html')) {
            return do_shortcode($this->get_customizer_value('custom_html_content'));
        }

        $atts = shortcode_atts(
            array(
                'tag'   => 'div',
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $tag = $atts['tag'];

        $class = '';
        if ( ! empty($atts['class'])) {
            $class = ' ' . esc_attr($atts['class']);
        }

        $class = "mo-optin-fields-wrapper{$class}";

        $style = '';
        if ($this->get_customizer_value('display_only_button')) {
            $style .= 'display:none;';
        }

        $style .= esc_attr($atts['style']);

        $html = "<$tag class=\"$class\" style=\"$style\">";
        $html .= do_shortcode($content);
        $html .= "</$tag>";

        return $html;
    }

    /**
     * Optin form fields wrapper shortcode.
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function shortcode_optin_form_cta_wrapper($atts, $content)
    {
        $atts = shortcode_atts(
            array(
                'tag'   => 'div',
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $tag = $atts['tag'];

        $class = ' ' . esc_attr($atts['class']);
        $class = "mo-optin-form-cta-wrapper{$class}";

        $style = '';
        if ( ! OptinCampaignsRepository::is_cta_button_active($this->optin_campaign_id)) {
            $style .= 'display:none;';
        }

        $style .= esc_attr($atts['style']);

        $html = "<$tag class=\"$class\" style=\"$style\">";
        $html .= do_shortcode($content);
        $html .= "</$tag>";

        return $html;
    }

    /**
     * HTML markup for processing and success div.
     * @return string
     */
    public function processing_success_structure()
    {
        $style = 'display:none';

        // what this does is hide cause the spinner and success message div not to be hidden by overriding the display:none style rule above.
        // the background-image: none; basically remove the spinner gif.
        if ( ! is_customize_preview() && OptinCampaignsRepository::user_has_successful_optin($this->optin_campaign_uuid) && $this->state_after_conversion() != 'optin_form_shown') {
            $style = 'background-image: none;'; //note: "bg none css rule" is basically only useful in processing/spinner div.
        }

        // processing / spinner div
        $html = apply_filters('mo_optin_processing_structure', "<div class='mo-optin-spinner' style='$style'></div>", $this->optin_campaign_uuid, $this->optin_css_id);

        // success div
        $html .= apply_filters(
            'mo_optin_success_structure',
            '<div class="mo-optin-success-msg" style="' . $style . '">' . $this->get_customizer_value('success_message') . '</div>',
            $this->optin_campaign_uuid,
            $this->optin_css_id
        );

        return $html;
    }

    /**
     * Plugin attribution link.
     *
     * @return mixed
     */
    public function branding_attribute()
    {
        if ($this->get_customizer_value('remove_branding') === true) {
            return '';
        }

        $affiliate_url = trim(Settings::instance()->mailoptin_affiliate_url());
        $mailoptin_url = 'https://mailoptin.io/?ref=optin-branding';

        if ( ! empty($affiliate_url)) $mailoptin_url = $affiliate_url;

        if ($this->optin_campaign_type == 'lightbox') {
            return apply_filters(
                'mailoptin_branding_attribute',
                '<div class="mo-optin-powered-by mailoptin-powered-by">' . __('Powered by', 'mailoptin') . ' <a rel="nofollow" href="' . $mailoptin_url . '" title="MailOptin" style="color:#fff;font-weight:700;text-decoration:underline;" target="_blank">MailOptin</a></div>'
            );
        }

        return apply_filters(
            'mailoptin_branding_attribute',
            "<div class=\"mo-optin-powered-by\" style='display:block !important;visibility:visible !important;position:static !important;top: 0 !important;left: 0 !important;text-align: center !important;height: auto !important;width: 60px !important;overflow: visible !important;opacity:1 !important;text-indent: 0 !important;clip: auto !important;clip-path: none !important;box-shadow:none !important;line-height:normal'>" .
            "<a rel=\"nofollow\" style='display:block !important;visibility:visible !important;position:static !important;top: 0 !important;left: 0 !important;text-align: center !important;height: auto !important;width: 60px !important;overflow: visible !important;opacity:1 !important;text-indent: 0 !important;clip: auto !important;clip-path: none !important;box-shadow:none !important' target=\"_blank\" href=\"$mailoptin_url\">" .
            '<img src="' . MAILOPTIN_ASSETS_URL . 'images/mo-optin-brand.png" style="height:auto !important;width:60px !important;display:inline !important;visibility:visible !important;position:static !important;top: 0 !important;left: 0 !important;text-align: center !important;overflow: visible !important;opacity:1 !important;text-indent: 0 !important;clip: auto !important;clip-path: none !important;box-shadow:none !important"/>' .
            '</a></div>'
        );
    }

    /**
     * Optin form headline shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_headline($atts)
    {
        $headline = apply_filters('mo_optin_form_before_headline', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $headline .= $this->get_customizer_value('headline');
        $headline .= apply_filters('mo_optin_form_after_headline', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        $headline = $this->embed_shortcode_parser($headline);

        $headline_styles = $this->headline_styles();

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
                'tag'   => 'h2',
            ),
            $atts
        );

        $tag = sanitize_text_field($atts['tag']);

        $class = "mo-optin-form-headline " . esc_attr($atts['class']);

        $style = esc_attr($atts['style']);
        $style = "$headline_styles $style";

        if ($atts['tag'] == 'h2') {
            $style .= "padding: 0;";
        }

        $html = "<$tag class=\"$class\" style=\"$style\">$headline</$tag>";

        return $html;
    }

    /**
     * Optin form close button
     *
     * @param array $atts
     * @param mixed $content
     */
    public function shortcode_optin_form_close_button($atts, $content = '')
    {
        if ($this->get_customizer_value('hide_close_button')) return '';

        $atts = array_map('esc_attr', shortcode_atts(
                array(
                    'class' => '',
                    'style' => '',
                ),
                $atts
            )
        );

        $title = __('Close optin form', 'mailoptin');

        $class = "mo-optin-form-close-icon " . esc_attr($atts['class']);

        $close_button = "<a href='#' rel=\"moOptin:close\" title=\"$title\" class=\"$class\" style='" . $atts['style'] . "'>";
        $close_button .= $content;
        $close_button .= '</a>';

        return $close_button;
    }

    /**
     * Optin form headline shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_image($atts)
    {
        if ($this->get_customizer_value('hide_form_image', false)) return '';

        $atts = shortcode_atts(
            array(
                'default'         => '',
                'style'           => '',
                'wrapper_enabled' => false,
                'wrapper_tag'     => 'div',
                'wrapper_class'   => '',
            ),
            $atts
        );

        $style = esc_attr($atts['style']);

        $src = $this->get_form_image_url($atts['default']);

        $wrapper_class = ' ' . $atts['wrapper_class'];
        $tag           = $atts['wrapper_tag'];

        $html = '';
        if ($atts['wrapper_enabled'] == 'true') {
            $html .= sprintf('<%s class="mo-optin-form-image-wrapper%s">', $tag, $wrapper_class);
        }

        $html .= sprintf('<img src="%s" class="mo-optin-form-image" style="%s"/>', esc_url_raw($src), $style);

        if ($atts['wrapper_enabled'] == 'true') {
            $html .= "</$tag>";
        }

        return $html;
    }

    /**
     * Optin form headline shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_background_image($atts)
    {
        $atts = shortcode_atts(
            array(
                'default' => '',
                'style'   => '',
            ),
            $atts
        );

        $style = esc_attr($atts['style']);

        $src = $this->get_form_background_image_url($atts['default_image']);

        return sprintf('<img src="%s" class="mo-optin-form-background-image" style="%s"/>', esc_url($src), $style);
    }

    /**
     * Get form_image URL.
     *
     * @param string $default_image_url
     *
     * @return bool|false|string
     */
    public function get_form_image_url($default_image_url = '')
    {
        return $this->get_attachment_image_url('form_image', $default_image_url);
    }

    /**
     * Get form_background_image URL.
     *
     * @param string $default_image_url
     *
     * @return bool|false|string
     */
    public function get_form_background_image_url($default_image_url = '')
    {
        $bg_image_url = $this->get_customizer_value('form_background_image');

        return ! empty($bg_image_url) ? $bg_image_url : $default_image_url;
    }

    /**
     * Get attachment image url from customize image control powered setting.
     *
     * @param string $optin_form_setting
     * @param string $default_image_url
     *
     * @return bool|false|string
     */
    public function get_attachment_image_url($optin_form_setting, $default_image_url = '')
    {
        $db_image_attachment_id = $this->get_customizer_value($optin_form_setting);

        // you could use wp_get_attachment_image_url($db_saved_form_image, '');
        // instead where second param is empty string thus returning full image url.
        return ! empty($db_image_attachment_id) ? wp_get_attachment_url($db_image_attachment_id) : $default_image_url;
    }

    /**
     * Optin form description shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_description($atts)
    {
        $description = apply_filters('mo_optin_form_before_description', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $description .= $this->get_customizer_value('description');
        $description .= apply_filters('mo_optin_form_after_description', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        $description = $this->embed_shortcode_parser($description);

        $description_styles = $this->description_styles();

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-description $class";

        $style = esc_attr($atts['style']);
        $style = "$description_styles $style";

        $html = "<div class=\"$class\" style=\"$style\">$description</div>";

        return $html;
    }

    /**
     * Optin form note shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_note($atts)
    {
        $note                          = apply_filters('mo_optin_form_before_note', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $is_acceptance_checkbox_active = $this->get_customizer_value('note_acceptance_checkbox');

        $note .= '<span class="mo-note-content">' . $this->get_customizer_value('note') . '</span>';

        if ($is_acceptance_checkbox_active) {
            $note .= '</label>';
        }

        $note .= apply_filters('mo_optin_form_after_note', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        $note = do_shortcode($note);

        $note_styles = $this->note_styles();

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);

        if ($this->get_customizer_value('note_close_optin_onclick')) {
            $class .= ' mo-close-optin';
        }

        $class = "mo-optin-form-note $class";

        $style = $note_styles . esc_attr($atts['style']);

        $html = '';

        if ($is_acceptance_checkbox_active) {
            $html .= '<label class="mo-acceptance-label">';
            $html .= '<input name="mo-acceptance" class="mo-acceptance-checkbox" type="checkbox" value="yes">';
        }

        $html .= str_replace(
            ['<p', '</p', '<div', '</div'],
            ['<span', '</span', '<span', '</span'],
            $note
        );

        if ($is_acceptance_checkbox_active) {
            $html .= '</label>';
        }

        return "<div class=\"$class\" style=\"$style\">$html</div>";
    }

    /**
     * Optin form name field shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_name_field($atts)
    {
        $optin_css_id           = $this->optin_css_id;
        $name_field_styles      = $this->name_field_styles();
        $name_field_placeholder = $this->get_customizer_value('name_field_placeholder');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-field mo-optin-form-name-field $class";

        $style = esc_attr($atts['style']);
        $style = "$name_field_styles $style";

        $value = '';
        if ($this->get_customizer_value('prefill_logged_user_data')) {
            $cache = wp_get_current_user();
            if ( ! empty($cache->first_name) || ! empty($cache->last_name)) {
                $value = $cache->first_name . ' ' . $cache->last_name;
            }
        }

        $html = apply_filters('mo_optin_form_before_form_name_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_name_field\" class=\"$class\" style='$style' type=\"text\" placeholder=\"$name_field_placeholder\" name=\"mo-name\" autocomplete=\"on\" value=\"$value\">";
        $html .= apply_filters('mo_optin_form_after_form_name_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form email field shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_email_field($atts)
    {
        $optin_css_id            = $this->optin_css_id;
        $email_field_styles      = $this->email_field_styles();
        $email_field_placeholder = $this->get_customizer_value('email_field_placeholder');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-field mo-optin-form-email-field $class";

        $style = esc_attr($atts['style']);
        $style = "$email_field_styles $style";

        $value = '';
        if ($this->get_customizer_value('prefill_logged_user_data')) {
            $email = wp_get_current_user()->user_email;
            if ( ! empty($email)) {
                $value = wp_get_current_user()->user_email;
            }
        }

        $html = apply_filters('mo_optin_form_before_form_name_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_email_field\" class=\"$class\" style=\"$style\" type=\"email\" placeholder=\"$email_field_placeholder\" name=\"mo-email\" autocomplete=\"on\" value=\"$value\">";
        $html .= apply_filters('mo_optin_form_after_form_email_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin custom fields shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_custom_fields($atts)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        $atts = shortcode_atts(
            array(
                'class'     => '',
                'style'     => '',
                'tag_start' => '',
                'tag_end'   => ''
            ),
            $atts
        );

        $saved_values = $this->get_customizer_value('fields');

        $html = '';

        if ( ! empty($saved_values) && is_string($saved_values)) {
            $result = json_decode($saved_values, true);
            if (is_array($result)) {
                $saved_values = $result;

                foreach ($saved_values as $index => $field) {
                    $optin_css_id = $this->optin_css_id;
                    $field_type   = empty($field['field_type']) ? 'text' : sanitize_text_field($field['field_type']);
                    $field_styles = $this->custom_field_styles($field);

                    $field_id    = sanitize_text_field($field['cid']);
                    $placeholder = isset($field['placeholder']) ? sanitize_text_field($field['placeholder']) : '';

                    $style = esc_attr($atts['style']);
                    $style = "$field_styles $style";

                    $id = "{$optin_css_id}_{$field_type}_{$field_id}";

                    $options = [];
                    if ( ! empty($field['field_options'])) {
                        $options = array_map('trim', explode(',', $field['field_options']));
                        $options = array_filter($options, function ($field) {
                            return ! empty($field);
                        });
                    }

                    $class = ' ' . esc_attr($atts['class']);
                    $class = "mo-optin-field mo-optin-form-custom-field {$field_type}-field field-{$field_id}{$class}";

                    $data_attr = sprintf('data-field-id="%s"', $field_id);

                    $html .= apply_filters('mo_optin_form_custom_field_output', '', $field_type, $field, $atts);

                    switch ($field_type) {
                        case 'text':
                        case 'date':
                            $html .= $atts['tag_start'];
                            $html .= "<input $data_attr id=\"$id\" class=\"$class\" style=\"$style\" type=\"text\" placeholder=\"$placeholder\" name=\"$field_id\">";
                            $html .= $atts['tag_end'];
                            break;
                        case 'hidden':
                            $value = moVar($field, 'hidden_value', '', true);
                            $html  .= $atts['tag_start'];
                            $html  .= "<input $data_attr id=\"$id\" class=\"$class\" style=\"display:none\" type=\"hidden\" value=\"$value\" name=\"$field_id\">";
                            $html  .= $atts['tag_end'];
                            break;
                        case 'password':
                            $html .= $atts['tag_start'];
                            $html .= "<input $data_attr id=\"$id\" class=\"$class\" style=\"$style\" type=\"password\" placeholder=\"$placeholder\" name=\"$field_id\">";
                            $html .= $atts['tag_end'];
                            break;
                        case 'textarea':
                            $html .= $atts['tag_start'];
                            $html .= "<textarea $data_attr id=\"$id\" class=\"$class\" style=\"$style\" placeholder=\"$placeholder\" name=\"$field_id\"></textarea>";
                            $html .= $atts['tag_end'];
                            break;
                        case 'checkbox':
                            $html .= $atts['tag_start'];
                            $html .= "<div $data_attr class=\"$class\" style=\"$style\" id=\"$id\">";
                            if (count($options) < 2) {
                                $value = empty($options) ? 'true' : esc_attr(trim($options[0]));
                                $html  .= "<input type=\"hidden\" value=\"false\" name=\"{$field_id}\"><label><input type=\"checkbox\" value=\"$value\" name=\"{$field_id}\"><span>$placeholder</span></label>";
                            } else {
                                $html .= "<div class='mo-checkbox-title'>$placeholder</div>";
                                foreach ($options as $option) {
                                    $option = esc_attr(trim($option));
                                    $html   .= "<label><input type=\"checkbox\" value=\"$option\" name=\"{$field_id}[]\"><span>$option</span></label>";
                                }
                            }
                            $html .= '</div>';
                            $html .= $atts['tag_end'];
                            break;
                        case 'radio':
                            $html .= $atts['tag_start'];
                            $html .= "<div $data_attr class=\"$class\" id=\"$id\" style=\"$style\"><div class='mo-radio-title'>$placeholder</div>";

                            //Display options
                            foreach ($options as $option) {
                                $option = esc_attr(trim($option));
                                if (empty ($option)) {
                                    continue;
                                }
                                $html .= "<label><input type=\"radio\" value=\"$option\" name=\"$field_id\"><span>$option</span></label>";
                            }
                            $html .= "</div>" . $atts['tag_end'];
                            break;
                        case 'select':
                            $placeholder = ! empty($placeholder) ? $placeholder : esc_html__('Select...', 'mailoptin');
                            $html        .= $atts['tag_start'];
                            $html        .= "<select name=\"$field_id\" $data_attr class=\"$class\" id=\"$id\" style=\"$style\">";
                            $html        .= "<option value='' selected='selected'>$placeholder</option>";
                            //Display options
                            foreach ($options as $option) {
                                $option = esc_attr(trim($option));
                                if (empty ($option)) {
                                    continue;
                                }
                                $html .= "<option value=\"$option\" >$option</option>";
                            }
                            $html .= "</select>" . $atts['tag_end'];
                            break;
                        case 'country':

                            $display_type = moVar($field, 'country_field_options', 'alpha-2', true);

                            $html .= $atts['tag_start'];

                            switch ($display_type) {
                                case 'alpha-2':
                                    $countries_lists = \MailOptin\Core\countries_array();
                                    if (is_array($countries_lists) && ! empty($countries_lists)) {
                                        $placeholder = ! empty($placeholder) ? $placeholder : esc_html__('Select...', 'mailoptin');
                                        $html        .= "<select name=\"$field_id\" $data_attr class=\"$class\" id=\"$id\" style=\"$style\">";
                                        $html        .= "<option value='' selected='selected'>$placeholder</option>";

                                        foreach ($countries_lists as $key => $countries_list) {
                                            $html .= sprintf('<option value="%1$s">%2$s</option>', $key, $countries_list);
                                        }
                                        $html .= '</select>';
                                    }
                                    break;
                                case 'alpha-3':
                                    $countries_lists = \MailOptin\Core\countries_array('alpha-3');
                                    if (is_array($countries_lists) && ! empty($countries_lists)) {
                                        $placeholder = ! empty($placeholder) ? $placeholder : esc_html__('Select...', 'mailoptin');
                                        $html        .= "<select name=\"$field_id\" $data_attr class=\"$class\" id=\"$id\" style=\"$style\">";
                                        $html        .= "<option value='' selected='selected'>$placeholder</option>";

                                        foreach ($countries_lists as $key => $countries_list) {
                                            $html .= sprintf('<option value="%1$s">%2$s</option>', $key, $countries_list);
                                        }
                                        $html .= '</select>';
                                    }
                                    break;
                            }
                            $html .= $atts['tag_end'];
                            break;
                        case 'list_subscription':
                            $display_type = moVar($field, 'list_subscription_display_type', 'checkbox', true);
                            $integration  = moVar($field, 'list_subscription_integration');

                            $name_attribute = 'mo-list-subscription';

                            $integration_email_list = ConnectionsRepository::connection_email_list($integration);

                            if (empty($integration) || empty($integration_email_list)) return '';

                            $options         = moVar($field, 'list_subscription_lists', [], true);
                            $alignment_style = sprintf("text-align: %s;", moVar($field, 'list_subscription_alignment', 'left', true));
                            $style           .= $alignment_style;

                            $html .= $atts['tag_start'];
                            $html .= sprintf('<input type="hidden" name="mo-list-subscription-integration" value="%s">', $integration);
                            switch ($display_type) {
                                case 'checkbox':
                                    if (is_array($options) && ! empty($options)) {
                                        $html .= sprintf('<div %s class="%s" style="%s" id="%s">', $data_attr, $class, $style, $id);
                                        $html .= "<div class='mo-checkbox-title' style='$alignment_style'>$placeholder</div>";

                                        foreach ($options as $option) {
                                            $html .= sprintf('<label class="mo-list-subscription-checkbox" style="%s">', $alignment_style);
                                            $html .= sprintf('<input style="%s" type="checkbox" name="%s[]" value="%s"> %s', $style, $name_attribute, $option, $integration_email_list[$option]);
                                            $html .= '</label>';
                                        }
                                        $html .= '</div>';
                                    }
                                    break;
                                case 'radio':
                                    if (is_array($options) && ! empty($options)) {
                                        $html .= sprintf('<div %s class="%s" style="%s" id="%s">', $data_attr, $class, $style, $id);
                                        $html .= "<div class='mo-radio-title' style='$alignment_style'>$placeholder</div>";
                                        foreach ($options as $option) {
                                            $html .= sprintf('<label class="mo-list-subscription-checkbox" style="%s">', $alignment_style);
                                            $html .= sprintf('<input style="%s" type="radio" name="%s" value="%s"> %s', $style, $name_attribute, $option, $integration_email_list[$option]);
                                            $html .= '</label>';
                                        }
                                        $html .= '</div>';
                                    }
                                    break;
                                case 'select':
                                    if (is_array($options) && ! empty($options)) {
                                        $placeholder = ! empty($placeholder) ? $placeholder : esc_html__('Select...', 'mailoptin');
                                        $html        .= "<select name=\"$name_attribute\" $data_attr class=\"$class\" id=\"$id\" style=\"$style\">";
                                        $html        .= "<option value='' selected='selected'>$placeholder</option>";
                                        foreach ($options as $option) {
                                            $html .= sprintf('<option value="%s">%s</option>', $option, $integration_email_list[$option]);
                                        }
                                        $html .= '</select>';
                                    }
                                    break;
                            }

                            $html .= $atts['tag_end'];
                            break;
                    }
                }
            }
        }

        return $html;
    }

    /**
     * Optin form submit button shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_submit_button($atts)
    {
        $optin_css_id         = $this->optin_css_id;
        $submit_button_styles = $this->submit_button_styles();
        $submit_button        = $this->get_customizer_value('submit_button');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-submit-button $class";

        $style = esc_attr($atts['style']);
        $style = "$submit_button_styles $style";

        $html = apply_filters('mo_optin_form_before_form_submit_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_submit_button\" class=\"$class\" style=\"$style\" type=\"submit\" value=\"$submit_button\">";
        $html .= apply_filters('mo_optin_form_after_form_submit_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form call-to-action button shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_cta_button($atts)
    {
        $optin_css_id      = $this->optin_css_id;
        $cta_button_styles = $this->cta_button_styles();
        $cta_button        = $this->get_customizer_value('cta_button');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => ''
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-cta-button $class";

        $style = esc_attr($atts['style']);
        $style = "$cta_button_styles $style";

        $html = apply_filters('mo_optin_form_before_cta_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_cta_button\" class=\"$class\" style=\"$style\" type=\"submit\" value=\"$cta_button\">";
        $html .= apply_filters('mo_optin_form_after_cta_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form error shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_error($atts)
    {
        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
                'label' => __('Invalid email address', 'mailoptin'),
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-error $class";

        $style = esc_attr($atts['style']);

        $label = $atts['label'];

        $html = "<div class=\"$class\" style='$style'>$label</div>";

        return $html;
    }
}