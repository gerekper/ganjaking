<?php

namespace MailOptin\MailjetConnect;

use MailOptin\Core\PluginSettings\Settings;

class SendCampaign extends AbstractMailjetConnect
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
     * Send campaign via mailjet.
     *
     * @return array
     */
    public function send()
    {
        try {

            $err = __('Unexpected error. Please try again', 'mailoptin');

            $sender = $this->mailjet_instance()->make_request('sender', [], 'get');

            if (empty($sender->Data[0]->ID)) {
                self::save_campaign_error_log(print_r($sender, true), $this->campaign_log_id, $this->email_campaign_id);

                return parent::ajax_failure($err);
            }

            $sender_id = $sender->Data[0]->ID;

            $args = [
                'ContactsListID' => $this->get_email_campaign_list_id($this->email_campaign_id),
                'Title'          => $this->get_email_campaign_campaign_title($this->email_campaign_id),
                'Subject'        => $this->campaign_subject,
                'SenderEmail'    => Settings::instance()->from_email(),
                'SenderName'     => Settings::instance()->from_name(),
                'EditMode'       => 'html2',
                'Locale'         => get_locale(),
                'Sender'         => $sender_id,
            ];

            $draft_campaign = $this->mailjet_instance()->make_request('campaigndraft', $args);

            if (empty($draft_campaign->Data[0]->ID)) {
                self::save_campaign_error_log(print_r($draft_campaign, true), $this->campaign_log_id, $this->email_campaign_id);

                return parent::ajax_failure($err);
            }

            $campaign_id = $draft_campaign->Data[0]->ID;

            $args = [
                'Html-part' => $this->content_html,
                'Text-part' => $this->content_text,
            ];

            $updated_campaign = $this->mailjet_instance()->make_request("campaigndraft/$campaign_id/detailcontent", $args);

            if ( ! isset($updated_campaign->Count) || empty($updated_campaign->Count)) {
                self::save_campaign_error_log(print_r($updated_campaign, true), $this->campaign_log_id, $this->email_campaign_id);

                return parent::ajax_failure($err);
            }

            $sent_campaign = $this->mailjet_instance()->make_request("campaigndraft/$campaign_id/send");

            if ( ! isset($updated_campaign->Count) || empty($sent_campaign->Count)) {
                self::save_campaign_error_log(print_r($sent_campaign, true), $this->campaign_log_id, $this->email_campaign_id);

                return parent::ajax_failure($err);
            }

            return self::ajax_success();

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($e->getMessage());
        }
    }
}