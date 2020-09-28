<?php

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

use MailOptin\Core\Repositories\EmailCampaignRepository;
use W3Guy\Custom_Settings_Page_Api;

class AddNewEmail extends AbstractSettingsPage
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
        echo "<a class=\"add-new-h2\" href=\"$url\">" . __('Back to Overview', 'mailoptin') . '</a>';
    }

    /**
     * Build the settings page structure. I.e tab, sidebar.
     */
    public function settings_admin_page()
    {
        add_action('wp_cspa_before_closing_header', [$this, 'back_to_optin_overview']);
        add_filter('wp_cspa_main_content_area', [$this, 'content']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->page_header(__('Add New Email', 'mailoptin'));
        $this->register_core_settings($instance);
        $instance->build(true, true);
    }

    public function content()
    {
        $email_automation_url = add_query_arg('view', 'add-new-email-automation', MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE);
        $newsletter_url       = add_query_arg('view', 'create-broadcast', MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE);
        ?>
        <div class="mo-add-new-form-wrapper">
            <div class="mo-design-gateway">
                <div class="mo-design-gateway-inner">
                    <div class="mo-half clearfix">
                        <div class="mo-hald-first">
                            <a href="<?= $email_automation_url; ?>">
                                <div class="mo-half-meta-inner">
                                    <div class="mo-half-first-thumb responsive-image">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="110" height="110" viewBox="0 0 24 24">
                                            <path fill="#098fe2" d="M15.91 13.34l2.636-4.026-.454-.406-3.673 3.099c-.675-.138-1.402.068-1.894.618-.736.823-.665 2.088.159 2.824.824.736 2.088.665 2.824-.159.492-.55.615-1.295.402-1.95zm-3.91-10.646v-2.694h4v2.694c-1.439-.243-2.592-.238-4 0zm8.851 2.064l1.407-1.407 1.414 1.414-1.321 1.321c-.462-.484-.964-.927-1.5-1.328zm-18.851 4.242h8v2h-8v-2zm-2 4h8v2h-8v-2zm3 4h7v2h-7v-2zm21-3c0 5.523-4.477 10-10 10-2.79 0-5.3-1.155-7.111-3h3.28c1.138.631 2.439 1 3.831 1 4.411 0 8-3.589 8-8s-3.589-8-8-8c-1.392 0-2.693.369-3.831 1h-3.28c1.811-1.845 4.321-3 7.111-3 5.523 0 10 4.477 10 10z"/>
                                        </svg>
                                    </div>
                                    <div class="mo-half-meta">
                                        <h2><?php _e('Email Automation', 'mailoptin'); ?></h2>
                                        <p><?php _e('Setup automated emails to your subscribers such as email notification after you published a new post, email digest etc.', 'mailoptin'); ?></p>
                                    </div>
                                </div>
                                <div class="mo-builder-create-btn"><?php _e('Setup Now', 'mailoptin'); ?></div>
                            </a>
                        </div>

                        <div class="mo-hald-first">
                            <a href="<?= $newsletter_url; ?>">
                                <div class="mo-half-meta-inner">
                                    <div class="mo-half-first-thumb responsive-image">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="110" height="110" viewBox="0 0 24 24">
                                            <path fill="#098fe2" d="M0 3v18h24v-18h-24zm21.518 2l-9.518 7.713-9.518-7.713h19.036zm-19.518 14v-11.817l10 8.104 10-8.104v11.817h-20z"/>
                                        </svg>
                                    </div>
                                    <div class="mo-half-meta">
                                        <h2><?php _e('Broadcast', 'mailoptin'); ?></h2>
                                        <p><?php _e('Create and send one-off emails to your subscribers informing them of any news or updates about your product or company.', 'mailoptin'); ?></p>
                                    </div>
                                </div>
                                <div class="mo-builder-create-btn"><?php _e('Create Now', 'mailoptin'); ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * @return AddNewEmail
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