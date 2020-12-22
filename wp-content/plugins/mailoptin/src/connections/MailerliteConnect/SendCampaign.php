<?php

namespace MailOptin\MailerliteConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractMailerliteConnect
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
     * Send campaign via Mailerlite.
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaignData = apply_filters('mailoptin_mailerlite_campaign_settings',
                [
                    'subject'   => $this->campaign_subject,
                    'type'      => 'regular',
                    'groups'    => [$list_id],
                    'from'      => Settings::instance()->from_email(),
                    'from_name' => Settings::instance()->from_name(),
                ], $this->email_campaign_id
            );

            $campaignsApi = $this->mailerlite_instance()->campaigns();

            $campaign = $campaignsApi->create($campaignData);

            if ( ! empty($campaign->id)) {
                $campaign_id = $campaign->id;

                // save the Mailerlite campaign ID against the campaign log.
                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'mailerlite_campaign_id', $campaign_id);

                $response = $campaignsApi->addContent(
                    $campaign_id,
                    ['html' => $this->content_html, 'plain' => $this->content_text]
                );

                if (true === $response->success) {
                    if ($campaignsApi->send($campaign_id)->id) {
                        return self::ajax_success();
                    } else {
                        self::save_campaign_error_log($response->error->message, $this->campaign_log_id, $this->email_campaign_id);
                    }
                }
            }

            $err = __('Unexpected error. Please try again', 'mailoptin');
            self::save_campaign_error_log($response->error->message, $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($err);

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($e->getMessage());
        }
    }
}