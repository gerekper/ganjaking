<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Submit_Button_Control;
use MailOptin\Core\Admin\Customizer\CustomizerTrait;
use MailOptin\Core\Admin\Customizer\UpsellCustomizerSection;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use function MailOptin\Core\mo_test_admin_email;

class Customizer
{
    use CustomizerTrait;

    /** @var string email campaign database option name */
    public $campaign_settings = MO_EMAIL_CAMPAIGNS_WP_OPTION_NAME;

    /** @var int email campaign ID */
    public $email_campaign_id;

    /** @var int email campaign type */
    public $email_campaign_type;

    /** @var string option name prefix. */
    public $option_prefix;

    /** @var string ID of email campaign settings customizer section. */
    public $campaign_settings_section_id = 'mailoptin_campaign_settings_section_id';

    /** @var string ID of template page customizer section. */
    public $campaign_page_section_id = 'mailoptin_campaign_page';

    /** @var string ID of template header customizer section. */
    public $campaign_header_section_id = 'mailoptin_campaign_header';

    /** @var string ID of template content customizer section. */
    public $campaign_content_section_id = 'mailoptin_campaign_content';

    public $newsletter_content_section_id = 'mailoptin_newsletter_content';

    /** @var string ID of template footer customizer section. */
    public $campaign_footer_section_id = 'mailoptin_campaign_footer';

    public $campaign_view_tags_section_id = 'mailoptin_campaign_view_tags';

    public $campaign_preview_section_id = 'mailoptin_campaign_preview';

    /** @var string ID of template footer customizer section. */
    public $campaign_send_email_section_id = 'mailoptin_campaign_send_email';

    /**
     * Customizer constructor.
     */
    public function __construct()
    {
        if ( ! empty($_REQUEST['mailoptin_email_campaign_id'])) {

            $this->clean_up_customizer();
            $this->modify_customizer_publish_button();

            add_action('customize_controls_enqueue_scripts', array($this, 'monkey_patch_customizer_payload'));
            add_action('customize_controls_enqueue_scripts', array($this, 'customizer_css'));
            add_action('customize_controls_enqueue_scripts', array($this, 'customizer_js'));

            add_action('customize_controls_print_footer_scripts', [$this, 'add_send_broadcast_button']);
            add_action('customize_controls_print_footer_scripts', [$this, 'add_activate_switch']);
            add_action('customize_controls_print_footer_scripts', [$this, 'change_title_html']);

            $this->email_campaign_id = absint($_REQUEST['mailoptin_email_campaign_id']);

            $this->email_campaign_type = ER::get_email_campaign_type(
                $this->email_campaign_id
            );

            add_action('customize_controls_print_scripts', function () {
                $email_campaign_is_code_your_own = ! ER::is_newsletter($this->email_campaign_id) && ER::is_code_your_own_template($this->email_campaign_id) ? 'true' : 'false';
                $newsletter_is_code_your_own     = ER::is_newsletter($this->email_campaign_id) && ER::is_code_your_own_template($this->email_campaign_id) ? 'true' : 'false';
                $is_email_newsletter             = ER::is_newsletter($this->email_campaign_id) ? 'true' : 'false';

                echo '<script type="text/javascript">';
                echo "var mailoptin_email_campaign_option_prefix = '{$this->campaign_settings}';";
                echo "var mailoptin_email_campaign_id = $this->email_campaign_id;";
                echo "var mailoptin_email_campaign_is_code_your_own = $email_campaign_is_code_your_own;";
                echo "var mailoptin_is_email_newsletter = $is_email_newsletter;";
                echo "var mailoptin_newsletter_is_code_your_own = $newsletter_is_code_your_own;";
                if ($is_email_newsletter == 'true') {
                    echo "var mailoptin_is_email_newsletter = $is_email_newsletter;";
                }
                echo '</script>';
            });

            add_action('customize_controls_enqueue_scripts', function () {
                wp_enqueue_script('mailoptin-send-test-email', MAILOPTIN_ASSETS_URL . 'js/admin/send-test-email.js', ['jquery']);
            });

            // do not use template_include because it doesnt work in some instances eg when membermouse plugin is installed.
            add_action('template_redirect', array($this, 'include_campaign_customizer_template'), 1);

            add_filter('gettext', array($this, 'rewrite_customizer_panel_description'), 10, 3);

            // remove all sections other than that of email campaign customizer.
            add_action('customize_section_active', array($this, 'remove_sections'), 10, 2);

            // Remove all customizer panels.
            add_action('customize_panel_active', '__return_false');

            add_action('customize_register', array($this, 'register_campaign_customizer'), -1);

            // save edited email campaign title
            add_action('customize_save', array($this, 'save_email_campaign_title'));

            add_action('customize_controls_init', [$this, 'set_customizer_urls']);
        }
    }

    public function preview_url()
    {
        return add_query_arg(
            '_wpnonce',
            wp_create_nonce('mailoptin-preview-email-campaign'),
            sprintf(home_url('/?mailoptin_email_campaign_id=%d'), $this->email_campaign_id)
        );
    }

    public function set_customizer_urls()
    {
        global $wp_customize;

        $preview_url = $this->preview_url();
        $return_url  = MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE;

        if (ER::is_newsletter($this->email_campaign_id)) {
            $return_url = MAILOPTIN_EMAIL_NEWSLETTERS_SETTINGS_PAGE;
        }

        $wp_customize->set_preview_url($preview_url);

        $wp_customize->set_return_url($return_url);
    }

    public function add_send_broadcast_button()
    {
        if ( ! ER::is_newsletter($this->email_campaign_id)) return;

        $date_sent = EmailCampaignMeta::get_meta_data($this->email_campaign_id, 'newsletter_date_sent');

        if ( ! empty($date_sent) && ! in_array($date_sent, [ER::NEWSLETTER_STATUS_FAILED, ER::NEWSLETTER_STATUS_DRAFT])) {
            echo '<style type="text/css">#customize-save-button-wrapper {display:none!important;}</style>';

            return;
        };

        $url = esc_url(
            add_query_arg(
                '_wpnonce',
                wp_create_nonce('mailoptin-send-newsletter'),
                admin_url('?action=mailoptin_send_newsletter&id=' . $this->email_campaign_id)
            )
        );

        $btn = sprintf(
            '<a onclick="return confirm(%s\'%s\')" href="%s" id="mo-send-newsletter-btn" class="button">%s</a>',
            '\\',
            __('Are you sure you want to send this newsletter now?', 'mailoptin') . '\\',
            $url,
            __('Send Broadcast', 'mailoptin')
        );
        ?>
        <script type="text/javascript">
            jQuery(function () {
                jQuery('#customize-header-actions').prepend(jQuery('<?php echo $btn; ?>'));
            });
        </script>
        <?php
    }

    /**
     * Add activation switch to optin customizer
     */
    public function add_activate_switch()
    {
        if (ER::is_newsletter($this->email_campaign_id)) return;

        $input_value = ER::is_campaign_active($this->email_campaign_id) ? 'yes' : 'no';
        $checked     = ($input_value == 'yes') ? 'checked="checked"' : null;
        $tooltip     = __('Toggle to activate and deactivate email automation.', 'mailoptin');

        $switch = sprintf(
            '<input id="mo-automation-activate-switch" type="checkbox" class="tgl tgl-light" value="%s" %s />',
            $input_value,
            $checked
        );

        $switch .= '<label id="mo-automation-active-switch" for="mo-automation-activate-switch" class="tgl-btn"></label>';
        $switch .= '<span title="' . $tooltip . '" class="mo-tooltipster dashicons dashicons-editor-help" style="margin: 9px 5px;font-size: 18px;cursor: pointer;"></span>';
        ?>
        <script type="text/javascript">
            jQuery(function () {
                jQuery('#customize-header-actions').prepend(jQuery('<?php echo $switch; ?>'));
            });
        </script>
        <?php
    }

    public function change_title_html()
    {
        $title = EmailCampaignRepository::get_email_campaign_name($this->email_campaign_id);
        ?>
        <div id="mo-change-name-html" style="display: none">
            <input id="motitleinput" type="text" value="<?= $title ?>">
            <input type="submit" id="mosavetitle" class="button button-primary" data-processing-label="<?= esc_html__('Updating...', 'mailoptin') ?>" value="<?= esc_html__('Update', 'mailoptin'); ?>">
        </div>
        <?php
    }

    public function selector_mapping_scripts_styles()
    {
        $mappings = apply_filters('mo_optin_selectors_mapping', [
            [
                'selector' => '.mo-header-container',
                'type'     => 'section',
                'value'    => $this->campaign_header_section_id
            ],
            [
                'selector' => '.mo-footer-container',
                'type'     => 'section',
                'value'    => $this->campaign_footer_section_id
            ],
            [
                'selector' => '.mo-powered-by-attribute',
                'type'     => 'control',
                'value'    => 'footer_removal'
            ]
        ]);


        if ( ! ER::is_newsletter($this->email_campaign_id)) {
            $mappings[] = [
                'selector' => '.mo-body-container',
                'type'     => 'section',
                'value'    => $this->campaign_content_section_id
            ];
            $mappings[] = [
                'selector' => '.mo-content-background-color',
                'type'     => 'section',
                'value'    => $this->campaign_content_section_id
            ];
        }

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $mappings[] =
                [
                    'selector' => '.mo-optin-powered-by',
                    'type'     => 'control',
                    'value'    => 'remove_branding'
                ];
        }

        // source: https://stackoverflow.com/a/35957563/2648410
        $last_mapping = array_values(array_slice($mappings, -1))[0];

        $css_selectors = '';
        foreach ($mappings as $mapping) {
            $css_selectors .= $mapping['selector'] . ':hover';
            // do not add comma to trailing/last selector
            if ($mapping != $last_mapping) {
                $css_selectors .= ',';
            }
        }
        $css_selectors .= '{background: rgba(255, 185, 0, 0.52) !important;border: 1px dashed #ffb900 !important;cursor: pointer !important;}';
        $css_selectors .= '.mo-email-builder-element:hover {border: 2px solid #0071a1 !important; padding: 5px; cursor: pointer !important;}';
        ?>

        <style type="text/css"><?php echo $css_selectors; ?></style>
        <script type="text/javascript">
            var mailoptin_option_mapping = <?php echo wp_json_encode($mappings); ?>;
            (function ($) {
                $(function () {
                    $.each(mailoptin_option_mapping, function (key, value) {
                        if (typeof value.type !== 'undefined') {
                            $(document).on('click', value.selector, function (e) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                if (value.type === 'section') {
                                    parent.wp.customize.section(value.value).focus()
                                }
                                if (value.type === 'control') {
                                    parent.wp.customize.control('mo_email_campaigns[' + mailoptin_email_campaign_id + '][' + value.value + ']').focus()
                                }
                                if (value.type === 'panel') {
                                    parent.wp.customize.panel(value.value).focus()
                                }
                            });
                        }
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    public function monkey_patch_customizer_payload()
    {
        wp_add_inline_script('customize-controls', '(function ( api ) {
              api.bind( "ready", function () {
                  var _query = api.previewer.query;
                      api.previewer.query = function () {
                          var query = _query.call( this );
                          query.mailoptin_email_campaign_id = "' . $this->email_campaign_id . '";
                          return query;
                      };
                  // needed to ensure save button is publising changes and not saving draft.
                  // esp for wp.com business hosting with save button set to draft by default.
                  api.state("selectedChangesetStatus").set("publish");
                  });
              })( wp.customize );'
        );
    }

    /**
     * Enqueue JavaScript for email campaign template customizer controls.
     */
    public function customizer_js()
    {
        wp_enqueue_script(
            'mailoptin-fetch-email-customizer-connect-list-controls',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/fetch-customizer-connect-list.js',
            array('customize-controls'),
            MAILOPTIN_VERSION_NUMBER
        );

        wp_enqueue_script(
            'mailoptin-email-customizer-contextual-controls',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/contextual-email-customizer-controls.js',
            array('customize-controls'),
            MAILOPTIN_VERSION_NUMBER
        );

        if ( ! ER::is_newsletter($this->email_campaign_id) && ER::is_code_your_own_template($this->email_campaign_id)) {

            wp_enqueue_script(
                'mailoptin-ace-js',
                MAILOPTIN_ASSETS_URL . 'js/customizer-controls/ace-editor/ace.js',
                array('jquery'),
                false,
                true
            );

            wp_enqueue_script(
                'mailoptin-email-automation-code-editor-controls',
                MAILOPTIN_ASSETS_URL . 'js/customizer-controls/email-automation-code-editor.js',
                array('customize-controls'),
                MAILOPTIN_VERSION_NUMBER
            );

            wp_localize_script('mailoptin-email-automation-code-editor-controls', 'moEmailCodeEditor_strings', array(
                'viewTags'      => __('View available tags', 'mailoptin'),
                'previewBtn'    => __('Preview', 'mailoptin'),
                'codeEditorBtn' => __('Code Editor', 'mailoptin')
            ));
        }

        if (ER::is_newsletter($this->email_campaign_id)) {

            wp_enqueue_script(
                'mailoptin-email-newsletter-editor',
                MAILOPTIN_ASSETS_URL . 'js/customizer-controls/newsletter-code-editor.js',
                array('customize-controls'),
                MAILOPTIN_VERSION_NUMBER
            );

            wp_localize_script('mailoptin-email-newsletter-editor', 'moEmailNewsletterEditor_strings', array(
                'viewTags'   => __('View available tags', 'mailoptin'),
                'previewBtn' => __('Preview', 'mailoptin'),
                'contentBtn' => __('Newsletter Content', 'mailoptin')
            ));

            wp_enqueue_script(
                'mailoptin-ace-js',
                MAILOPTIN_ASSETS_URL . 'js/customizer-controls/ace-editor/ace.js',
                array('jquery'),
                false,
                true
            );
        }

        do_action('mailoptin_email_campaign_enqueue_customizer_js');
    }

    /**
     * customizer enqueued CSS
     */
    public function customizer_css()
    {
        wp_enqueue_style('mailoptin-customizer', MAILOPTIN_ASSETS_URL . 'css/admin/customizer-stylesheet.css');
    }


    /**
     * By default, customizer uses blog name as panel title
     * hence the rewrite to email campaign name if pass as query sting to customizer url.
     * default to 'Email Campaign'.
     *
     * @param string $blogname
     *
     * @return string
     */
    public function rewrite_customizer_panel_title($blogname)
    {
        $campaign_name = ER::get_email_campaign_name($this->email_campaign_id);

        return $campaign_name ?: __('Email Automation', 'mailoptin');
    }

    /**
     * By default, customizer has the below as its panel description
     *
     * The Customizer allows you to preview changes to your site before publishing them.
     * You can also navigate to different pages on your site to preview them.
     *
     * This class method rewrite this.
     *
     * @param string $translations
     * @param string $text
     * @param string $domain
     *
     * @return string
     */
    public function rewrite_customizer_panel_description($translations, $text, $domain)
    {
        if (strpos($text, 'Customizer allows you to preview changes to your site')) {
            $translations = __(
                'The customizer allows you to design, preview and set up to email campaigns.',
                'mailoptin'
            );
        }

        return $translations;
    }

    /**
     * Remove non-mailoptin customizer sections.
     *
     * @param $active
     * @param $section
     *
     * @return bool
     */
    public function remove_sections($active, $section)
    {
        $sections_ids = apply_filters(
            'mailoptin_campaign_sections_ids',
            array(
                $this->campaign_settings_section_id,
                $this->campaign_page_section_id,
                $this->campaign_header_section_id,
                $this->campaign_content_section_id,
                $this->newsletter_content_section_id,
                $this->campaign_footer_section_id,
                $this->campaign_send_email_section_id,
                $this->campaign_view_tags_section_id,
                $this->campaign_preview_section_id
            )
        );

        return in_array($section->id, $sections_ids);
    }

    /**
     * Include template preview template.
     *
     * @return string
     */
    public function include_campaign_customizer_template()
    {
        if (is_customize_preview() && wp_verify_nonce($_REQUEST['_wpnonce'], 'mailoptin-preview-email-campaign')) {
            include(MAILOPTIN_SRC . 'Admin/Customizer/EmailCampaign/email-campaign-preview.php');
            exit;
        }

        wp_safe_redirect(MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE);
        exit;
    }

    /**
     * Customizer registration.
     *
     * @param \WP_Customize_Manager $wp_customize
     */
    public function register_campaign_customizer($wp_customize)
    {
        if (Settings::instance()->switch_customizer_loader() != 'true') {
            remove_all_actions('customize_register'); // improve compatibility with hestia, generatepress themes etc
        }

        $email_campaign_id = absint($_REQUEST['mailoptin_email_campaign_id']);

        $option_prefix = $this->campaign_settings . '[' . $email_campaign_id . ']';

        do_action('mailoptin_register_campaign_customizer', $email_campaign_id);

        $template_class = ER::get_template_class($email_campaign_id);

        if ($template_class !== ER::CODE_YOUR_OWN_TEMPLATE) {

            $result = EmailCampaignFactory::make($email_campaign_id);

            // $result is false of optin form class do not exist.
            if ( ! $result) {
                wp_safe_redirect(add_query_arg('email-campaign-error', 'class-not-found', MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE));
                exit;
            }
        }

        $this->register_custom_section($wp_customize);
        $this->register_control_type($wp_customize);

        $this->add_sections($wp_customize);
        $this->add_settings($wp_customize, $option_prefix);
        $this->add_controls($wp_customize, $option_prefix);

        // rewrite panel name from blog name to email campaign name.
        add_filter('pre_option_blogname', array($this, 'rewrite_customizer_panel_title'));

        add_action('wp_footer', [$this, 'selector_mapping_scripts_styles'], 99);
    }

    /**
     * @param \WP_Customize_Manager $wp_customize_manager
     */
    public function save_email_campaign_title($wp_customize_manager)
    {
        $email_campaign_id = $this->email_campaign_id;
        $option_name       = "mo_email_campaigns[$email_campaign_id][email_campaign_title]";
        $posted_values     = $wp_customize_manager->unsanitized_post_values();

        if (array_key_exists($option_name, $posted_values)) {
            ER::update_campaign_name(
                sanitize_text_field($posted_values[$option_name]),
                $email_campaign_id
            );
        }
    }

    /**
     * Add sections to customizer.
     *
     * @param \WP_Customize_Manager $wp_customize
     */
    public function add_sections($wp_customize)
    {
        if ( ! apply_filters('mo_email_customizer_disable_upsell_section', false)) {

            $wp_customize->add_section(
                new UpsellCustomizerSection($wp_customize, 'mailoptin_upsell_section',
                    array(
                        'pro_text'   => __('Check out MailOptin Premium!', 'mailoptin'),
                        'pro_url'    => 'https://mailoptin.io/pricing/?utm_source=optin_customizer&utm_medium=upgrade&utm_campaign=upsell_customizer_section',
                        'capability' => \MailOptin\Core\get_capability(),
                        'priority'   => 0,
                        'type'       => 'mo-upsell-section'
                    )
                )
            );
        }

        $wp_customize->add_section($this->campaign_settings_section_id, array(
                'title'    => __('Settings', 'mailoptin'),
                'priority' => 10,
            )
        );

        if (ER::is_code_your_own_template($this->email_campaign_id)) {

            $wp_customize->add_section($this->campaign_view_tags_section_id, array(
                    'title'    => __('View Available Tags', 'mailoptin'),
                    'priority' => 20,
                )
            );
        } else {
            $wp_customize->add_section($this->campaign_page_section_id, array(
                    'title'    => __('Body', 'mailoptin'),
                    'priority' => 20,
                )
            );

            $wp_customize->add_section($this->campaign_header_section_id, array(
                    'title'    => __('Header', 'mailoptin'),
                    'priority' => 30,
                )
            );

            if ( ! ER::is_newsletter($this->email_campaign_id)) {
                $wp_customize->add_section($this->campaign_content_section_id, array(
                        'title'    => __('Content', 'mailoptin'),
                        'priority' => 40,
                    )
                );
            } else {
                $wp_customize->add_section($this->newsletter_content_section_id, array(
                        'title'    => __('Content', 'mailoptin'),
                        'priority' => 40,
                    )
                );
            }

            $wp_customize->add_section($this->campaign_footer_section_id, array(
                    'title'    => __('Footer', 'mailoptin'),
                    'priority' => 50,
                )
            );
        }

        $wp_customize->add_section($this->campaign_preview_section_id, array(
                'title'    => __('Preview', 'mailoptin'),
                'priority' => 55,
            )
        );

        $wp_customize->add_section($this->campaign_send_email_section_id, array(
                'title'    => __('Send Test Email', 'mailoptin'),
                'priority' => 60,
            )
        );
    }


    /**
     * Add customizer settings.
     *
     * @param \WP_Customize_Manager $wp_customize
     */
    public function add_settings($wp_customize, $option_prefix)
    {
        $instance = new CustomizerSettings($wp_customize, $option_prefix, $this);
        $instance->available_tags_settings();
        $instance->preview_settings();
        $instance->campaign_settings();
        $instance->page_settings();
        $instance->header_settings();
        $instance->content_settings();
        $instance->footer_settings();
        $instance->test_email_settings();
    }

    /**
     * Add customizer controls.
     *
     * @param \WP_Customize_Manager $wp_customize
     */
    public function add_controls($wp_customize, $option_prefix)
    {
        $instance = new CustomizerControls($wp_customize, $option_prefix, $this);
        $instance->available_tags_control();
        $instance->preview_control();
        $instance->campaign_settings_controls();
        $instance->page_controls();
        $instance->header_controls();
        $instance->content_controls();
        $instance->newsletter_content_control();
        $instance->footer_controls();
        $instance->test_email_controls();
    }

    /**
     * @return Customizer
     */
    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}