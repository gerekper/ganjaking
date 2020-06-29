<?php

namespace MailOptin\Core\EmailCampaigns;


interface TriggerInterface
{
    /**
     * Does the actual campaign sending.
     *
     * @param int $email_campaign_id
     * @param int $campaign_id
     */
    public function send_campaign($email_campaign_id, $campaign_id);
}