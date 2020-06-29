<?php

namespace MailOptin\MailjetConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractMailjetConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'MailjetConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'), 10, 3);
        add_action('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'), 10, 4);

        add_filter('mo_optin_integrations_advance_controls', array($this, 'customizer_advance_controls'));
        add_filter('mo_optin_form_integrations_default', [$this, 'customizer_advance_controls_defaults']);

        add_filter('mo_connections_with_advance_settings_support', function ($val) {
            $val[] = self::$connectionName;

            return $val;
        });

        add_action('init', [$this, 'confirm_subscription_handler']);

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::EMAIL_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    /**
     * Register Mailjet Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Mailjet', 'mailoptin');

        return $connections;
    }

    public function confirm_subscription_handler()
    {
        if ( ! isset($_GET['mo_mailjet_confirm_email']) || empty($_GET['mo_mailjet_confirm_email'])) return;

        $email_harsh = sanitize_text_field($_GET['mo_mailjet_confirm_email']);

        $bucket = get_option('mo_mailjet_double_optin_bucket', []);

        $error_message = esc_html__('There was an error saving your contact. Please try again.', 'mailoptin');

        foreach ($bucket as $key => $lead_data) {

            if ($key != $email_harsh) continue;

            $mailjet_list_id   = $lead_data['mailjet_list_id'];
            $optin_campaign_id = $lead_data['optin_campaign_id'];

            unset($lead_data['mailjet_list_id']);
            unset($lead_data['optin_campaign_id']);

            try {

                $endpoint = "contactslist/{$mailjet_list_id}/managecontact";

                $response = $this->mailjet_instance()->make_request($endpoint, $lead_data);

                if (isset($response->Count) && ! empty($response->Count)) {

                    $data = get_option('mo_mailjet_double_optin_bucket', []);
                    unset($data[$key]);
                    update_option('mo_mailjet_double_optin_bucket', $data);

                    $message = apply_filters('mo_mailjet_confirmed_message', esc_html__('Your subscription has been confirmed.', 'mailoptin'));

                    wp_die($message, $message, ['response' => 200]);
                }

                self::save_optin_error_log(json_encode($response), 'mailjet', $optin_campaign_id);

                wp_die($error_message, $error_message, ['response' => 200]);

            } catch (\Exception $e) {

                self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'mailjet', $optin_campaign_id);

                wp_die($error_message, $error_message, ['response' => 200]);
            }
        }
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['MailjetConnect_enable_double_optin'] = false;

        return $settings;
    }

    /**
     * @param array $controls
     *
     * @return mixed
     */
    public function integration_customizer_controls($controls)
    {
        //EmmaConnect_upgrade_notice
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'toggle',
                'name'        => 'MailjetConnect_enable_double_optin',
                'label'       => __('Enable Double Optin', 'mailoptin'),
                'description' => __("Double optin requires users to confirm their subscription before they are added.", 'mailoptin')
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to enable double optin for Mailjet.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=mailjet_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'MailjetConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        $search = [
            '{{webversion}}',
            '{{unsubscribe}}'
        ];

        $replace = [
            '[[PERMALINK]]',
            // can also be [[UNSUB_LINK_EN]] or just [[UNSUB_LINK]]
            // see https://app.mailjet.com/support/how-can-i-add-an-unsubscribe-link-to-my-newsletter,103.htm
            '[[UNSUB_LINK_LOCALE]]'
        ];

        $content = str_replace($search, $replace, $content);

        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     * @param $controls
     *
     * @return array
     */
    public function customizer_advance_controls($controls)
    {
        // always prefix with the name of the connect/connection service.
        $controls[] = [
            'field'       => 'text',
            'name'        => 'MailjetConnect_first_name_field_key',
            'label'       => __('First Name Property', 'mailoptin'),
            'description' => sprintf(
                __('If subscribers first names are missing, change this to the correct contact property name. %sLearn more%s', 'mailoptin'),
                '<a href="https://mailoptin.io/?p=21482" target="_blank">', '</a>'
            )
        ];

        $controls[] = [
            'field'       => 'text',
            'name'        => 'MailjetConnect_last_name_field_key',
            'label'       => __('Last Name Property', 'mailoptin'),
            'description' => sprintf(
                __('If subscribers last names are missing, change this to the correct attribute name. %sLearn more%s', 'mailoptin'),
                '<a href="https://mailoptin.io/?p=21482" target="_blank">', '</a>'
            )
        ];

        return $controls;
    }

    public function customizer_advance_controls_defaults($defaults)
    {
        $defaults['MailjetConnect_first_name_field_key'] = 'firstname';
        $defaults['MailjetConnect_last_name_field_key']  = 'name';

        return $defaults;
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
            return $this->mailjet_instance()->get_lists();
        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailjet');
        }
    }

    /**
     * Fetch user defined custom fields
     */
    public function get_optin_fields($list_id = '')
    {
        try {
            $fields = $this->mailjet_instance()->get_custom_fields();

            $firstname_key = $this->get_first_name_property();
            $lastname_key  = $this->get_last_name_property();

            unset($fields[$firstname_key]);
            unset($fields[$lastname_key]);
            unset($fields[apply_filters('mo_connections_mailjet_acceptance_tag', 'gdpr')]);

            return $fields;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailjet');
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
        return (new SendCampaign($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text))->send();
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