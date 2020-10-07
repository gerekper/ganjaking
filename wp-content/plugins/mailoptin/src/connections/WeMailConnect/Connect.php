<?php

namespace MailOptin\WeMailConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractWeMailConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'WeMailConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));

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
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['WeMailConnect_lead_tags'] = apply_filters('mailoptin_customizer_optin_campaign_WeMailConnect_lead_tags', '');

        return $settings;
    }

    /**
     * @param array $controls
     *
     * @return mixed
     */
    public function integration_customizer_controls($controls)
    {
        //WeMailConnect_upgrade_notice
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'text',
                'name'        => 'WeMailConnect_lead_tags',
                'placeholder' => 'tag1, tag2',
                'label'       => __('Tags', 'mailoptin'),
                'description' => __('Enter comma-separated list of tags to assign to subscribers.', 'mailoptin')
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to add custom fields and apply tags to subscribers.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=wemail_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'WeMailConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }


    /**
     * Register WeMail Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('weMail', 'mailoptin');

        return $connections;
    }

    /**
     * Replace placeholder tags with actual WeMail tags.
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
        try {

            $response = $this->wemail_instance()->make_request('lists');
            $response = $response['body'];

            if (is_array($response['lists'])) {

                foreach ($response['lists'] as $list) {
                    $lists_array[$list['id']] = $list['name'];
                }
            }

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'wemail');
        }
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
            'mobile'        => __('Mobile', 'mailoptin'),
            'phone'         => __('Phone', 'mailoptin'),
            'address1'      => __('Address Line 1', 'mailoptin'),
            'address2'      => __('Address Line 2', 'mailoptin'),
            'city'          => __('City', 'mailoptin'),
            'state'         => __('State', 'mailoptin'),
            'country'       => __('Country', 'mailoptin'),
            'zip'           => __('Zip Code', 'mailoptin'),
            'date_of_birth' => __('Date of Birth', 'mailoptin')
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
    public function subscribe($email, $name, $list_id, $extras = null)
    {
        return (new Subscription($email, $name, $list_id, $extras))->subscribe();
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