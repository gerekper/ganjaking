<?php

namespace MailOptin\EmailOctopusConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractEmailOctopusConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'EmailOctopusConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    /**
     * Register EmailOctopus Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('EmailOctopus', 'mailoptin');

        return $connections;
    }

    /**
     * Replace placeholder tags with actual EmailOctopus tags.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {

    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_email_list()
    {
        try {

            $response = $this->emailoctopus_instance()->get_lists();

            if (isset($response['body'], $response['body']->error)) {
                return self::save_optin_error_log($response['body']->error->code . ': ' . $response['body']->error->message, 'emailoctopus');
            }

            // an array with list id as key and name as value.
            $lists_array = array();

            if (self::is_http_code_success($response['status_code'])) {

                $lists = $response['body']->data;

                if ( ! empty($lists)) {
                    foreach ($lists as $list) {
                        $lists_array[$list->id] = $list->name;
                    }
                }
            }

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'emailoctopus');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $response = $this->emailoctopus_instance()->make_request(sprintf('lists/%s', $list_id));

            $custom_fields_array = [];

            if (isset($response['body']->fields) && ! empty($response['body']->fields)) {
                foreach ($response['body']->fields as $customField) {
                    if (in_array($customField->tag, ['EmailAddress', 'FirstName', 'LastName'])) continue;
                    if ($customField->label == apply_filters('mo_connections_emailoctopus_acceptance_tag', 'GDPR')) continue;
                    $custom_fields_array[$customField->tag] = $customField->label;
                }

                return $custom_fields_array;
            }

            return self::save_optin_error_log($response['body']->error->code . ': ' . $response['body']->error->message, 'emailoctopus');

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'emailoctopus');
        }
    }

    /**
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return array
     * @throws \Exception
     *
     */
    public function send_newsletter($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text)
    {

    }

    /**
     * @param string $email
     * @param string $name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed|null $extras
     *
     * @return mixed
     */
    public function subscribe($email, $name, $list_id, $extras = null)
    {
        return (new Subscription($email, $name, $list_id, $extras, $this))->subscribe();
    }

    /**
     * Singleton poop.
     *
     * @return Connect|null
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