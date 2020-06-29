<?php

namespace MailOptin\ActiveCampaignConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractActiveCampaignConnect
{
    /** @var int ID of email campaign */
    protected $email_campaign_id;

    /** @var int ID of campaign log */
    protected $campaign_log_id;

    /** @var string campaign subject */
    protected $campaign_subject;

    /** @var string campaign email in HTML */
    protected $content_text;

    /** @var string campaign email in plain text */
    protected $content_html;

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

        $this->email_campaign_id = $email_campaign_id;
        $this->campaign_log_id = $campaign_log_id;
        $this->campaign_subject = $campaign_subject;
        $this->content_html = $content_html;
        $this->content_text = $content_text;
    }

    /**
     * Send campaign via ActiveCampaign.
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $message = apply_filters('mailoptin_activecampaign_message_settings',
                [
                    "format" => "mime",
                    "subject" => $this->campaign_subject,
                    "fromemail" => Settings::instance()->from_email(),
                    "reply2" => Settings::instance()->from_email(),
                    "fromname" => Settings::instance()->from_name(),
                    "html" => $this->content_html,
                    "text" => $this->content_text,
                    "p[{$list_id}]" => $list_id,
                ],
                $this->email_campaign_id
            );

            $message_add = parent::activecampaign_instance()->api("message/add", $message);

            if (!(int)$message_add->success) {
                self::save_campaign_error_log($message_add->error, $this->campaign_log_id, $this->email_campaign_id);
                return parent::ajax_failure($message_add->error);
            }

            // successful request
            $message_id = (int)$message_add->id;

            $campaign = apply_filters('mailoptin_activecampaign_campaign_settings',
                [
                    "type" => "single",
                    "name" => $campaign_title,
                    "sdate" => '2013-07-01 00:00:00', // setting a time in the past will send the newsletter immediately
                    "status" => 1,
                    "public" => 1,
                    "tracklinks" => "all",
                    "trackreads" => 1,
                    "htmlunsub" => 0,
                    "textunsub" => 0,
                    "p[{$list_id}]" => $list_id,
                    "m[{$message_id}]" => 100, // 100 percent of subscribers
                ],
                $this->email_campaign_id
            );

            $campaign_create = parent::activecampaign_instance()->api("campaign/create", $campaign);

            if (!(int)$campaign_create->success) {
                self::save_campaign_error_log($campaign_create->error, $this->campaign_log_id, $this->email_campaign_id);
                return parent::ajax_failure($campaign_create->error);
            }

            if (!empty($campaign_create->id)) {
                $campaign_id = $campaign_create->id;

                // save the ActiveCampaign campaign ID against the campaign log.
                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'activecampaign_campaign_id', $campaign_id);
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