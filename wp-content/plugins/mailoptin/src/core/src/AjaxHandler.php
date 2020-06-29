<?php

namespace MailOptin\Core;

use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_EA_CPT_Control_Trait;
use MailOptin\Core\Admin\Customizer\EmailCampaign\NewPublishPostTemplatePreview;
use MailOptin\Core\Admin\Customizer\EmailCampaign\NewsletterTemplatePreview;
use MailOptin\Core\Admin\Customizer\EmailCampaign\PostsEmailDigestTemplatePreview;
use MailOptin\Core\Admin\Customizer\EmailCampaign\SolitaryDummyContent;
use MailOptin\Core\Admin\SettingsPage\Email_Campaign_List;
use MailOptin\Core\Admin\SettingsPage\OptinCampaign_List;
use MailOptin\Core\Admin\SettingsPage\SplitTestOptinCampaign;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\EmailCampaigns\Misc;
use MailOptin\Core\EmailCampaigns\NewPublishPost\NewPublishPost;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use MailOptin\Core\Repositories\OptinCampaignMeta;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use MailOptin\Core\Repositories\OptinCampaignStat;
use MailOptin\Core\Repositories\OptinConversionsRepository;
use MailOptin\Core\Repositories\OptinThemesRepository;
use MailOptin\Core\Repositories\StateRepository;
use MailOptin\Libsodium\LeadBank\LeadBank;

class AjaxHandler
{
    use WP_Customize_EA_CPT_Control_Trait;

    public function __construct()
    {
        add_action('init', array($this, 'define_ajax'), 0);
        add_action('template_redirect', array($this, 'do_mailoptin_ajax'), 0);

        // MailOptin_event => nopriv
        $ajax_events = array(
            'track_optin_impression'                   => true,
            'subscribe_to_email_list'                  => true,
            'send_test_email'                          => false,
            'create_optin_campaign'                    => false,
            'create_email_campaign'                    => false,
            'customizer_fetch_email_list'              => false,
            'customizer_rename_optin'                  => false,
            'optin_toggle_active'                      => false,
            'automation_toggle_active'                 => false,
            'toggle_optin_activated'                   => false,
            'toggle_automation_activated'              => false,
            'optin_type_selection'                     => false,
            'create_optin_split_test'                  => false,
            'pause_optin_split_test'                   => false,
            'end_optin_split_modal'                    => false,
            'split_test_select_winner'                 => false,
            'page_targeting_search'                    => false,
            'dismiss_toastr_notifications'             => false,
            'customizer_email_automation_get_taxonomy' => false,
            'customizer_optin_map_custom_field'        => false,
            'view_error_log'                           => false,
            'customizer_get_templates'                 => false,
            'customizer_set_template'                  => false,
            'ecb_fetch_post_type_posts'                => false,
        );

        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_mailoptin_' . $ajax_event, array($this, $ajax_event));

            if ($nopriv) {
                // MailOptin AJAX can be used for frontend ajax requests.
                add_action('mailoptin_ajax_' . $ajax_event, array($this, $ajax_event));
            }
        }
    }

    public static function define_ajax()
    {
        if ( ! empty($_GET['mailoptin-ajax'])) {
            if ( ! defined('DOING_AJAX')) {
                define('DOING_AJAX', true);
            }

            if ( ! WP_DEBUG || (WP_DEBUG && ! WP_DEBUG_DISPLAY)) {
                @ini_set('display_errors', 0); // Turn off display_errors during AJAX events to prevent malformed JSON
            }
            $GLOBALS['wpdb']->hide_errors();
        }
    }

    /**
     * Get MailOptin Ajax Endpoint.
     *
     * @param string $request Optional
     *
     * @return string
     */
    public static function get_endpoint()
    {
        return esc_url_raw(add_query_arg('mailoptin-ajax', '%%endpoint%%'));
    }

    public function do_mailoptin_ajax()
    {
        global $wp_query;

        if ( ! empty($_GET['mailoptin-ajax'])) {
            $wp_query->set('mailoptin-ajax', sanitize_text_field($_GET['mailoptin-ajax']));
        }

        if ($action = $wp_query->get('mailoptin-ajax')) {
            $this->mo_ajax_headers();
            do_action('mailoptin_ajax_' . sanitize_text_field($action));
            wp_die();
        }
    }

    /**
     * Send headers for MailOptin Ajax Requests.
     *
     * @since 2.5.0
     */
    private static function mo_ajax_headers()
    {
        send_origin_headers();
        @header('Content-Type: text/html; charset=' . get_option('blog_charset'));
        @header('X-Robots-Tag: noindex');
        send_nosniff_header();
        self::do_not_cache();
        status_header(200);
    }

    public static function do_not_cache()
    {
        if ( ! defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }

        if ( ! defined('DONOTCACHEDB')) {
            define('DONOTCACHEDB', true);
        }

        if ( ! defined('DONOTMINIFY')) {
            define('DONOTMINIFY', true);
        }

        if ( ! defined('DONOTCDN')) {
            define('DONOTCDN', true);
        }

        if ( ! defined('DONOTCACHCEOBJECT')) {
            define('DONOTCACHCEOBJECT', true);
        }

        // Set the headers to prevent caching for the different browsers.
        nocache_headers();
    }

    /**
     * Send template customizer test email.
     */
    public function send_test_email()
    {
        // if not in admin dashboard, bail
        if ( ! is_admin()) {
            return;
        }

        check_ajax_referer('mailoptin-send-test-email-nonce', 'security');

        if ( ! current_user_has_privilege()) {
            return;
        }

        $admin_email       = apply_filters('mailoptin_email_campaign_test_admin_email', get_option('admin_email'));
        $email_campaign_id = absint($_REQUEST['email_campaign_id']);
        $campaign_subject  = Misc::parse_email_subject(EmailCampaignRepository::get_customizer_value($email_campaign_id, 'email_campaign_subject'));

        if (EmailCampaignRepository::is_newsletter($email_campaign_id)) {
            $campaign_subject = Misc::parse_email_subject(EmailCampaignRepository::get_customizer_value($email_campaign_id, 'email_campaign_title'));
        }

        $campaign_type = EmailCampaignRepository::get_email_campaign_type($email_campaign_id);

        $plugin_settings = new Settings();
        $from_name       = $plugin_settings->from_name();
        $from_email      = $plugin_settings->from_email();
        $headers         = ["Content-Type: text/html", "From: $from_name <$from_email>"];

        /** call appropriate method to get template preview. Eg @see self::new_publish_post_preview() */
        $data = $this->{"{$campaign_type}_preview"}($email_campaign_id, $campaign_subject);

        $content_html            = $data[0];
        $formatted_email_subject = $data[1];

        $response = wp_mail($admin_email, $formatted_email_subject, $content_html, $headers);

        if ( ! $response) {
            $headers  = ["Content-Type: text/html"];
            $response = wp_mail($admin_email, $formatted_email_subject, $content_html, $headers);
        }

        wp_send_json(array('success' => (bool)$response));
    }

    /**
     * Handles generating preview of "new publish post" email campaign for test email sending
     *
     * @param int $email_campaign_id
     * @param string $email_campaign_subject
     *
     * @return array index0 is content_html index1 is email campaign subject.
     */
    public function new_publish_post_preview($email_campaign_id, $email_campaign_subject)
    {
        $post             = new \stdClass();
        $post->post_title = SolitaryDummyContent::title();

        $preview_post_id = EmailCampaignRepository::get_customizer_value($email_campaign_id, 'post_as_preview');

        if ( ! empty($preview_post_id)) {
            $post = get_post($preview_post_id);
        }

        return [
            (new NewPublishPostTemplatePreview($email_campaign_id))->forge(),
            NewPublishPost::format_campaign_subject($email_campaign_subject, $post)
        ];
    }

    /**
     * Handles generating preview of "posts email digest" email campaign for test email sending
     *
     * @param int $email_campaign_id
     * @param string $email_campaign_subject
     *
     * @return array index0 is content_html index1 is email campaign subject.
     */
    public function posts_email_digest_preview($email_campaign_id, $email_campaign_subject)
    {
        return [
            (new PostsEmailDigestTemplatePreview($email_campaign_id))->forge(),
            $email_campaign_subject
        ];
    }

    /**
     * Handles generating preview of newsletter email campaign for test email sending
     *
     * @param int $email_campaign_id
     * @param string $email_campaign_subject
     *
     * @return array index0 is content_html index1 is email campaign subject.
     */
    public function newsletter_preview($email_campaign_id, $email_campaign_subject)
    {
        return [
            (new NewsletterTemplatePreview($email_campaign_id))->forge(),
            $email_campaign_subject
        ];
    }

    /**
     * Filter optin designs by type.
     */
    public function optin_type_selection()
    {

        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if (empty($_REQUEST['optin-type'])) {
            wp_send_json_error(__('Unexpected error. Please try again.', 'mailoptin'));
        }

        $optin_type = sanitize_text_field($_REQUEST['optin-type']);
        if (empty($optin_type)) $optin_type = 'lightbox';

        echo '<div class="mailoptin-optin-themes mailoptin-optin-clear">';
        // lightbox/modal display should be default.

        OptinThemesRepository::listing_display_template($optin_type);
        echo '</div>';

        exit;
    }

    /**
     * Create optin slit test
     */
    public function create_optin_split_test()
    {

        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if ( ! isset($_REQUEST['variant_name'], $_REQUEST['split_note'], $_REQUEST['parent_optin_id'])) {
            wp_send_json_error();
        }

        $variant_name    = sanitize_text_field($_REQUEST['variant_name']);
        $split_note      = sanitize_text_field($_REQUEST['split_note']);
        $parent_optin_id = absint($_REQUEST['parent_optin_id']);

        if (OptinCampaignsRepository::campaign_name_exist($variant_name)) {
            wp_send_json_error(__('Optin campaign with similar variant name exist already.', 'mailoptin'));
        }

        $optin_campaign_id = (new SplitTestOptinCampaign($parent_optin_id, $variant_name, $split_note))->forge();

        if ( ! $optin_campaign_id) wp_send_json_error();

        wp_send_json_success(
            ['redirect' => OptinCampaign_List::_optin_campaign_customize_url($optin_campaign_id)]
        );
    }

    /**
     * Pause and start optin slit test
     */
    public function pause_optin_split_test()
    {

        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if ( ! isset($_POST['parent_optin_id'], $_POST['split_test_action'])) {
            wp_send_json_error();
        }

        $parent_optin_id   = absint($_POST['parent_optin_id']);
        $split_test_action = sanitize_text_field($_POST['split_test_action']);

        $variant_ids = OptinCampaignsRepository::get_split_test_variant_ids($parent_optin_id);

        foreach ($variant_ids as $variant_id) {

            if ('pause' === $split_test_action) {
                OptinCampaignsRepository::deactivate_campaign($variant_id);
            } else {
                OptinCampaignsRepository::activate_campaign($variant_id);
            }
        }

        wp_send_json_success();
    }

    /**
     * Select winner of split test
     */
    public function split_test_select_winner()
    {

        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if ( ! isset($_POST['parent_optin_id'], $_POST['winner_optin_id'])) {
            wp_send_json_error();
        }

        $parent_optin_id = absint($_POST['parent_optin_id']);
        $winner_optin_id = absint($_POST['winner_optin_id']);

        $variant_ids = OptinCampaignsRepository::get_split_test_variant_ids($parent_optin_id);

        // merge parent ID with variant IDs
        $variant_ids[] = $parent_optin_id;

        foreach ($variant_ids as $variant_id) {
            $variant_id = absint($variant_id);
            // skip deleting the winning optin.
            if ($variant_id !== $winner_optin_id) {
                OptinCampaignsRepository::delete_optin_campaign($variant_id);
            }
        }

        // ensure the winning optin do not have split test meta so it is no longer consider a variant.
        // useful if winner id was previously a variant.
        OptinCampaignMeta::delete_campaign_meta($winner_optin_id, 'split_test_parent');

        wp_send_json_success(['redirect' => MAILOPTIN_OPTIN_CAMPAIGNS_SETTINGS_PAGE]);
    }

    /**
     * End ans select winning optin split test
     */
    public function end_optin_split_modal()
    {

        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if ( ! isset($_POST['parent_optin_id'])) {
            wp_send_json_error();
        }

        $parent_optin_id = absint($_POST['parent_optin_id']);

        $variant_ids = OptinCampaignsRepository::get_split_test_variant_ids($parent_optin_id);

        // merge parent optin with variants
        array_unshift($variant_ids, $parent_optin_id);

        ob_start();
        ?>
        <div class="mo-end-test-modal">
        <div class="mo-end-test-conversion-rate">
            <div class="mo-end-test-centered">
                <h2><?php _e('Select a Winner', 'mailoptin'); ?></h2>
                <div class="mo-end-test-first-section">
                    <div class="mo-end-test-content-optin-name mo-end-test-th-header">
                        <?php _e('Optin Name', 'mailoptin'); ?>
                    </div>
                    <div class="mo-end-test-content-conversion mo-end-test-th-header">
                        <?php _e('Conversion Rate', 'mailoptin'); ?>
                    </div>
                </div>
                <?php foreach ($variant_ids as $variant_id) : ?>
                    <?php $variant_name = OptinCampaignsRepository::get_optin_campaign_name($variant_id); ?>
                    <?php $variant_conversion_rate = (new OptinCampaignStat($variant_id))->get_conversion_rate(); ?>
                    <div class="mo-end-test-first-section mo-end-test-tbody mo-end-test-clearfix" data-parent-id="<?php echo $parent_optin_id; ?>" data-optin-id="<?php echo $variant_id; ?>">
                        <div class="mo-end-test-content-optin-name"><?php echo $variant_name; ?></div>
                        <div class="mo-end-test-content-conversion">
                            <span class="mo-end-test-converted-rate"><?php echo $variant_conversion_rate; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="mo-end-test-warning-note">
                    <?php _e('Warning: selecting a winner will delete the other A/B test variants.', 'mailoptin'); ?>
                </div>

                <div class="mo-end-test-cancel-button mo-end-test-centered">
                    <a href="javascript:window.jQuery.fancybox.getInstance().close();" class="mo-end-test-btn-converted">
                        <?php _e('Cancel', 'mailoptin'); ?>
                    </a>
                </div>
                <div class="mo-end-test-preloader">
                    <img class="mo-spinner mo-end-test-spinner" id="mo-split-end-test-spinner" src="<?php echo admin_url('images/spinner.gif'); ?>">
                </div>
                <div id="mo-select-winner-error" class="mailoptin-error" style="display:none;text-align:center;font-weight:normal;">
                    <?php _e('An error occurred. Please try again.', 'mailoptin'); ?>
                </div>
            </div>
        </div>

        <?php
        wp_send_json_success(ob_get_clean());
    }

    /**
     * Create new optin campaign.
     */
    public function create_optin_campaign()
    {

        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if (empty($_REQUEST['title']) || empty($_REQUEST['theme']) || empty($_REQUEST['type'])) {
            wp_send_json_error(__('Unexpected error. Please try again.', 'mailoptin'));
        }

        $title = sanitize_text_field($_REQUEST['title']);
        $theme = sanitize_text_field($_REQUEST['theme']);
        $type  = sanitize_text_field($_REQUEST['type']);

        if (OptinCampaignsRepository::campaign_name_exist($title)) {
            wp_send_json_error(__('Optin campaign with similar name exist already.', 'mailoptin'));
        }

        do_action('mailoptin_before_add_optin_email_campaign');

        $response = OptinCampaignsRepository::add_optin_campaign(self::generateUniqueId(), $title, $theme, $type);

        if (is_int($response)) {
            wp_send_json_success(
                array('redirect' => OptinCampaign_List::_optin_campaign_customize_url($response))
            );
        } else {
            wp_send_json_error();
        }

        wp_die();
    }

    /**
     * Create new optin campaign.
     */
    public function create_email_campaign()
    {
        if ( ! current_user_has_privilege()) {
            return;
        }

        check_ajax_referer('mailoptin-admin-nonce', 'nonce');

        if (empty($_REQUEST['title']) || empty($_REQUEST['template']) || empty($_REQUEST['type'])) {
            wp_send_json_error(__('Unexpected error. Please try again.', 'mailoptin'));
        }

        $title    = sanitize_text_field($_REQUEST['title']);
        $template = sanitize_text_field($_REQUEST['template']);
        $type     = sanitize_text_field($_REQUEST['type']);

        if (apply_filters('mailoptin_add_new_email_campaign_limit', true) && $_POST['type'] != EmailCampaignRepository::NEWSLETTER && EmailCampaignRepository::campaign_count() >= 1) {
            $upgrade_url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=add_email_campaign_limit';
            wp_send_json_error(
                sprintf(__('Upgrade to %s to create multiple email campaigns', 'mailoptin'),
                    '<a href="' . $upgrade_url . '" target="_blank">MailOptin premium</a>'
                )
            );
        }

        if (EmailCampaignRepository::campaign_name_exist($title)) {
            wp_send_json_error(__('Email campaign with similar name exist already.', 'mailoptin'));
        }

        $email_campaign_id = EmailCampaignRepository::add_email_campaign($title, $type, $template);

        if (is_int($email_campaign_id)) {
            EmailCampaignMeta::add_meta_data($email_campaign_id, 'created_at', current_time('mysql'));
            wp_send_json_success(
                array('redirect' => Email_Campaign_List::_campaign_customize_url($email_campaign_id))
            );
        } else {
            wp_send_json_error();
        }

        wp_die();
    }

    /**
     * Generate unique ID for each optin form.
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateUniqueId($length = 10)
    {
        $characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * fetch connect/email provider email list.
     */
    public function customizer_fetch_email_list()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        if ( ! current_user_has_privilege()) {
            exit;
        }

        $connect = sanitize_text_field($_REQUEST['connect_service']);

        $email_list = ConnectionsRepository::connection_email_list($connect);

        wp_send_json_success($email_list);

        wp_die();
    }

    public function customizer_rename_optin()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        if ( ! current_user_has_privilege()) {
            exit;
        }

        if (moVar($_POST, 'optin_campaign_id', false, true)) {
            OptinCampaignsRepository::updateCampaignName(
                sanitize_text_field($_REQUEST['title']),
                absint($_REQUEST['optin_campaign_id'])
            );
        }

        if (moVar($_POST, 'email_campaign_id', false, true)) {
            EmailCampaignRepository::update_campaign_name(
                sanitize_text_field($_REQUEST['title']),
                absint($_REQUEST['email_campaign_id'])
            );
        }

        wp_send_json_success();

        wp_die();
    }

    public function optin_toggle_active()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        if ( ! current_user_has_privilege()) {
            exit;
        }

        $optin_campaign_id = absint($_POST['id']);
        $status            = sanitize_text_field($_POST['status']);

        if ($status == 'true') {
            OptinCampaignsRepository::activate_campaign($optin_campaign_id);
        } else {
            OptinCampaignsRepository::deactivate_campaign($optin_campaign_id);
        }

        wp_die();
    }

    public function automation_toggle_active()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        if ( ! current_user_has_privilege()) {
            exit;
        }

        $email_campaign_id = absint($_POST['id']);
        $status            = sanitize_text_field($_POST['status']);

        if ($status == 'true') {
            EmailCampaignRepository::activate_email_campaign($email_campaign_id);
        } else {
            EmailCampaignRepository::deactivate_email_campaign($email_campaign_id);
        }

        wp_die();
    }

    /**
     * Toggle activation of optin camapigns in WP List
     */
    public function toggle_optin_activated()
    {
        if ( ! current_user_has_privilege()) {
            exit;
        }

        $optin_campaign_id = absint($_POST['id']);
        $status            = sanitize_text_field($_POST['status']);

        if ($status == 'true') {
            OptinCampaignsRepository::activate_campaign($optin_campaign_id);
        } else {
            OptinCampaignsRepository::deactivate_campaign($optin_campaign_id);
        }

        wp_die();
    }

    public function toggle_automation_activated()
    {
        if ( ! current_user_has_privilege()) {
            exit;
        }

        $email_campaign_id = absint($_POST['id']);
        $status            = sanitize_text_field($_POST['status']);

        if ($status == 'true') {
            EmailCampaignRepository::activate_email_campaign($email_campaign_id);
        } else {
            EmailCampaignRepository::deactivate_email_campaign($email_campaign_id);
        }

        wp_die();
    }

    /**
     * Add subscriber to a connected service mailing list.
     */
    public function subscribe_to_email_list()
    {
        if ( ! isset($_REQUEST['optin_data'])) wp_send_json_error();

        $builder             = new ConversionDataBuilder();
        $builder->payload    = $payload = apply_filters('mailoptin_optin_subscription_request_body', sanitize_data($_REQUEST['optin_data']));
        $builder->optin_uuid = $optin_uuid = $payload['optin_uuid'];

        $builder->optin_campaign_id = isset($payload['optin_campaign_id']) && ! empty($payload['optin_campaign_id']) ? absint($payload['optin_campaign_id']) : absint(OptinCampaignsRepository::get_optin_campaign_id_by_uuid($optin_uuid));
        $builder->email             = $payload['email'];
        $builder->name              = isset($payload['name']) ? $payload['name'] : '';
        $builder->user_agent        = $payload['user_agent'];
        $builder->conversion_page   = $payload['conversion_page'];
        $builder->referrer          = $payload['referrer'];

        $response = self::do_optin_conversion($builder);

        wp_send_json($response);
    }

    public static function no_email_provider_or_list_error()
    {
        return sprintf(
            __('No email provider or list has been set for this optin. %sSee fix here%s', 'mailoptin'),
            '<a target="_blank" href="https://mailoptin.io/article/fix-error-no-email-provider-or-list-set-for-this-optin/">',
            '</a>'
        );
    }

    /**
     * Accept wide range of optin conversion data and save the lead.
     *
     * @param ConversionDataBuilder $conversion_data
     *
     * @return array
     */
    public static function do_optin_conversion(ConversionDataBuilder $conversion_data)
    {
        /** @var \WP_Error $error */
        $error = apply_filters('mo_subscription_form_error', '', $conversion_data);

        if (is_wp_error($error)) {
            return AbstractConnect::ajax_failure($error->get_error_message());
        }

        // honeypot check
        if ( ! empty($conversion_data->payload['mo-hp-email']) || ! empty($conversion_data->payload['mo-hp-website'])) {
            return AbstractConnect::ajax_failure(
                apply_filters('mo_optin_campaign_honeypot_error', __('Your submission has been flagged as potential spam.', 'mailoptin'))
            );
        }

        $optin_campaign_id = $conversion_data->optin_campaign_id;

        $no_email_provider_or_list_error = self::no_email_provider_or_list_error();

        if ( ! is_email($conversion_data->email)) {
            return AbstractConnect::ajax_failure(
                apply_filters('mo_subscription_invalid_email_error', __('Email address is invalid. Try again.', 'mailoptin'))
            );
        }

        if ($conversion_data->is_timestamp_check_active === true) {

            $timestamp = $conversion_data->payload['_mo_timestamp'];

            // make sure `_mo_timestamp` is at least 1.5 seconds ago
            // culled from https://twitter.com/w3guy/status/869576726296358915
            if (empty($timestamp) || time() < (intval($timestamp) + 1.5)) {
                return AbstractConnect::ajax_failure(
                    apply_filters('mo_subscription_invalid_spam_error', __('Your submission has been flagged as potential spam.', 'mailoptin'))
                );
            }
        }

        $optin_campaign_type = isset($conversion_data->optin_campaign_type) ? $conversion_data->optin_campaign_type : OptinCampaignsRepository::get_optin_campaign_type($optin_campaign_id);

        $lead_bank_only = OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'lead_bank_only', false);

        $form_custom_fields = OptinCampaignsRepository::form_custom_fields($optin_campaign_id);

        $lead_custom_fields_data = [];

        if (is_array($form_custom_fields) && ! empty($form_custom_fields)) {
            foreach ($form_custom_fields as $custom_field) {
                $field_key = $custom_field['cid'];
                $title     = $custom_field['placeholder'];
                if ( ! empty($conversion_data->payload[$field_key])) {
                    $lead_custom_fields_data[$title] = $conversion_data->payload[$field_key];
                }
            }
        }

        $lead_data = [
            'optin_campaign_id'   => $optin_campaign_id,
            'optin_campaign_type' => $optin_campaign_type,
            'name'                => $conversion_data->name,
            'email'               => $conversion_data->email,
            'custom_fields'       => json_encode($lead_custom_fields_data),
            'user_agent'          => $conversion_data->user_agent,
            'conversion_page'     => $conversion_data->conversion_page,
            'referrer'            => $conversion_data->referrer,
        ];

        // lite should also store leads in leadbank albeit locked.
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM') || (class_exists('MailOptin\Libsodium\LeadBank\LeadBank') && ! LeadBank::is_leadbank_disabled())) {
            // capture optin lead / conversion
            OptinConversionsRepository::add($lead_data);
        }

        // kick-in if only lead bank should be used
        if ($lead_bank_only === true) {
            // record optin campaign conversion.
            self::track_conversion($optin_campaign_id, $lead_data);

            return AbstractConnect::ajax_success();
        }

        // we are not checking if $connection_email_list is set because it can be null when supplied by elementor connection such as convertfox.
        $connection_service = isset($conversion_data->connection_service) ? $conversion_data->connection_service : '';

        if ( ! empty($connection_service)) {

            // for Elementor, WPForms integration that has leadbank among the list of email service.
            if ($connection_service == 'leadbank') {
                return AbstractConnect::ajax_success();
            }

            $connection_email_list = isset($conversion_data->connection_email_list) ? $conversion_data->connection_email_list : '';
            $response              = self::add_lead_to_connection($connection_service, $connection_email_list, $conversion_data);

            if (AbstractConnect::is_ajax_success($response)) {
                self::track_conversion($optin_campaign_id, $lead_data);
            }

            return $response;
        }

        $integrations = json_decode(
            OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'integrations', ''),
            true
        );

        if ( ! $integrations) {

            AbstractConnect::send_optin_error_email($optin_campaign_id, $no_email_provider_or_list_error);

            return AbstractConnect::ajax_failure($no_email_provider_or_list_error);
        }

        $responses = [];
        if (is_array($integrations) && ! empty($integrations)) {
            foreach ($integrations as $integration) {
                $conversion_data->payload['integration_data'] = $integration;
                $responses[]                                  = self::add_lead_to_connection(
                    $integration['connection_service'],
                    isset($integration['connection_email_list']) ? $integration['connection_email_list'] : '',
                    $conversion_data
                );
            }
        }

        // if we get here, it means we have multiple integration tied to the optin campaign
        $is_any_success = false;
        foreach ($responses as $response) {
            if (AbstractConnect::is_ajax_success($response)) {
                $is_any_success = true;
                break;
            }
        }

        if ($is_any_success) {
            self::track_conversion($optin_campaign_id, $lead_data);

            return AbstractConnect::ajax_success();
        }

        // if we get here, it means all integration responses failed. so return the first error message
        // which is a generic "There was an error saving your contact. Please try again." error.
        return $responses[0];
    }

    /**
     * Record optin campaign conversion
     *
     * @param int $optin_campaign_id
     * @param mixed $lead_data
     */
    public static function track_conversion($optin_campaign_id, $lead_data)
    {
        // record optin campaign conversion.
        (new OptinCampaignStat($optin_campaign_id))->save('conversion');

        do_action('mailoptin_track_conversions', $lead_data, $optin_campaign_id);
    }

    public static function add_lead_to_connection($connection_service, $connection_email_list, $conversion_data)
    {
        $optin_campaign_id = $conversion_data->optin_campaign_id;

        $no_email_provider_or_list_error = self::no_email_provider_or_list_error();

        $connection_fqn_class = ConnectionFactory::get_fqn_class($connection_service);

        if (empty($connection_service) || empty($connection_email_list)
        ) {
            AbstractConnect::send_optin_error_email($optin_campaign_id, $no_email_provider_or_list_error);

            return AbstractConnect::ajax_failure($no_email_provider_or_list_error);
        }

        $extras                          = $conversion_data->payload;
        $extras['optin_campaign_id']     = $optin_campaign_id;
        $extras['connection_service']    = $connection_service;
        $extras['connection_email_list'] = $connection_email_list;
        // useful for third party integration to specify custom fields.
        if ( ! empty($conversion_data->form_custom_field_mappings)) {
            $extras['form_custom_field_mappings'] = $conversion_data->form_custom_field_mappings;
        }
        // useful for third party integration to specify subscribers tags.
        if ( ! empty($conversion_data->form_tags)) {
            $extras['form_tags'] = $conversion_data->form_tags;
        }

        do_action_ref_array('mailoptin_before_optin_subscription', $extras);

        $instance = ConnectionFactory::make($connection_service);

        $response = $instance->subscribe($conversion_data->email, $conversion_data->name, $connection_email_list, $extras);

        do_action_ref_array('mailoptin_after_optin_subscription', $extras);

        return $response;
    }

    /**
     * Track optin impression.
     */
    public function track_optin_impression()
    {
        $disable_impression = apply_filters('mo_disable_impression_tracking', Settings::instance()->disable_impression_tracking());
        if ( ! empty($disable_impression) && ($disable_impression == 'true' || $disable_impression === true)) {
            return;
        }

        $payload           = sanitize_data($_REQUEST['stat_data']);
        $optin_uuid        = $payload['optin_uuid'];
        $optin_campaign_id = OptinCampaignsRepository::get_optin_campaign_id_by_uuid($optin_uuid);
        $stat_type         = 'impression';
        (new OptinCampaignStat($optin_campaign_id))->save($stat_type);

        do_action('mailoptin_track_impressions', $payload, $optin_campaign_id, $optin_uuid);
    }

    /**
     * Prints error log
     */
    public function view_error_log()
    {
        check_ajax_referer('mailoptin-log');

        if (current_user_has_privilege()) {
            $file           = sanitize_text_field($_REQUEST['file']);
            $error_log_file = MAILOPTIN_OPTIN_ERROR_LOG . $file . '.log';

            // Return an empty string if the file does not exist
            if ( ! file_exists($error_log_file)) {
                exit;
            }

            //Maybe delete log
            if (isset($_GET['delete']) && ( ! empty($_GET['delete']) || '1' == $_GET['delete'])) {
                unlink($error_log_file);
                die(__('Error log successfully deleted', 'mailoptin'));
            }

            //Stream the log file
            $url     = esc_url(add_query_arg('delete', '1'));
            $confirm = __('This will delete the error log forever. Press OK to confirm', 'mailoptin');
            $message = __('Delete Error Log', 'mailoptin');

            $onclick = "onclick=\"return confirm('$confirm')\"";
            echo "<a href='$url' style='background: #cc0000;color: #fff;text-decoration: none;padding: 5px;font-size: 14px;' $onclick>$message</a><pre>";
            readfile($error_log_file);
            echo '</pre>';
        }

        exit;
    }

    /**
     * Let's the user switch themes in the campaign customizer
     */
    public function customizer_get_templates()
    {
        check_ajax_referer('mailoptin-themes');

        if (current_user_has_privilege() && ! empty($_REQUEST['id'])) {

            //Fetch the campaign type
            $type = OptinCampaignsRepository::get_optin_campaign_type($_REQUEST['id']);

            //And output themes belonging to this type to the user
            echo '<div class="mailoptin-optin-themes mailoptin-optin-clear">';
            OptinThemesRepository::listing_display_template($type);
            echo '</div>';

        } else {
            wp_die(-1, 403);
        }

        exit;
    }

    /**
     * Set the template of an optin
     */
    public function customizer_set_template()
    {
        check_ajax_referer('mailoptin-themes');

        if (current_user_has_privilege() && ! empty($_REQUEST['id']) && ! empty($_REQUEST['theme'])) {

            $id    = $_REQUEST['id'];
            $theme = $_REQUEST['theme'];
            if (false === OptinCampaignsRepository::set_optin_campaign_class($id, $theme)) {
                wp_die(-1, 403);
            }

        } else {
            wp_die(-1, 403);
        }

        exit;
    }

    /**
     * Set the template of an optin
     */
    public function ecb_fetch_post_type_posts()
    {
        check_ajax_referer('customizer-fetch-email-list', 'nonce');

        if ( ! current_user_has_privilege()) exit;

        if (isset($_POST['default_selections']) && is_array($_POST['default_selections'])) {
            $formatted_result = [];
            foreach ($_POST['default_selections'] as $post_id) {
                $formatted_result[] = ['id' => $post_id, 'text' => get_the_title($post_id)];
            }

            wp_send_json($formatted_result, 200);
        }

        $post_type = sanitize_text_field($_POST['post_type']);
        $search    = sanitize_text_field($_POST['search']);

        $result = ControlsHelpers::get_post_type_posts($post_type, 1000, 'publish', $search);

        $formatted_result = [];
        foreach ($result as $key => $value) {
            $formatted_result[] = ['id' => $key, 'text' => $value];
        }

        wp_send_json(['results' => $formatted_result], 200);
    }

    public function customizer_email_automation_get_taxonomy()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        $custom_post_type = sanitize_text_field($_POST['custom_post_type']);

        if ( ! empty($custom_post_type)) {
            ob_start();
            $this->render_fields($custom_post_type);
            wp_send_json_success(ob_get_clean());
        }

        wp_send_json_error();
    }

    public function customizer_optin_map_custom_field()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        $connection            = sanitize_text_field($_POST['connect_service']);
        $list_id               = sanitize_text_field($_POST['list_id']);
        $custom_fields         = stripslashes(sanitize_text_field($_POST['custom_fields']));
        $custom_field_mappings = stripslashes(sanitize_text_field($_POST['custom_field_mappings']));
        $integration_index     = sanitize_text_field($_POST['integration_index']);

        $close_btn = '<div class="mo-optin-map-custom-field-close"></div>';

        if (empty($custom_fields)) wp_send_json_error($close_btn . __('Error: You have no custom field added to your optin. Consider adding one.', 'mailoptin'));

        $custom_fields = json_decode($custom_fields, true);

        $merge_fields = ConnectionFactory::make($connection)->get_optin_fields($list_id);

        if (empty($merge_fields)) wp_send_json_error($close_btn . __('Error: No integration field found. Select a list first if you haven\'t and try again.', 'mailoptin'));

        $response = $close_btn;
        $response .= '<div style="text-align:center" class="customize-control-title">';
        $response .= __('Map integration fields to form custom fields', 'mailoptin');
        $response .= '</div>';
        $response .= apply_filters('mo_optin_customizer_field_map_description', '', $connection, $list_id);

        if ( ! empty($custom_field_mappings)) {
            $custom_field_mappings = json_decode($custom_field_mappings, true);
        }

        foreach ($merge_fields as $key => $label) {
            $response .= '<div class="mo-integration-block">';
            $response .= "<label for='' class='customize-control-title'>$label</label>";
            $response .= "<select id=\"$key\" class=\"mo-optin-custom-field-select\" name=\"$key\">";
            $response .= '<option value="">' . __('Select...', 'mailoptin') . '</option>';
            foreach ($custom_fields as $custom_field) {
                $response .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    $custom_field['cid'],
                    selected($custom_field_mappings[$integration_index][$key], $custom_field['cid'], false),
                    $custom_field['placeholder']
                );
            }
            $response .= '</select>';
            $response .= '</div>';
        }

        $response .= '<div class="mo-integration-block">';
        $response .= '<button type="button" class="button button-primary mo-optin-field-map-save">' . __('Save', 'mailoptin') . '</button>';
        $response .= '</div>';

        wp_send_json_success($response);
    }

    /**
     * Handle search done on page target chosen field.
     */
    public function page_targeting_search()
    {
        $q           = sanitize_text_field($_REQUEST['q']);
        $search_type = sanitize_text_field($_REQUEST['search_type']);
        $response    = array();

        switch ($search_type) {
            case 'posts_never_load' :
                $response = ControlsHelpers::get_post_type_posts('post', 1000, 'publish', $q);
                break;
            case 'woocommerce_products' :
                $response = ControlsHelpers::get_post_type_posts('product', 1000, 'publish', $q);
                break;
            case 'pages_never_load' :
                $response = ControlsHelpers::get_post_type_posts('page', 1000, 'publish', $q);
                break;
            case 'cpt_never_load' :
                $response = ControlsHelpers::get_all_post_types_posts(array('post', 'page'), 1000, $q);
                break;
            case 'exclusive_post_types_posts_load' :
                $response = ControlsHelpers::get_all_post_types_posts([], 1000, $q);
                break;
        }

        wp_send_json($response);
    }

    /**
     * Save state of dismissible toastr notification.
     */
    public function dismiss_toastr_notifications()
    {
        $optin_campaign_id = sanitize_text_field($_POST['optin_id']);
        $notification      = sanitize_text_field($_POST['notification']);
        (new StateRepository())->set($notification, absint($optin_campaign_id));
    }

    /**
     * @return AjaxHandler
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}