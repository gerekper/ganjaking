<?php

namespace MailOptin\MailChimpConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class SendCampaign extends AbstractMailChimpConnect
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
        $this->campaign_log_id   = $campaign_log_id;
        $this->campaign_subject  = $campaign_subject;
        $this->content_html      = $content_html;
        $this->content_text      = $content_text;
    }

    /**
     * Send campaign via MailChimp.
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $segment_id = EmailCampaignRepository::get_merged_customizer_value(
                $this->email_campaign_id,
                'MailChimpConnect_groups'
            );

            $list_segments = Connect::get_instance()->get_list_segments($list_id);

            // $segment_id has to be integer
            $recipients = ['list_id' => $list_id];

            if ( ! empty($segment_id) && in_array($segment_id, array_keys($list_segments))) {
                $recipients['segment_opts'] = [
                    'saved_segment_id' => absint($segment_id),
                ];
            }

            $recipients = (object)$recipients;

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $settings = apply_filters('mailoptin_mailchimp_campaign_settings', [
                'title'        => $campaign_title,
                'subject_line' => $this->campaign_subject,
                'from_name'    => Settings::instance()->from_name(),
                'reply_to'     => Settings::instance()->reply_to(),
                'auto_footer'  => false
            ], $this->email_campaign_id, $this);

            // create the campaign
            $response = $this->mc_campaign_instance()->addCampaign('regular', $recipients, $settings);

            if ( ! empty($response->id)) {
                $campaign_id = $response->id;

                // save the MailChimp campaign ID against the campaign log.
                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'mailchimp_campaign_id', $campaign_id);

                $parameters = [
                    'plain_text' => $this->content_text,
                    'html'       => $this->content_html
                ];

                $result = $this->mc_campaign_instance()->setCampaignContent($campaign_id, $parameters);
                // if response from API is good (i.e checking if the email html content is not empty) send the email.
                if ( ! empty($result->html)) {

                    $ck_response = $this->mc_campaign_instance()->getSendChecklist($campaign_id);

                    if ( ! $ck_response->is_ready) {
                        $errors = [];
                        foreach ($ck_response->items as $item) {
                            if ($item->type == 'error') {
                                $errors[] = $item->details;
                            }
                        }

                        self::save_campaign_error_log(json_encode($errors), $this->campaign_log_id, $this->email_campaign_id);

                        return parent::ajax_failure();
                    }

                    $this->mc_campaign_instance()->send($campaign_id);

                    return self::ajax_success();
                }
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