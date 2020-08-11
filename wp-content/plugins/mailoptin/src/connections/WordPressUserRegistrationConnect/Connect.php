<?php

namespace MailOptin\WordPressUserRegistrationConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'WordPressUserRegistrationConnect';

    public function __construct()
    {
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));
        add_filter('mo_optin_form_integrations_default', array($this, 'set_default_role'));

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
     * Register Constant Contact Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('WordPress User Registration', 'mailoptin');

        return $connections;
    }

    public function set_default_role($defaults)
    {
        $defaults['connection_email_list'] = 'subscriber';

        return $defaults;
    }

    /**
     * Replace placeholder tags with actual SendFox tags.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        return [];
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
        $wp_roles = wp_roles()->roles;
        $wp_roles = array_reduce(array_keys($wp_roles), function ($carry, $item) use ($wp_roles) {

            if ('administrator' != $item) {
                $carry[$item] = $wp_roles[$item]['name'];
            }

            return $carry;
        });

        return $wp_roles;
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '')
    {
        return [
            'user_login'   => __('Username', 'mailoptin'),
            'user_pass'    => __('Password', 'mailoptin'),
            'user_url'     => __('Website', 'mailoptin'),
            'display_name' => __('Display Name', 'mailoptin'),
            'nickname'     => __('Nickname', 'mailoptin'),
            'description'  => __('Biographical Description', 'mailoptin')
        ];
    }

    /**
     *
     * {@inheritdoc}
     *
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
        return [];
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed|null $extras
     *
     * @return mixed
     */
    public function subscribe($name, $email, $role, $extras = null)
    {
        return (new Subscription($name, $email, $role, $extras))->subscribe();
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