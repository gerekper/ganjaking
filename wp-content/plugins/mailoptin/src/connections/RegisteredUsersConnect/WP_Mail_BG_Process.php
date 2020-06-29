<?php

namespace MailOptin\RegisteredUsersConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Logging\CampaignLogRepository;
use MailOptin\Core\PluginSettings\Settings;
use WP_Background_Process;


class WP_Mail_BG_Process extends WP_Background_Process
{
    /**
     * @var string
     */
    protected $action = 'mo_wp_mail_bg_process';

    /**
     * HTML email content type.
     *
     * @return string
     */
    public function html_content_type()
    {
        return 'text/html';
    }

    /**
     * Add content_type email filter.
     */
    public function add_html_content_type()
    {
        add_filter('wp_mail_content_type', array($this, 'html_content_type'));
    }

    /**
     * Remove content_type email filter.
     */
    public function remove_html_content_type()
    {
        remove_filter('wp_mail_content_type', array($this, 'html_content_type'));
    }

    /**
     * Filters the email address to send from.
     */
    public function wp_mail_from_filter()
    {
        add_filter('wp_mail_from', function ($val) {
            $from_email = Settings::instance()->from_email();

            return ! empty($from_email) ? $from_email : $val;
        });
    }

    /**
     * Filters the name to associate with the “from” email address.
     */
    public function wp_mail_from_name_filter()
    {
        add_filter('wp_mail_from_name', function ($val) {
            $from_name = Settings::instance()->from_name();

            return ! empty($from_name) ? $from_name : $val;
        });
    }

    /**
     * Add plain text message to email to send.
     *
     * @param string $plain_text_message
     */
    public function add_plain_text_message($plain_text_message)
    {
        add_action('phpmailer_init', function ($phpmailer) use ($plain_text_message) {
            $phpmailer->AltBody = $plain_text_message;
        });
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $user_data object of user's username and email address.
     *
     * @return mixed
     */
    protected function task($user_data)
    {
        $repository = CampaignLogRepository::instance();

        $email_address = $user_data->user_email;

        $unsubscribed_contacts = get_option('mo_wp_user_unsubscribers', []);
        if ( ! empty($unsubscribed_contacts)) {
            if (in_array(base64_encode($email_address), $unsubscribed_contacts)) return false;
        }

        $username          = $user_data->user_login;
        $email_campaign_id = $user_data->email_campaign_id;
        $campaign_log_id   = $user_data->campaign_log_id;
        $content_html      = $user_data->content_html;
        $content_text      = $user_data->content_text;

        $search = ['{{unsubscribe}}', '{{webversion}}'];

        $replace = [
            home_url('?mo_wp_user_unsubscribe=' . base64_encode($email_address)),
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
                "Username: $username; Email address: $email_address; Note: $status",
                $campaign_log_id,
                $email_campaign_id
            );
        }

        return false;
    }

}