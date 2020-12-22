<?php

namespace MailOptin\CtctConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractCtctConnect
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

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $company_country = Settings::instance()->company_country();
            $company_state = Settings::instance()->company_state();
            $company_name = Settings::instance()->company_name();
            $company_address = Settings::instance()->company_address();
            $company_address_2 = Settings::instance()->company_address_2();
            $company_city = Settings::instance()->company_city();
            $postal_code = Settings::instance()->company_zip();

            $create_campaign = $this->ctctInstance()->createEmailCampaign(
                apply_filters('mailoptin_constantcontact_campaign_settings', [
                    // susceptible to duplicate campaign name error
                    'name' => $campaign_title . ' ' . gmdate("Y-m-d H:i:s", current_time('timestamp')),
                    'subject' => $this->campaign_subject,
                    'from_name' => Settings::instance()->from_name(),
                    'from_email' => Settings::instance()->from_email(),
                    'reply_to_email' => Settings::instance()->reply_to(),
                    'is_view_as_webpage_enabled' => true,
                    'view_as_web_page_text' => 'View this message as a web page',
                    'view_as_web_page_link_text' => 'Click here',
                    'email_content' => $this->content_html,
                    'text_content' => $this->content_text,
                    'email_content_format' => 'HTML',
                    'message_footer' => array_filter(array(
                        'organization_name' => $company_name,
                        'address_line_1' => $company_address,
                        'address_line_2' => $company_address_2,
                        'city' => $company_city,
                        'state' => $company_country == 'US' ? $company_state : null,
                        'postal_code' => $company_country == 'US' ? $postal_code : null,
                        'international_state' => $company_country != 'US' ? $company_state : null,
                        'country' => $company_country,
                    ),
                        array($this, 'isNotNull')
                    ),
                    'sent_to_contact_lists' => [
                        [
                            "id" => "$list_id"
                        ]
                    ]
                ],
                    $this->email_campaign_id
                )
            );

            if (isset($create_campaign->id)) {
                $this->ctctInstance()->sendEmailCampaign($create_campaign->id);

                AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'ctct_campaign_id', $create_campaign->id);

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