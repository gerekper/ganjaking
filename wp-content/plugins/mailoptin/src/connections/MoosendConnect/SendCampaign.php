<?php

namespace MailOptin\MoosendConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractMoosendConnect
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
        $this->campaign_log_id   = $campaign_log_id;
        $this->campaign_subject  = $campaign_subject;
        $this->content_html      = $content_html;
        $this->content_text      = $content_text;
    }

    /**
     * @return array
     */
    public function send()
    {
        try {

            $list_id      = $this->get_email_campaign_list_id($this->email_campaign_id);
            $preview_uuid = $this->campaignlog_id_to_uuid($this->campaign_log_id, 'moosend_email_fetcher');

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $home_url = defined('W3GUY_LOCAL') ? 'http://828d011e9b9f.ngrok.io' : home_url();

            $created_campaign = $this->moosend_instance()->create_campaign(
                $list_id,
                apply_filters('mailoptin_moosend_campaign_settings', [
                    'Name'         => $campaign_title,
                    'Subject'      => $this->campaign_subject,
                    'SenderEmail'  => Settings::instance()->from_email(),
                    'ReplyToEmail' => Settings::instance()->from_email(),
                    'WebLocation'  => add_query_arg(['moosend_preview_type' => 'html', 'uuid' => $preview_uuid], $home_url),
                ], $this->email_campaign_id)
            );

            if (is_string($created_campaign)) {
                $this->moosend_instance()->send_campaign($created_campaign);

                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'moosend_campaign_id', $created_campaign);

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