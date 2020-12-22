<?php

namespace MailOptin\Ctctv3Connect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class SendCampaign extends AbstractCtctv3Connect
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

            $company_country   = Settings::instance()->company_country();
            $company_state     = Settings::instance()->company_state();
            $company_name      = Settings::instance()->company_name();
            $company_address   = Settings::instance()->company_address();
            $company_address_2 = Settings::instance()->company_address_2();
            $company_city      = Settings::instance()->company_city();
            $postal_code       = Settings::instance()->company_zip();

            $from_name      = Settings::instance()->from_name();
            $from_email     = Settings::instance()->from_email();
            $reply_to_email = Settings::instance()->reply_to();
            $subject        = $this->campaign_subject;

            $payload = apply_filters('mailoptin_constantcontactv3_campaign_settings', [
                // susceptible to duplicate campaign name error
                'name'                      => $campaign_title . ' ' . gmdate('Y-m-d H:i:s', current_time('timestamp')),
                'email_campaign_activities' => [
                    [
                        'format_type'                => 5,
                        'from_name'                  => $from_name,
                        'from_email'                 => $from_email,
                        'reply_to_email'             => $reply_to_email,
                        'subject'                    => $subject,
                        'html_content'               => str_replace('</body>', '[[trackingImage]]</body>', $this->content_html),
                        'physical_address_in_footer' => array_filter([
                            'address_line1'     => $company_address,
                            'address_line2'     => $company_address_2,
                            'city'              => $company_city,
                            'country_code'      => $company_country,
                            'organization_name' => $company_name,
                            'postal_code'       => $postal_code,
                            'state_code'        => $company_country == 'US' ? $company_state : null,
                            'state_non_us_name' => $company_country != 'US' ? $company_state : null,
                        ], [$this, 'isNotNull']),
                        'text_content'               => $this->content_text
                    ]
                ],
                $this->email_campaign_id
            ]);

            $create_campaign = $this->ctctv3Instance()->createEmailCampaign($payload);

            if (isset($create_campaign->campaign_id)) {
                //update the campaign with the lists id
                foreach ($create_campaign->campaign_activities as $campaign_activity) {

                    if ($campaign_activity->role == 'primary_email') {

                        $update_campaign = $this->ctctv3Instance()->updateEmailCampaign(
                            [
                                'contact_list_ids' => [$list_id],
                                'from_name'        => $from_name,
                                'from_email'       => $from_email,
                                'reply_to_email'   => $reply_to_email,
                                'subject'          => $subject,
                            ],
                            $campaign_activity->campaign_activity_id
                        );

                        $this->ctctv3Instance()->sendEmailCampaign($update_campaign->campaign_activity_id);

                        AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'ctctv3_campaign_id', $create_campaign->campaign_id);
                    }
                }

                // if we get here, campaign was sent because no exception was thrown by sendEmailCampaign().
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