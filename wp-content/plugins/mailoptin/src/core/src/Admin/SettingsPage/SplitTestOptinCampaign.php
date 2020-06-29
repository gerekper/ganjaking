<?php

namespace MailOptin\Core\Admin\SettingsPage;

use MailOptin\Core\AjaxHandler;
use MailOptin\Core\Repositories\OptinCampaignMeta;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class SplitTestOptinCampaign
{
    protected $parent_optin_id;

    /**
     * @param int $parent_optin_id
     * @param string $name
     * @param string $note
     */
    public function __construct($parent_optin_id, $name, $note)
    {
        $this->parent_optin_id = $parent_optin_id;
        $this->name = $name;
        $this->note = $note;
    }

    /**
     * Do the variant creation.
     *
     * @return bool
     */
    public function forge()
    {
        $clonee = OptinCampaignsRepository::get_optin_campaign_by_id($this->parent_optin_id);

        $optin_campaign_id = OptinCampaignsRepository::add_optin_campaign(
            AjaxHandler::generateUniqueId(),
            $this->name,
            $clonee['optin_class'],
            $clonee['optin_type']
        );

        if ($optin_campaign_id === false) return false;

        OptinCampaignMeta::add_campaign_meta($optin_campaign_id, 'split_test_parent', $this->parent_optin_id);

        $all_optin_campaign_settings = OptinCampaignsRepository::get_settings();
        // append new template settings to existing settings.
        $all_optin_campaign_settings[$optin_campaign_id] = OptinCampaignsRepository::get_settings_by_id($this->parent_optin_id);
        $all_optin_campaign_settings[$optin_campaign_id]['split_test_note'] = $this->note;
        $all_optin_campaign_settings[$optin_campaign_id]['activate_optin'] = true;
        // save to DB
        OptinCampaignsRepository::updateSettings($all_optin_campaign_settings);

        return $optin_campaign_id;
    }
}