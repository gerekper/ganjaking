<?php

namespace MailOptin\SendyConnect;

use MailOptin\Core\Repositories\EmailCampaignRepository;

class SendCampaign extends AbstractSendyConnect
{
    /** @var int ID of email campaign */
    public $email_campaign_id;

    /** @var int ID of email campaign */
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
        $this->campaign_log_id = $campaign_log_id;
        $this->campaign_subject = $campaign_subject;
        $this->content_html = $content_html;
        $this->content_text = $content_text;
    }

    /**
     * Send campaign via sendy.
     *
     * @return array
     */
    public function send()
    {
        try {
            $api_config = parent::api_config();

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $query_string = EmailCampaignRepository::get_customizer_value(
                $this->email_campaign_id,
                'SendyConnect_query_string'
            );

            $config = array(
                'api_key' => $api_config['api_key'],
                'installation_url' => $api_config['installation_url'],
                // this is redundant because it isn't used by create campaign. added to prevent any exception being thrown.
                'list_id' => $list_id
            );

            $sendy = new SendyPHP($config);

            $response = $sendy->createCampaign(
                array(
                    'from_name' => $this->plugin_settings->from_name(),
                    'from_email' => $this->plugin_settings->from_email(),
                    'reply_to' => $api_config['reply_to'],
                    'subject' => $this->campaign_subject,
                    'plain_text' => $this->content_text,
                    'html_text' => $this->content_html,
                    'list_ids' => $list_id,
                    // Required only if you are creating a 'Draft' campaign (send_campaign set to 0 or left as default).
                    'brand_id' => (int)apply_filters('mailoptin_sendy_brand_id', 0), // 0 value ensure
                    'query_string' => $query_string,
                    // if you want to send the campaign as well and not just create a draft. Default is 0.
                    'send_campaign' => (int)apply_filters('mailoptin_sendy_draft_campaign', 1)
                )
            );

            // $response returns ['status' => true, 'message' => 'API returned (Error) message here if there is any.']
            if (isset($response['status']) && $response['status'] === true) {
                return parent::ajax_success();
            }

            $err = !empty($response['message']) ? $response['message'] : __('Unexpected error. Please try again', 'mailoptin');
            self::save_campaign_error_log($err, $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure(__('Unexpected error. Please try again', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);
            return parent::ajax_failure($e->getMessage());
        }
    }
}