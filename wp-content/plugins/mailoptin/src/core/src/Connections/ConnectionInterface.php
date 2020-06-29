<?php

namespace MailOptin\Core\Connections;


interface ConnectionInterface
{
    const connectionName = '';

    /**
     * Connections must return array of features (e.g optin_campaign, email_campaign) they support.
     *
     * @return array
     */
    public static function features_support();

    /**
     * Replace placeholder tags in content_html and content_text with connect/email service replacement.
     *
     * @param string $content
     * @param string $type content type. text or html.
     *
     * @return string
     */
    public function replace_placeholder_tags($content, $type = 'html');

    /**
     * Return array of a connection email list for use by optin and email newsletter services.
     *
     * @return array
     */
    public function get_email_list();

    /**
     * Return a list of merge or custom fields for mapping.
     *
     * @param mixed $list_id
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '');

    /**
     * Does the actual sending of newsletter
     *
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return array should be in form eg ['success' => true, 'message' => 'Error message here']
     */
    public function send_newsletter($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text);

    /**
     * Subscribe user to optin / email list.
     *
     * @param string $email subscriber email address
     * @param string $name subscriber full name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed $extras extra data about optin subscription
     *
     * @return array [success: true|false, message:'']
     */
    public function subscribe($email, $name, $list_id, $extras = null);

    /**
     * Return instance of connection class.
     *
     * @return mixed
     */
    public static function get_instance();
}