<?php

namespace MailOptin\GetResponseConnect;

use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractGetResponseConnect
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
     * Send campaign via GetResponse.
     *
     * @return array
     */
    public function send()
    {
        try {

            $response = (array)$this->getresponse_instance()->getFromFields(['perPage' => 1]);

            if (isset($response['message'], $response['moreInfo'])) {
                self::save_campaign_error_log(json_encode($response), $this->campaign_log_id, $this->email_campaign_id);
            }

            $from_field_id = $response[0]->fromFieldId;

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $result = $this->getresponse_instance()->sendNewsletter([
                "subject" => $this->campaign_subject,
                "fromField" => ['fromFieldId' => $from_field_id],
                "replyTo" => ['fromFieldId' => $from_field_id],
                "content" => [
                    'html' => $this->content_html,
                    'plain' => $this->content_text

                ],
                'campaign' => ['campaignId' => $list_id],
                'flags' => ['openrate', 'clicktrack', 'google_analytics'],
                "sendSettings" => [
                    "selectedCampaigns" => [$list_id],
                    'timeTravel' => false,
                    'perfectTiming' => false

                ]
            ]);

            if(isset($result->newsletterId)) return parent::ajax_success();

            $err = __('Unexpected error. Please try again', 'mailoptin');
            self::save_campaign_error_log(json_encode($response), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($err);

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);
            return parent::ajax_failure($e->getMessage());
        }
    }
}