<?php

namespace MailOptin\CleverReachConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class SendCampaign extends AbstractCleverReachConnect
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
     * Send campaign via Constant Contact v3.
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $from_name  = Settings::instance()->from_name();
            $from_email = Settings::instance()->from_email();
            $subject    = $this->campaign_subject;

            $payload = apply_filters('mailoptin_cleverreach_campaign_settings', [
                'name'         => $campaign_title,
                'subject'      => $subject,
                'sender_name'  => $from_name,
                'sender_email' => $from_email,
                'content'      => [
                    'type' => 'html/text',
                    'html' => $this->content_html,
                    'text' => $this->content_text,
                ],
                'receivers'    => [
                    'groups' => [$list_id]
                ],
                'settings'     => [
                    "editor"         => "advanced",
                    "open_tracking"  => true,
                    "click_tracking" => true
                ]
            ], $this->email_campaign_id);

            $send_campaign = $this->cleverreachInstance()->sendMailing($payload);

            if (true === $send_campaign) {
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