<?php

namespace MailOptin\KlaviyoConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\AbstractCampaignLogMeta;

class SendCampaign extends AbstractKlaviyoConnect
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
     * Send campaign via Campaign Monitor.
     *
     * @return array
     */
    public function send()
    {
        try {

            $list_id = $this->get_email_campaign_list_id($this->email_campaign_id);

            $campaign_title = $this->get_email_campaign_campaign_title($this->email_campaign_id);

            $response = $this->klaviyo_instance()->create_template(
                __('Template: ', 'mailoptin') . $this->campaign_subject,
                $this->content_html
            );

            if (self::is_http_code_not_success($response['status_code'])) {
                self::save_campaign_error_log($response['body']->message, $this->campaign_log_id, $this->email_campaign_id);
                return parent::ajax_failure($response['body']->message);
            }

            if (isset($response['body']->id)) {
                $template_id = $response['body']->id;

                $result = $this->klaviyo_instance()->create_campaign([
                    'list_id' => $list_id,
                    'name' => $campaign_title,
                    'template_id' => $template_id,
                    'from_email' => Settings::instance()->from_email(),
                    'from_name' => Settings::instance()->from_name(),
                    'subject' => $this->campaign_subject,
                ]);

                if (isset($result['body']->id)) {
                    $created_campaign_id = $result['body']->id;
                    $this->klaviyo_instance()->send_immediately($created_campaign_id);
                    AbstractCampaignLogMeta::add_campaignlog_meta($this->campaign_log_id, 'klaviyo_campaign_id', $created_campaign_id);
                    $this->klaviyo_instance()->delete_template($template_id);
                    return parent::ajax_success();
                }

                self::save_campaign_error_log($result['body']->message, $this->campaign_log_id, $this->email_campaign_id);

                return parent::ajax_failure($result['body']->message);
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