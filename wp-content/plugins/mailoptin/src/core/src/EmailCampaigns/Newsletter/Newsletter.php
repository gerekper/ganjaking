<?php

namespace MailOptin\Core\EmailCampaigns\Newsletter;

use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\EmailCampaigns\AbstractTriggers;
use MailOptin\Core\EmailCampaigns\Misc;
use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

class Newsletter extends AbstractTriggers
{
    public function __construct()
    {
        parent::__construct();

        add_action('admin_init', [$this, 'newsletter_sending_handler']);
    }

    public function newsletter_sending_handler()
    {
        if ( ! is_admin() || ! \MailOptin\Core\current_user_has_privilege()) return;

        if ( ! isset($_GET['action']) || $_GET['action'] != 'mailoptin_send_newsletter') return;

        check_admin_referer('mailoptin-send-newsletter');

        $email_campaign_id = absint($_GET['id']);

        $email_subject = Misc::parse_email_subject(ER::get_merged_customizer_value($email_campaign_id, 'email_campaign_title'));

        $content_html = (new Templatify($email_campaign_id))->forge();

        $campaign_id = $this->save_campaign_log(
            $email_campaign_id,
            $email_subject,
            $content_html
        );

        $this->send_campaign($email_campaign_id, $campaign_id);
    }

    /**
     * Does the actual campaign sending.
     *
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     */
    public function send_campaign($email_campaign_id, $campaign_log_id)
    {
        $campaign           = $this->CampaignLogRepository->getById($campaign_log_id);
        $connection_service = $this->connection_service($email_campaign_id);

        if (empty($connection_service)) {
            wp_die(__('Error: no recipient has been defined for the newsletter. Please go back and do that now.', 'mailoptin'));
        }

        $connection_instance = ConnectionFactory::make($connection_service);

        $response = $connection_instance->send_newsletter(
            $email_campaign_id,
            $campaign_log_id,
            $campaign->title,
            $connection_instance->replace_placeholder_tags($campaign->content_html, 'html'),
            $connection_instance->replace_placeholder_tags($campaign->content_text, 'text')
        );

        if (isset($response['success']) && (true === $response['success'])) {
            $this->update_campaign_status($campaign_log_id, 'processed');
            EmailCampaignMeta::update_meta_data($email_campaign_id, 'newsletter_date_sent', current_time('mysql'));
        } else {
            $this->update_campaign_status($campaign_log_id, 'failed');
            EmailCampaignMeta::update_meta_data($email_campaign_id, 'newsletter_date_sent', ER::NEWSLETTER_STATUS_FAILED);
        }

        wp_safe_redirect(MAILOPTIN_EMAIL_NEWSLETTERS_SETTINGS_PAGE);
        exit;
    }

    /**
     * Singleton.
     *
     * @return self
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}