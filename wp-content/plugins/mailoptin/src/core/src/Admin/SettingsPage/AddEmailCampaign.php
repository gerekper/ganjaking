<?php

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

use MailOptin\Core\Repositories\EmailCampaignRepository;
use MailOptin\Core\Repositories\EmailTemplatesRepository;
use W3Guy\Custom_Settings_Page_Api;

class AddEmailCampaign extends AbstractSettingsPage
{
    /**
     * Array of email campaign types available.
     *
     * @return array
     */
    public function email_campaign_types()
    {
        return apply_filters('mo_email_campaign_types', [
            EmailCampaignRepository::NEW_PUBLISH_POST   => __('New Post Notification', 'mailoptin'),
            EmailCampaignRepository::POSTS_EMAIL_DIGEST => __('Posts Email Digest', 'mailoptin')
        ]);
    }

    /**
     * Back to campaign overview button.
     */
    public function back_to_optin_overview()
    {
        $url = MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE;
        echo "<a class=\"add-new-h2\" style='margin-left: 10px;' href=\"$url\">" . __('Back to Overview', 'mailoptin') . '</a>';
    }

    /**
     * Sub-menu header for optin theme types.
     */
    public function add_email_campaign_settings_header()
    {
        if ( ! empty($_GET['page']) && $_GET['page'] == MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_SLUG) {
            ?>
            <div class="mailoptin-optin-new-list mailoptin-optin-clear">
                <strong><?php _e('Title', 'mailoptin'); ?></strong>
                <input type="text" name="mailoptin-optin-campaign" id="mailoptin-add-campaign-title" placeholder="<?php _e('Enter a name for this automation...', 'mailoptin'); ?>">
            </div>
            <div class="mailoptin-optin-new-list mailoptin-new-toolbar mailoptin-optin-clear">
                <strong><?php _e('Select Type', 'mailoptin'); ?></strong>
                <span class="spinner mo-dash-spinner"></span>
                <?php $this->_build_campaign_types_select_dropdown(); ?>
            </div>
        <?php }
    }

    /**
     * Build the settings page structure. I.e tab, sidebar.
     */
    public function settings_admin_page()
    {
        add_action('wp_cspa_before_closing_header', [$this, 'back_to_optin_overview']);
        add_action('wp_cspa_before_post_body_content', array($this, 'add_email_campaign_settings_header'), 10, 2);
        add_filter('wp_cspa_main_content_area', [$this, 'campaign_available_email_templates']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->page_header(__('Add Email Automation', 'mailoptin'));
        $this->register_core_settings($instance);
        $instance->build(true, true);
    }

    /**
     * Email Automation types select dropdown
     */
    protected function _build_campaign_types_select_dropdown()
    {
        echo '<select name="mo_email_newsletter_title"   id="mo-email-newsletter-title">';
        echo sprintf('<option value="...">%s</option>', __('Select...', 'mailoptin'));
        foreach ($this->email_campaign_types() as $key => $value) {
            echo sprintf('<option value="%s">%s</option>', $key, $value);
        }
        echo '</select>';
    }

    /**
     * Display available email template for selected campaign type.
     */
    public function campaign_available_email_templates()
    {
        $this->template_listing_tmpl(EmailCampaignRepository::NEW_PUBLISH_POST);
        $this->template_listing_tmpl(EmailCampaignRepository::POSTS_EMAIL_DIGEST);

        do_action('mo_campaign_available_email_templates');
    }

    public function template_listing_tmpl($campaign_type)
    {
        echo "<div id=\"notifType_{$campaign_type}\" class=\"mailoptin-email-templates mailoptin-template-clear\" style=\"display:none\">";
        if ($campaign_type == EmailCampaignRepository::POSTS_EMAIL_DIGEST && ! apply_filters('mailoptin_enable_post_email_digest', false)) {
            echo '<div class="mo-error-box" style="padding: 87px 10px;margin:0;">';
            printf(
                __('Posts email digest automatically sends daily, weekly or monthly round-up of published posts to your users or email list. Upgrade to %s or higher to get this cool feature.', 'mailoptin'),
                '<a href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=posts_email_digest" target="_blank">MailOptin Pro plan</a>'
            );
            echo '</div>';
            echo '</div>';

            return;
        }
        foreach (EmailTemplatesRepository::get_by_type($campaign_type) as $email_template) {

            $template_name  = $email_template['name'];
            $template_class = $email_template['template_class'];
            $screenshot     = $email_template['screenshot'];
            ?>
            <div id="mailoptin-email-template-list"
                 class="mailoptin-email-template mailoptin-email-template-<?php echo $template_class; ?>"
                 data-email-template="<?php echo $template_class; ?>"
                 data-campaign-type="<?php echo $campaign_type; ?>">
                <div class="mailoptin-email-template-screenshot">
                    <img src="<?php echo $screenshot; ?>" alt="<?php echo $template_name; ?>">
                </div>
                <h3 class="mailoptin-email-template-name"><?php echo $template_name . ' ' . __('Template', 'mailoptin'); ?></h3>
                <div class="mailoptin-email-template-actions">
                    <a class="button button-primary mailemail-template-select"
                       data-email-template="<?php echo $template_class; ?>"
                       data-campaign-type="<?php echo $campaign_type; ?>"
                       title="<?php _e('Select this template', 'mailoptin'); ?>">
                        <?php _e('Select Template', 'mailoptin'); ?>
                    </a>
                </div>
            </div>
            <?php
        }
        $this->code_your_own_box($campaign_type);
        echo '</div>';
    }

    public function code_your_own_box($campaign_type)
    {
        if ( ! apply_filters('mailoptin_enable_post_email_digest', false)) return;

        $label = __('Code Your Own', 'mailoptin');
        ?>
        <div id="mailoptin-email-template-list"
             class="mailoptin-email-template"
             data-email-template="HTML"
             data-campaign-type="<?php echo $campaign_type; ?>">
            <div class="mailoptin-email-template-screenshot">
                <img src="<?php echo MAILOPTIN_ASSETS_URL . 'images/email-templates/code-your-own.jpg' ?>" alt="<?php echo $label; ?>">
            </div>
            <h3 class="mailoptin-email-template-name" style="visibility:hidden"><?php echo $label; ?></h3>
            <div class="mailoptin-email-template-actions">
                <a class="button button-primary mailemail-template-select"
                   data-email-template="<?php echo EmailCampaignRepository::CODE_YOUR_OWN_TEMPLATE; ?>"
                   data-campaign-type="<?php echo $campaign_type; ?>"
                   title="<?php echo $label; ?>">
                    <?php echo $label; ?>
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * @return AddEmailCampaign
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