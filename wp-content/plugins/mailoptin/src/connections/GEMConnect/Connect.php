<?php

namespace MailOptin\GEMConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractGEMConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'GEMConnect';

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
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('GoDaddy Email Marketing', 'mailoptin');

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

            $lists_array = array();

            $response = $this->gem_instance()->get_lists();

            if (self::is_http_code_success($response['status'])) {

                $lists = $response['body']->subscriberLists;

                if ( ! empty($lists)) {
                    foreach ($lists as $list) {
                        $lists_array[$list->id] = $list->name;
                    }
                }

                return $lists_array;
            }

            self::save_optin_error_log($response['body'], 'gem');

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'gem');

            return [];
        }
    }

    public function get_optin_fields($list_id = '')
    {
        return [
            'city'    => __('City', 'mailoptin'),
            'phone'   => __('Phone Number', 'mailoptin'),
            'company' => __('Company', 'mailoptin'),
            'title'   => __('Title', 'mailoptin'),
            'address' => __('Address', 'mailoptin'),
            'state'   => __('State', 'mailoptin'),
            'zip'     => __('Zip', 'mailoptin'),
            'country' => __('Country', 'mailoptin')
        ];
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