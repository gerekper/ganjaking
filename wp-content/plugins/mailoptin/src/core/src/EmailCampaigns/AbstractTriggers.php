<?php

namespace MailOptin\Core\EmailCampaigns;

use MailOptin\Core\Logging\CampaignLog;
use MailOptin\Core\Logging\CampaignLogRepository;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

/**
 * @method string send_immediately($email_campaign_id) determine if email campaign should be sent immediately.
 * @method int schedule_digit($email_campaign_id) scheduled time "digit" that campaign will go out.
 * @method string schedule_type($email_campaign_id) scheduled time "type" that campaign will go out.
 * @method string connection_service($email_campaign_id) connection or connect or email provider saved against this campaign.
 */
abstract class AbstractTriggers implements TriggerInterface
{
    protected $CampaignLogRepository;

    public function __construct()
    {
        $this->CampaignLogRepository = CampaignLogRepository::instance();
    }

    /**
     * Handles automagic fetching of email campaign customizer values.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return string
     */
    public function __call($name, $arguments)
    {
        $email_campaign_id = absint($arguments[0]);
        if (!method_exists($this, $name)) {
            return ER::get_merged_customizer_value($email_campaign_id, $name);
        }
    }


    /**
     * Save email campaign to log.
     *
     * @param int $email_campaign_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return int
     */
    public function save_campaign_log($email_campaign_id, $subject, $content_html, $content_text = '')
    {
        $content_text = empty($content_text) ? \MailOptin\Core\html_to_text($content_html) : $content_text;

        $campaign = new CampaignLog(
            array(
                'email_campaign_id' => $email_campaign_id,
                'title' => $subject,
                'content_html' => $content_html,
                'content_text' => $content_text,
            )
        );

        // save the campaign
        $campaign_id = $this->CampaignLogRepository->save($campaign);

        return absint($campaign_id);
    }


    /**
     * Update campaign status and time.
     *
     * @param int $campaign_id
     * @param string $status
     * @param null|string $status_time timestamp when status changed
     */
    public function update_campaign_status($campaign_id, $status, $status_time = null)
    {
        $this->CampaignLogRepository->updateStatus($campaign_id, $status);

        $status_time = !is_null($status_time) ? date("Y-m-d H:i:s", $status_time) : current_time('mysql');
        $this->CampaignLogRepository->updateStatusTime($campaign_id, $status_time);
    }
}