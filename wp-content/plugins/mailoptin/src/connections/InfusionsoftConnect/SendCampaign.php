<?php

namespace MailOptin\InfusionsoftConnect;

use MailOptin\Core\Repositories\EmailCampaignRepository;

class SendCampaign extends AbstractInfusionsoftConnect
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
     * Send campaign via Campaign Monitor.
     *
     * @return array
     */
    public function send()
    {
        try {

            $user_id = EmailCampaignRepository::get_customizer_value($this->email_campaign_id, 'InfusionsoftConnect_email_user_id');

            if (empty($user_id)) {
                throw new \Exception('Infusionsoft user ID to send email on behalf of is not set');
            }

            $contacts = [];

            $tags = EmailCampaignRepository::get_customizer_value($this->email_campaign_id, 'InfusionsoftConnect_email_tag_recipients');

            if (empty($tags)) {
                $contacts = $this->infusionsoftInstance()->get_contact_ids();
            } else {
                foreach ($tags as $tagID) {
                    $tcontacts = $this->infusionsoftInstance()->get_contact_ids($tagID);
                    if (is_array($tcontacts) && ! empty($tcontacts)) {
                        $contacts = array_merge($contacts, $tcontacts);
                    }
                }
            }

            // {"message":"Only 1000 Contact Ids can be sent to with a single request."}.
            $chunks = array_chunk($contacts, 980);

            foreach ($chunks as $chunk) {

                $payload = apply_filters('mailoptin_infusionsoft_email_campaign_settings', [
                    'contacts'      => $chunk,
                    'html_content'  => base64_encode($this->content_html),
                    'plain_content' => base64_encode($this->content_text),
                    "subject"       => $this->campaign_subject,
                    "user_id"       => absint($user_id)
                ]);

                $this->infusionsoftInstance()->sendEmail($payload);
            }

            return parent::ajax_success();

        } catch (\Exception $e) {
            self::save_campaign_error_log($e->getMessage(), $this->campaign_log_id, $this->email_campaign_id);

            return parent::ajax_failure($e->getMessage());
        }
    }
}