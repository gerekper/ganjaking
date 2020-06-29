<?php

namespace MailOptin\Core\Admin\SettingsPage;

use MailOptin\Core\AjaxHandler;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class CloneOptinCampaign
{
    protected $optin_campaign_id;

    /**
     * @param int $optin_campaign_id
     */
    public function __construct($optin_campaign_id)
    {
        $this->optin_campaign_id = $optin_campaign_id;
    }

    /**
     * Do the clone.
     *
     * @return bool
     */
    public function forge()
    {
        $clonee = OptinCampaignsRepository::get_optin_campaign_by_id($this->optin_campaign_id);

        $optin_campaign_id = OptinCampaignsRepository::add_optin_campaign(
            AjaxHandler::generateUniqueId(),
            apply_filters('mailoptin_optin_campaign_clone_name', $clonee['name'] . ' - Copy', $clonee),
            $clonee['optin_class'],
            $clonee['optin_type']
        );

        $all_optin_campaign_settings = OptinCampaignsRepository::get_settings();
        // append new template settings to existing settings.
        $all_optin_campaign_settings[$optin_campaign_id] = OptinCampaignsRepository::get_settings_by_id($this->optin_campaign_id);

        // deactivate cloned optin campaign by default
        $all_optin_campaign_settings[$optin_campaign_id]['activate_optin'] = false;

        return OptinCampaignsRepository::updateSettings($all_optin_campaign_settings);

    }
}