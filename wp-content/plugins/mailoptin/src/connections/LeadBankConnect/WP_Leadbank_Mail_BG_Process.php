<?php

namespace MailOptin\LeadBankConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Logging\CampaignLogRepository;
use MailOptin\RegisteredUsersConnect\WP_Mail_BG_Process;


class WP_Leadbank_Mail_BG_Process extends WP_Mail_BG_Process
{
    /**
     * @var string
     */
    protected $action = 'mo_leadbank_mail_bg_process';

    /**
     * {@inherit_doc}
     */
    protected function task($user_data)
    {
        $repository = CampaignLogRepository::instance();

        $email_address = $user_data['email_address'];

        $unsubscribed_contacts = get_option('mo_leadbank_unsubscribers', []);

        if ( ! empty($unsubscribed_contacts)) {
            if (in_array(base64_encode($email_address), $unsubscribed_contacts)) return false;
        }

        $email_campaign_id = $user_data['email_campaign_id'];
        $campaign_log_id   = $user_data['campaign_log_id'];

        $content_html = CampaignLogRepository::instance()->retrieveContentHtml($campaign_log_id);
        $content_text = CampaignLogRepository::instance()->retrieveContentText($campaign_log_id);

        $search = ['{{unsubscribe}}', '{{webversion}}'];

        $replace = [
            home_url('?mo_leadbank_unsubscribe=' . base64_encode($email_address)),
            home_url('?mo_view_web_version=' . $campaign_log_id)
        ];

        $content_text = str_replace($search, $replace, $content_text);
        $content_html = str_replace($search, $replace, $content_html);

        $subject = $repository->retrieveTitle($campaign_log_id);

        $this->wp_mail_from_filter();
        $this->wp_mail_from_name_filter();
        $this->add_plain_text_message($content_text);

        $this->add_html_content_type();
        $response = wp_mail($email_address, $subject, $content_html); // send the newsletter.
        $this->remove_html_content_type();

        if ( ! $response) {
            $status = __('Email failed to be delivered', 'mailoptin');

            AbstractConnect::save_campaign_error_log(
                "Email address: $email_address; Note: $status",
                $campaign_log_id,
                $email_campaign_id
            );
        }

        return false;
    }

}