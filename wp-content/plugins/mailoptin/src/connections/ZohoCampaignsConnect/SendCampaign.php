<?php

namespace MailOptin\ZohoCampaignsConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class SendCampaign extends AbstractZohoCampaignsConnect
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
     * Filter our non-null values.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isNotNull($value)
    {
        return ! is_null($value);
    }

    /**
     * Send campaign via Constant Contact.
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $preview_uuid = $this->campaignlog_id_to_uuid($this->campaign_log_id, 'zohocampaigns_email_fetcher');
            $home_url     = defined('W3GUY_LOCAL') ? 'http://e0944dde.ngrok.io/' : home_url();

            $payload = apply_filters('mailoptin_zohocampaigns_campaign_settings', [
                'campaignname' => $campaign_title,
                'from_email'   => Settings::instance()->from_email(),
                'subject'      => $this->campaign_subject,
                'content_url'  => add_query_arg(['zohocampaigns_preview_type' => 'html', 'uuid' => $preview_uuid], $home_url),
                'list_details' => json_encode([
                    $list_id => []
                ]),
            ],
                $this->email_campaign_id
            );

            $response = $this->zcInstance()->apiRequest('createCampaign?resfmt=json', 'POST', $payload);


            if (isset($response->campaignKey)) {
                // add a check here to see if the campaign was sent
                $response2 = $this->zcInstance()->apiRequest('sendcampaign?resfmt=JSON', 'POST', ['campaignkey' => $response->campaignKey]);

                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'zohocampaigns_campaign_id', $response->campaignKey);

                // if we get here, campaign was sent because no exception was thrown by sendEmailCampaign().
                return parent::ajax_success();
            }

            $err = __('Unexpected error. Please try again', 'mailoptin');
            self::save_campaign_error_log(json_encode($response), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($err);

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($e->getMessage());
        }
    }
}