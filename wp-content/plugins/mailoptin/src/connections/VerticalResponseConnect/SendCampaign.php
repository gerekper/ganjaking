<?php

namespace MailOptin\VerticalResponseConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractVerticalResponseConnect
{
    /** @var int ID of email campaign */
    public $email_campaign_id;

    /** @var int ID of campaign log */
    public $campaign_log_id;

    /** @var string campaign subject */
    public $campaign_subject;

    /** @var string campaign email in HTML */
    public $content_text;

    /** @var string campaign email in plain text */
    public $content_html;

    /**
     * Constructor poop.
     *
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $campaign_subject
     * @param string $content_html
     * @param string $content_text
     */
    public function __construct($email_campaign_id, $campaign_log_id, $campaign_subject, $content_html, $content_text = '')
    {
        parent::__construct();

        $this->email_campaign_id    = $email_campaign_id;
        $this->campaign_log_id      = $campaign_log_id;
        $this->campaign_subject     = $campaign_subject;
        $this->content_html         = $content_html;
        $this->content_text         = $content_text;
    }

    /**
     * Send campaign via verticalresponse
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id                = $this->get_email_campaign_list_id($this->email_campaign_id);
            $campaign_title         = $this->get_email_campaign_campaign_title($this->email_campaign_id);
            $created_campaign       = $this->verticalresponseInstance()->createDraftCampaign(
                apply_filters('mailoptin_verticalresponse_campaign_settings', [
                    'name'              => $campaign_title,
                    'subject'           => $this->campaign_subject,
                    'from_label'        => Settings::instance()->from_name(),
                    'from_address'      => Settings::instance()->from_email(),
                    'reply_to'          => Settings::instance()->from_email(),
                    'postal_code'       => Settings::instance()->company_zip(),
                    'company'           => Settings::instance()->company_name(),
                    'street_address'    => Settings::instance()->company_address(),
                    'locality'          => Settings::instance()->company_city(),
                    'region'            => Settings::instance()->company_state(),
                    'message'           => $this->content_html,
                ], $this->email_campaign_id)
            );

            if (is_string($created_campaign)) {
                $this->verticalresponseInstance()->sendDraftCampaign($created_campaign, [$list_id]);

                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'verticalresponse_campaign_id', $created_campaign);

                // if we get here, campaign was sent because no exception was thrown by sendEmailCampaign().
                return parent::ajax_success();
            }

            $err = __('Unexpected error. Please try again', 'mailoptin');
            self::save_campaign_error_log($err, $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($err);

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);
            return parent::ajax_failure($e->getMessage());
        }
    }
}