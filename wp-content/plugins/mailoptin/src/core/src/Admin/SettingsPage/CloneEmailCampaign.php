<?php

namespace MailOptin\Core\Admin\SettingsPage;

use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class CloneEmailCampaign
{
    protected $email_campaign_id;
    protected $new_email_campaign_name;

    /**
     * @param int $email_campaign_id
     * @param string $new_email_campaign_name
     */
    public function __construct($email_campaign_id, $new_email_campaign_name = '')
    {
        $this->email_campaign_id       = $email_campaign_id;
        $this->new_email_campaign_name = $new_email_campaign_name;
    }

    /**
     * Do the clone.
     *
     * @return bool
     */
    public function forge()
    {
        // get settings of email campaign being cloned
        $clone_meta_data = EmailCampaignRepository::get_email_campaign_by_id($this->email_campaign_id);

        $new_email_campaign_name = ! empty($this->new_email_campaign_name) ? $this->new_email_campaign_name : $clone_meta_data['name'] . ' - Copy';
        $new_email_campaign_name = apply_filters('mailoptin_email_campaign_clone_name', $new_email_campaign_name, $clone_meta_data);

        $new_email_campaign_id = EmailCampaignRepository::add_email_campaign(
            $new_email_campaign_name,
            $clone_meta_data['campaign_type'],
            $clone_meta_data['template_class']
        );

        EmailCampaignMeta::add_meta_data(
            $new_email_campaign_id,
            'created_at',
            current_time('mysql')
        );

        $all_templates_settings = EmailCampaignRepository::get_settings();

        // append new template settings to existing settings.
        $all_templates_settings[$new_email_campaign_id] = EmailCampaignRepository::get_settings_by_id($this->email_campaign_id);

        // deactivate cloned campaign by default
        $all_templates_settings[$new_email_campaign_id]['activate_email_campaign'] = false;

        EmailCampaignRepository::deactivate_email_campaign($new_email_campaign_id);

        if (EmailCampaignRepository::is_newsletter($new_email_campaign_id)) {
            $all_templates_settings[$new_email_campaign_id]['email_campaign_title'] = $new_email_campaign_name;
        }

        // save to DB
        return EmailCampaignRepository::updateSettings($all_templates_settings);

    }
}