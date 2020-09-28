<?php

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

use MailOptin\Core\Repositories\EmailCampaignRepository;
use MailOptin\Core\Repositories\EmailTemplatesRepository;
use W3Guy\Custom_Settings_Page_Api;

class AddNewsletter extends AbstractSettingsPage
{
    /**
     * Back to campaign overview button.
     */
    public function back_to_optin_overview()
    {
        $url = MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE;
        echo "<a class=\"add-new-h2\" href=\"$url\">" . __('Back to Overview', 'mailoptin') . '</a>';
    }

    /**
     * Sub-menu header for optin theme types.
     */
    public function add_email_campaign_settings_header()
    {
        if ( ! empty($_GET['page']) && $_GET['page'] == MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_SLUG) {
            ?>
            <div class="mailoptin-optin-new-list mailoptin-optin-clear">
                <strong><?php _e('Email Subject', 'mailoptin'); ?></strong>
                <input type="text" name="mailoptin-optin-campaign" id="mailoptin-add-campaign-title" style="width:45%;" placeholder="<?php _e('What is the subject line for this email?', 'mailoptin'); ?>">
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
        add_filter('wp_cspa_main_content_area', [$this, 'available_email_templates']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->page_header(__('Create Broadcast', 'mailoptin'));
        $this->register_core_settings($instance);
        $instance->build(true, true);
    }

    /**
     * Display available email template for selected campaign type.
     */
    public function available_email_templates()
    {
        $this->template_listing_tmpl(EmailCampaignRepository::NEWSLETTER);

        do_action('mo_campaign_available_newsletter_templates');
    }

    public function template_listing_tmpl($campaign_type)
    {
        echo "<div id=\"notifType_{$campaign_type}\" class=\"mailoptin-email-templates mailoptin-template-clear\">";
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
     * @return AddNewsletter
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