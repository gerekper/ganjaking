<?php

namespace MailOptin\SendGridConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use function MailOptin\Core\strtotime_utc;

class SendCampaign extends AbstractSendGridConnect
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

            $suppression_group = $query_string = EmailCampaignRepository::get_customizer_value(
                $this->email_campaign_id,
                'SendGridConnect_suppression_group'
            );

            $sender_id = $this->connections_settings->sendgrid_sender();

            if (empty($suppression_group)) {
                throw new \Exception('Suppression group not defined');
            }

            if (empty($sender_id)) {
                throw new \Exception('Sender ID is not defined');
            }

            $payload = [
                'name'         => $campaign_title,
                'send_at'      => gmdate('Y-m-d\TH:i:s\Z', strtotime_utc('+1 hour')),
                'send_to'      => ['list_ids' => [$list_id]],
                'email_config' => [
                    'subject'              => $this->campaign_subject,
                    'html_content'         => $this->content_html,
                    'plain_content'        => $this->content_text,
                    'editor'               => 'code',
                    'suppression_group_id' => absint($suppression_group),
                    'sender_id'            => absint($sender_id)
                ]
            ];

            if ($list_id == 'none') {
                $payload['send_to']        = [];
                $payload['send_to']['all'] = true;
            }

            $response = $this->sendgrid_instance()->make_request(
                '/marketing/singlesends',
                apply_filters('mailoptin_sendgrid_campaign_settings', $payload, $this->email_campaign_id),
                'post'
            );

            if (isset($response['body']['id'])) {
                $campaign_id = $response['body']['id'];

                $result = $this->sendgrid_instance()->make_request(
                    sprintf('marketing/singlesends/%s/schedule', $campaign_id),
                    ['send_at' => 'now'],
                    'put'
                );

                if (self::is_http_code_success($result['status_code'])) {
                    AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'sendgrid_campaign_id', $campaign_id);

                    return parent::ajax_success();
                }
            }

            return parent::ajax_failure(__('Unexpected error. Please try again', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($e->getMessage());
        }
    }
}