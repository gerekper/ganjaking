<?php

namespace MailOptin\CampaignMonitorConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractCampaignMonitorConnect
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

        $this->email_campaign_id = $email_campaign_id;
        $this->campaign_log_id   = $campaign_log_id;
        $this->campaign_subject  = $campaign_subject;
        $this->content_html      = $content_html;
        $this->content_text      = $content_text;
    }

    /**
     * Send campaign via Campaign Monitor.
     *
     * @return array
     */
    public function send()
    {
        try {

            $preview_uuid = $this->campaignlog_id_to_uuid($this->campaign_log_id, 'campaignmonitor_email_fetcher');

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $home_url = defined('W3GUY_LOCAL') ? 'http://d40973e4.ngrok.io/' : home_url();

            $created_campaign_id = $this->campaignmonitorInstance()->createDraftCampaign(
                $this->client_id,
                apply_filters('mailoptin_campaignmonitor_campaign_settings', [
                    // susceptible to duplicate campaign name error
                    'Name'      => $campaign_title . ' ' . gmdate("Y-m-d H:i:s", current_time('timestamp')),
                    'Subject'   => $this->campaign_subject,
                    'FromName'  => Settings::instance()->from_name(),
                    'FromEmail' => Settings::instance()->from_email(),
                    'ReplyTo'   => Settings::instance()->reply_to(),
                    'HtmlUrl'   => add_query_arg(['campaignmonitor_preview_type' => 'html', 'uuid' => $preview_uuid], $home_url),
                    'TextUrl'   => add_query_arg(['campaignmonitor_preview_type' => 'text', 'uuid' => $preview_uuid], $home_url),
                    "ListIDs"   => [
                        "$list_id"
                    ]
                ], $this->email_campaign_id)
            );

            if (is_string($created_campaign_id)) {
                $this->campaignmonitorInstance()->sendDraftCampaign($created_campaign_id, Settings::instance()->reply_to());

                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'campaignmonitor_campaign_id', $created_campaign_id);

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