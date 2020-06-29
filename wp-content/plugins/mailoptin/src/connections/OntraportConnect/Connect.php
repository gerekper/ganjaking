<?php

namespace MailOptin\OntraportConnect;

use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractOntraportConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'OntraportConnect';

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
     * Register Ontraport Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Ontraport', 'mailoptin');

        return $connections;
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['OntraportConnect_subscriber_tags'] = apply_filters('mailoptin_customizer_optin_campaign_OntraportConnect_subscriber_tags', []);

        return $settings;
    }

    /**
     * @return mixed
     */
    public function get_tags()
    {
        if ( ! self::is_connected()) return;

        try {

            $cache_key = 'ontraport_tags';

            $tag_array = get_transient($cache_key);

            if (empty($tag_array) || false === $tag_array) {

                $response = $this->ontraportInstance()->make_request('/Tags');

                if (isset($response['data']) && is_array($response['data'])) {

                    $gdpr_tag = apply_filters('mo_connections_ontraport_acceptance_tag', 'GDPR');

                    $tag_array = [];

                    foreach ($response['data'] as $tag) {
                        if ($tag['tag_name'] == $gdpr_tag) continue;
                        $id             = $tag['tag_id'];
                        $tag_array[$id] = $tag['tag_name'];
                    }

                    set_transient($cache_key, $tag_array, 10 * MINUTE_IN_SECONDS);
                }
            }

            return $tag_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'ontraport');

            return ['' => esc_html__('Select...', 'mailoptin')];
        }
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     *
     * @return mixed
     */
    public function integration_customizer_controls($controls)
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'chosen_select',
                'name'        => 'OntraportConnect_subscriber_tags',
                'choices'     => $this->get_tags(),
                'label'       => __('Subscriber Tags', 'mailoptin'),
                'description' => __('Select Ontraport tags that will be assigned to subscribers.', 'mailoptin')
            ];

        } else {

            $content = sprintf(
                __("%sMailOptin Premium%s allows you to apply tags to subscribers.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=ontraport_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'name'    => 'OntraportConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

    /**
     * Replace placeholder tags with actual Ontraport merge tags.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        return $this->replace_footer_placeholder_tags($content);
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
        return ['none' => __('All Contacts', 'mailoptin')];
    }

    public function get_optin_fields($list_id = '')
    {
        try {
            return $this->ontraportInstance()->get_fields();
        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'ontraport');
        }
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