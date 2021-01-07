<?php

namespace MailOptin\SendinblueConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractSendinblueConnect
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
        $this->campaign_log_id = $campaign_log_id;
        $this->campaign_subject = $campaign_subject;
        $this->content_html = $content_html;
        $this->content_text = $content_text;
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
        return !is_null($value);
    }

    /**
     * Send campaign via Constant Contact.
     *
     * @return array
     */
    public function send()
    {
        try {
            // sendinblue require this to be an integer.
            $list_id = absint($this->get_email_campaign_list_id($this->email_campaign_id));

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $response = $this->sendinblue_instance()->make_request(
                'emailCampaigns',
                apply_filters('mailoptin_sendinblue_campaign_settings', [
                    'tag' => 'MailOptin',
                    'sender' => [
                        'name' => Settings::instance()->from_name(),
                        'email' => Settings::instance()->from_email()
                    ],
                    'name' => $campaign_title,
                    'htmlContent' => $this->content_html,
                    'subject' => $this->campaign_subject,
                    'replyTo' => Settings::instance()->reply_to(),
                    'recipients' => [
                        'listIds' => [$list_id]
                    ],
                    'type' => 'classic',
                    'mirrorActive' => false, // disable auto inserted view in browser block.
                ],
                    $this->email_campaign_id
                ),
                'post'
            );

            if (isset($response['body']->id)) {
                $campaign_id = $response['body']->id;

                $result = $this->sendinblue_instance()->make_request(
                    sprintf('emailCampaigns/%d/sendNow', $campaign_id),
                    [],
                    'post'
                );

                if ($result['status_code'] >= 200 && $result['status_code'] <= 299) {
                    AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'sendinblue_campaign_id', $campaign_id);
                    return parent::ajax_success();
                }
            }

            if (isset($response['body']->code, $response['body']->message)) {
                self::save_campaign_error_log($response['body']->code . ': ' . $response['body']->message, $this->campaign_log_id, $this->email_campaign_id);
            }

            $err = __('Unexpected error. Please try again', 'mailoptin');

            return parent::ajax_failure($err);

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);
            return parent::ajax_failure($e->getMessage());
        }
    }
}