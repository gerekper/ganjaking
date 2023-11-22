<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\ExtensionInfo;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicEmail extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    use ExtensionInfo;
    public $has_action = \true;
    public $action_priority = 100;
    public static $txt = '';
    public $doc_url;
    public function __construct()
    {
        self::add_dce_email_template_type();
        // Add specific Template Type
    }
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_email';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Dynamic Email', 'dynamic-content-for-elementor');
    }
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_email', ['label' => $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('dce_form_email_enable', ['label' => __('Enable email', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => __('You can temporary disable and reactivate it next time without deleting settings ', 'dynamic-content-for-elementor'), 'separator' => 'after']);
        $repeater_fields->add_control('dce_form_email_condition_field', ['label' => __('Condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('Type here the form field ID to check, or leave it empty to always execute this action', 'dynamic-content-for-elementor')]);
        $repeater_fields->add_control('dce_form_email_condition_status', ['label' => __('Condition Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['empty' => ['title' => __('Empty', 'dynamic-content-for-elementor'), 'icon' => 'eicon-circle-o'], 'valued' => ['title' => __('Valorized with any value', 'dynamic-content-for-elementor'), 'icon' => 'eicon-dot-circle-o'], 'lt' => ['title' => __('Less than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-left'], 'gt' => ['title' => __('Greater than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right'], 'equal' => ['title' => __('Equal to', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'contain' => ['title' => __('Contains', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check']], 'default' => 'valued', 'toggle' => \false, 'label_block' => \true, 'condition' => ['dce_form_email_condition_field!' => '']]);
        $repeater_fields->add_control('dce_form_email_condition_value', ['label' => __('Condition Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('A value to compare the value of the field', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_condition_field!' => '', 'dce_form_email_condition_status' => ['lt', 'gt', 'equal', 'contain']]]);
        /* translators: %s: Site title. */
        $default_message = \sprintf(__('New message from "%s"', 'dynamic-content-for-elementor'), get_option('blogname'));
        $repeater_fields->add_control('dce_form_email_subject', ['label' => __('Subject', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => $default_message, 'placeholder' => $default_message, 'label_block' => \true, 'render_type' => 'none', 'separator' => 'before']);
        $repeater_fields->add_control('dce_form_email_to', ['label' => __('To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => get_option('admin_email'), 'placeholder' => get_option('admin_email'), 'label_block' => \true, 'title' => __('Separate emails with commas', 'dynamic-content-for-elementor'), 'render_type' => 'none', 'separator' => 'before']);
        $site_domain = Helper::get_site_domain();
        $repeater_fields->add_control('dce_form_email_from', ['label' => __('From Email', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'email@' . $site_domain, 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_from_name', ['label' => __('From Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => get_bloginfo('name'), 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_reply_to', ['label' => __('Reply-To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_to_cc', ['label' => __('Cc', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'title' => __('Separate emails with commas', 'dynamic-content-for-elementor'), 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_to_bcc', ['label' => __('Bcc', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'title' => __('Separate emails with commas', 'dynamic-content-for-elementor'), 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_content_type', ['label' => __('Send As', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'html', 'render_type' => 'none', 'options' => ['html' => __('HTML', 'dynamic-content-for-elementor'), 'plain' => __('Plain', 'dynamic-content-for-elementor')], 'separator' => 'before']);
        $repeater_fields->add_control('dce_form_email_content_type_advanced', ['label' => __('Email body', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => __('Message', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text', 'condition' => ['dce_form_email_content_type' => 'html']]);
        $repeater_fields->add_control('dce_form_email_content', ['label' => __('Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '[all-fields]', 'placeholder' => '[all-fields]', 'description' => \sprintf(__('By default, all form fields are sent via %s shortcode. To customize sent fields, copy the shortcode that appears inside each field and paste it above.', 'dynamic-content-for-elementor'), '<code>[all-fields]</code>'), 'label_block' => \true, 'render_type' => 'none', 'condition' => ['dce_form_email_content_type_advanced' => 'text']]);
        $repeater_fields->add_control('dce_form_email_content_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => __('Use an Elementor Template as body for this Email.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_content_type' => 'html', 'dce_form_email_content_type_advanced' => 'template']]);
        $repeater_fields->add_control('dce_form_email_content_template_style', ['label' => __('Styles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['' => ['title' => __('Only HTML', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-window-close-o'], 'inline' => ['title' => __('Inline', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left']], 'default' => 'inline', 'condition' => ['dce_form_email_content_type' => 'html', 'dce_form_email_content_type_advanced' => 'template']]);
        $repeater_fields->add_control('dce_form_email_content_template_layout', ['label' => __('Flex or Table', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex' => ['title' => __('CSS FLEX', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-leaf'], 'table' => ['title' => __('CSS TABLE', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large'], 'html' => ['title' => __('HTML TABLE', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-table']], 'default' => 'table', 'description' => __('Add more compatibility for columned layout visualization', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_content_type' => 'html', 'dce_form_email_content_type_advanced' => 'template', 'dce_form_email_content_template_style' => 'inline']]);
        $repeater_fields->add_control('dce_form_email_attachments', ['label' => __('Add Upload files as Attachments', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Send all Uploaded Files as Email Attachments', 'dynamic-content-for-elementor'), 'separator' => 'before']);
        $repeater_fields->add_control('dce_form_email_attachments_delete', ['label' => __('Delete Files after Emails are sent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Remove all uploaded Files from Server after Email is sent with the Files as Attachments', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_attachments!' => '']]);
        $repeater_fields->add_control('dce_form_pdf_attachments_delete', ['label' => __('Delete PDF attachments after Emails are sent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'description' => __('Remove all attached PDF files from Server after Email is sent.', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_email_repeater', ['label' => __('Emails', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ dce_form_email_subject }}}', 'fields' => $repeater_fields->get_controls(), 'description' => __('Send all Email you need', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_email_help', ['type' => Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->doc_url . '" target="_blank">' . __('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
        $widget->end_controls_section();
    }
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $post_id = \intval($_POST['post_id']);
        $form_id = sanitize_text_field($_POST['form_id']);
        if (!empty($fields['submitted_on_id'])) {
            // force post for Dynamic Tags and Widgets
            $submitted_on_id = $fields['submitted_on_id'];
            global $post, $wp_query;
            $post = get_post($submitted_on_id);
            $wp_query->queried_object = $post;
            $wp_query->queried_object_id = $submitted_on_id;
        }
        $document = \Elementor\Plugin::$instance->documents->get($post_id);
        if ($document) {
            $form = \ElementorPro\Modules\Forms\Module::find_element_recursive($document->get_elements_data(), $form_id);
            $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance($form);
            $settings = $widget->get_settings_for_display();
        } else {
            $settings = $record->get('form_settings');
        }
        $settings = Helper::get_dynamic_value($settings, $fields);
        $this->email($fields, $settings, $ajax_handler, $record);
    }
    protected function email($fields, $settings = null, $ajax_handler = null, $record = null)
    {
        $remove_uploaded_files = \false;
        $all_pdf_attachments = [];
        $remove_pdf_files = \false;
        foreach ($settings['dce_form_email_repeater'] as $mkey => $amail) {
            if ($amail['dce_form_email_enable']) {
                $condition_satisfy = \true;
                if (!empty($amail['dce_form_email_condition_field'])) {
                    $field_value = $fields[$amail['dce_form_email_condition_field']] ?? '';
                    switch ($amail['dce_form_email_condition_status']) {
                        case 'empty':
                            if (!empty($field_value)) {
                                $condition_satisfy = \false;
                            }
                            break;
                        case 'valued':
                            if (empty($field_value)) {
                                $condition_satisfy = \false;
                            }
                            break;
                        case 'lt':
                            if (empty($field_value) || $field_value > $amail['dce_form_email_condition_value']) {
                                $condition_satisfy = \false;
                            }
                            break;
                        case 'gt':
                            if (empty($field_value) || $field_value < $amail['dce_form_email_condition_value']) {
                                $condition_satisfy = \false;
                            }
                            break;
                        case 'equal':
                            if ($field_value != $amail['dce_form_email_condition_value']) {
                                $condition_satisfy = \false;
                            }
                        case 'contain':
                            $field_type = Helper::get_field_type($amail['dce_form_email_condition_field'], $settings);
                            if ($field_type == 'checkbox') {
                                $field_value = Helper::str_to_array(', ', $field_value);
                            }
                            if (\is_array($fields[$amail['dce_form_email_condition_field']])) {
                                if (!\in_array($amail['dce_form_email_condition_value'], $field_value)) {
                                    $condition_satisfy = \false;
                                }
                            } else {
                                if (\strpos($field_value, $amail['dce_form_email_condition_value']) === \false) {
                                    $condition_satisfy = \false;
                                }
                            }
                            break;
                    }
                }
                $use_template = \false;
                if (!empty($amail['dce_form_email_content_type_advanced']) && $amail['dce_form_email_content_type_advanced'] == 'template') {
                    $use_template = \true;
                }
                $send_html = 'plain' !== $amail['dce_form_email_content_type'] || $use_template;
                $line_break = $send_html ? '<br />' : "\n";
                $attachments = array();
                $email_fields = [
                    'dce_form_email_to' => get_option('admin_email'),
                    /* translators: %s: Site title. */
                    'dce_form_email_subject' => \sprintf(__('New message from "%s"', 'dynamic-content-for-elementor'), get_bloginfo('name')),
                    'dce_form_email_content' => '[all-fields]',
                    'dce_form_email_from_name' => get_bloginfo('name'),
                    'dce_form_email_from' => get_bloginfo('admin_email'),
                    'dce_form_email_reply_to' => 'no-reply@' . Helper::get_site_domain(),
                    'dce_form_email_to_cc' => '',
                    'dce_form_email_to_bcc' => '',
                ];
                foreach ($email_fields as $key => $default) {
                    $setting = $amail[$key];
                    if (!empty($setting)) {
                        $email_fields[$key] = $setting;
                    }
                }
                $headers = \sprintf('From: %s <%s>' . "\r\n", $email_fields['dce_form_email_from_name'], $email_fields['dce_form_email_from']);
                if (!empty($email_fields['dce_form_email_reply_to'])) {
                    if (\filter_var($email_fields['dce_form_email_reply_to'], \FILTER_VALIDATE_EMAIL)) {
                        // control if is a valid email
                        $headers .= \sprintf('Reply-To: %s' . "\r\n", $email_fields['dce_form_email_reply_to']);
                    }
                }
                if ($send_html) {
                    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                }
                $cc_header = '';
                if (!empty($email_fields['dce_form_email_to_cc'])) {
                    $cc_header = 'Cc: ' . $email_fields['dce_form_email_to_cc'] . "\r\n";
                }
                $bcc_header = '';
                if (!empty($email_fields['dce_form_email_to_bcc'])) {
                    $bcc_header = 'Bcc: ' . $email_fields['dce_form_email_to_bcc'] . "\r\n";
                }
                /**
                 * Email headers.
                 *
                 * Filters the additional headers sent when the form send an email.
                 *
                 * @since 1.0.0
                 *
                 * @param string|array $headers Additional headers.
                 */
                $headers = apply_filters('elementor_pro/forms/wp_mail_headers', $headers);
                /**
                 * Email content.
                 *
                 * Filters the content of the email sent by the form.
                 *
                 * @since 1.0.0
                 *
                 * @param string $email_content Email content.
                 */
                if ($use_template) {
                    // using a template
                    $inline = '';
                    if ($amail['dce_form_email_content_template_style'] == 'embed') {
                        $inline = ' inlinecss="true"';
                    }
                    $author = '';
                    $current_user_id = get_current_user_id();
                    if ($current_user_id) {
                        $author = ' author_id="' . $current_user_id . '"';
                    }
                    $t_post = '';
                    if (get_the_ID()) {
                        $t_post = ' post_id="' . get_the_ID() . '"';
                    }
                    $dce_form_email_content = do_shortcode('[dce-elementor-template id="' . $amail['dce_form_email_content_template'] . '"' . $t_post . $inline . $author . ']');
                    $pdf_attachments = $this->get_email_pdf_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $all_pdf_attachments += $pdf_attachments;
                    $upload_attachments = $this->get_email_upload_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $attachments = $pdf_attachments + $upload_attachments;
                    $dce_form_email_content = $this->remove_attachment_tokens($dce_form_email_content, $fields);
                    $dce_form_email_content = $this->replace_content_shortcodes($dce_form_email_content, $record, $line_break);
                    $dce_form_email_content = Helper::get_dynamic_value($dce_form_email_content, $fields);
                    if ($amail['dce_form_email_content_template_style']) {
                        $css = Helper::get_post_css($amail['dce_form_email_content_template']);
                        // add some fixies
                        $css .= '/*.elementor-column-wrap,*/ .elementor-widget-wrap { display: block !important; }';
                        if (!empty($amail['dce_form_email_content_template_layout']) && $amail['dce_form_email_content_template_layout'] != 'flex') {
                            // from flex to table
                            $css .= '.elementor-section .elementor-container { display: table !important; width: 100% !important; }';
                            $css .= '.elementor-row { display: table-row !important; }';
                            $css .= '.elementor-column { display: table-cell !important; }';
                            $css .= '.elementor-column-wrap, .elementor-widget-wrap { display: block !important; }';
                            $css = \str_replace(':not(.elementor-motion-effects-element-type-background) > .elementor-element-populated', ':not(.elementor-motion-effects-element-type-background)', $css);
                        }
                        if ($amail['dce_form_email_content_template_style'] == 'inline') {
                            // https://github.com/tijsverkoyen/CssToInlineStyles
                            // create instance
                            $cssToInlineStyles = new \DynamicOOOS\TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
                            // output
                            $dce_form_email_content = $cssToInlineStyles->convert($dce_form_email_content, $css);
                        }
                        if (!empty($amail['dce_form_email_content_template_layout']) && $amail['dce_form_email_content_template_layout'] == 'html') {
                            // from div to table
                            $dce_form_email_content = Helper::tablefy($dce_form_email_content);
                        }
                        if ($amail['dce_form_email_content_template_style'] == 'embed') {
                            $dce_form_email_content = '<style>' . $css . '</style>' . $dce_form_email_content;
                        }
                    }
                    $dce_form_email_content_txt = '';
                } else {
                    $settings_raw = $record->get('form_settings');
                    // from message textarea with dynamic token
                    $dce_form_email_content = $settings_raw['dce_form_email_repeater'][$mkey]['dce_form_email_content'];
                    $pdf_attachments = $this->get_email_pdf_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $all_pdf_attachments += $pdf_attachments;
                    $upload_attachments = $this->get_email_upload_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $attachments = \array_merge($pdf_attachments, $upload_attachments);
                    $dce_form_email_content = $this->remove_attachment_tokens($dce_form_email_content, $fields);
                    $dce_form_email_content = $this->replace_content_shortcodes($dce_form_email_content, $record, $line_break);
                    $dce_form_email_content = Helper::get_dynamic_value($dce_form_email_content, $fields);
                    // generate the TEXT/PLAIN version
                    $dce_form_email_content_txt = $dce_form_email_content;
                    $dce_form_email_content_txt = \str_replace('</p>', '</p><br /><br />', $dce_form_email_content_txt);
                    $dce_form_email_content_txt = \str_replace('<br />', "\n", $dce_form_email_content_txt);
                    $dce_form_email_content_txt = \str_replace('<br>', "\n", $dce_form_email_content_txt);
                    $dce_form_email_content_txt = \strip_tags($dce_form_email_content_txt);
                    if ($send_html) {
                        add_action('phpmailer_init', [$this, 'set_wp_mail_altbody']);
                    } else {
                        $dce_form_email_content = $dce_form_email_content_txt;
                        $dce_form_email_content_txt = '';
                    }
                    $dce_form_email_content = apply_filters('elementor_pro/forms/wp_mail_message', $dce_form_email_content);
                }
                self::$txt = $dce_form_email_content_txt;
                // replace single fields shorcode
                $dce_form_email_content = Helper::replace_setting_shortcodes($dce_form_email_content, $fields);
                if ($condition_satisfy) {
                    $email_sent = wp_mail($email_fields['dce_form_email_to'], $email_fields['dce_form_email_subject'], $dce_form_email_content, $headers . $cc_header . $bcc_header, $attachments);
                    do_action('elementor_pro/forms/mail_sent', $amail, $record);
                    if (!$email_sent) {
                        $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $amail));
                    }
                }
                if ($amail['dce_form_email_attachments'] && $amail['dce_form_email_attachments_delete']) {
                    $remove_uploaded_files = \true;
                }
                if (($amail['dce_form_pdf_attachments_delete'] ?? '') === 'yes') {
                    $remove_pdf_files = \true;
                }
                global $phpmailer;
                if (isset($phpmailer) && $phpmailer !== NULL) {
                    $phpmailer->AltBody = '';
                    // clear the previous alt body for the next email.
                }
                remove_action('phpmailer_init', [$this, 'set_wp_mail_altbody']);
            }
        }
        if ($remove_pdf_files) {
            foreach ($all_pdf_attachments as $pdf_path) {
                \unlink($pdf_path);
            }
        }
        if ($remove_uploaded_files && $ajax_handler->is_success) {
            if (!empty($fields) && \is_array($fields)) {
                foreach ($fields as $akey => $adatas) {
                    $afield = Helper::get_field($akey, $settings);
                    if ($afield) {
                        if ($afield['field_type'] == 'upload') {
                            $files = Helper::str_to_array(',', $adatas);
                            if (!empty($files)) {
                                foreach ($files as $adata) {
                                    if (\filter_var($adata, \FILTER_VALIDATE_URL)) {
                                        $filename = Helper::url_to_path($adata);
                                        if (\is_file($filename)) {
                                            \unlink($filename);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public static function set_wp_mail_altbody($phpmailer)
    {
        if (isset($phpmailer) && $phpmailer !== NULL) {
            $phpmailer->AltBody = self::$txt;
        }
    }
    public function remove_attachment_tokens($dce_form_email_content, $fields)
    {
        $attachments_tokens = \explode(':attachment]', $dce_form_email_content);
        foreach ($attachments_tokens as $akey => $avalue) {
            $pieces = \explode('[form:', $avalue);
            if (\count($pieces) > 2) {
                $field = \end($pieces);
                if (isset($fields[$field])) {
                    $dce_form_email_content = \str_replace('[form:' . $field . ':attachment]', '', $dce_form_email_content);
                }
            }
        }
        return $dce_form_email_content;
    }
    public function get_email_pdf_attachments($dce_form_email_content, $fields, $amail, $settings)
    {
        $attachments = array();
        $pdf_attachment = '<!--[dce_form_pdf:attachment]-->';
        $pdf_form = '[form:pdf]';
        $pos_pdf_token = \strpos($dce_form_email_content, $pdf_attachment);
        $pos_pdf_form = \strpos($dce_form_email_content, $pdf_form);
        if ($pos_pdf_token !== \false || $pos_pdf_form !== \false) {
            // add PDF as attachment
            global $dce_form;
            if (isset($dce_form['pdf']) && isset($dce_form['pdf']['path'])) {
                $pdf_path = $dce_form['pdf']['path'];
                $attachments[] = $pdf_path;
            }
            $dce_form_email_content = \str_replace($pdf_attachment, '', $dce_form_email_content);
            $dce_form_email_content = \str_replace($pdf_form, '', $dce_form_email_content);
        }
        $attachments_tokens = \explode(':attachment]', $dce_form_email_content);
        foreach ($attachments_tokens as $akey => $avalue) {
            $pieces = \explode('[form:', $avalue);
            if (\count($pieces) > 1) {
                $field = \end($pieces);
                if (isset($fields[$field])) {
                    $files = Helper::str_to_array(',', $fields[$field]);
                    if (!empty($files)) {
                        foreach ($files as $adata) {
                            if (\filter_var($adata, \FILTER_VALIDATE_URL)) {
                                $file_path = Helper::url_to_path($adata);
                                if (\is_file($file_path)) {
                                    if (!\in_array($file_path, $attachments)) {
                                        $attachments[] = $file_path;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attachments;
    }
    public function get_email_upload_attachments($dce_form_email_content, $fields, $amail, $settings)
    {
        $attachments = [];
        if ($amail['dce_form_email_attachments']) {
            if (!empty($fields) && \is_array($fields)) {
                foreach ($fields as $akey => $adatas) {
                    $afield = Helper::get_field($akey, $settings);
                    if ($afield) {
                        if ($afield['field_type'] == 'upload') {
                            $files = Helper::str_to_array(',', $adatas);
                            if (!empty($files)) {
                                foreach ($files as $adata) {
                                    if (\filter_var($adata, \FILTER_VALIDATE_URL)) {
                                        $file_path = Helper::url_to_path($adata);
                                        if (\is_file($file_path)) {
                                            if (!\in_array($file_path, $attachments)) {
                                                $attachments[] = $file_path;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attachments;
    }
    /**
     * @copyright Elegant Themes
     * @link http://www.elegantthemes.com/
     * @license GPLv2
     */
    public function replace_content_shortcodes($email_content, $record, $line_break)
    {
        $all_fields_shortcode = '[all-fields]';
        $text = $this->get_shortcode_value($all_fields_shortcode, $email_content, $record, $line_break);
        $email_content = \str_replace($all_fields_shortcode, $text, $email_content);
        $all_valued_fields_shortcode = '[all-fields|!empty]';
        $text = $this->get_shortcode_value($all_valued_fields_shortcode, $email_content, $record, $line_break, \false);
        $email_content = \str_replace($all_valued_fields_shortcode, $text, $email_content);
        return $email_content;
    }
    /**
     * @copyright Elegant Themes
     * @link http://www.elegantthemes.com/
     * @license GPLv2
     */
    public function get_shortcode_value($shortcode, $email_content, $record, $line_break, $show_empty = \true)
    {
        $text = '';
        if (\false !== \strpos($email_content, $shortcode)) {
            foreach ($record->get('fields') as $field) {
                $formatted = '';
                if (!empty($field['title'])) {
                    $formatted = \sprintf('%s: %s', $field['title'], $field['value']);
                } elseif (!empty($field['value'])) {
                    $formatted = \sprintf('%s', $field['value']);
                }
                if ('textarea' === $field['type'] && '<br>' === $line_break) {
                    $formatted = \str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
                }
                if (!$show_empty && empty($field['value'])) {
                    continue;
                }
                $text .= $formatted . $line_break;
            }
        }
        return $text;
    }
    public static function add_dce_email_template_type()
    {
        // Add Email Template Type
        $dce_email = 'Elementor\\Modules\\Library\\Documents\\Email';
        \Elementor\Plugin::instance()->documents->register_document_type($dce_email::get_name_static(), \Elementor\Modules\Library\Documents\Email::get_class_full_name());
    }
    public function on_export($element)
    {
        return $element;
    }
}
