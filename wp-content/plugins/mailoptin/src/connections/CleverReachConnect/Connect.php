<?php

namespace MailOptin\CleverReachConnect;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Select_Control;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractCleverReachConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'CleverReachConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));

        add_filter('mo_optin_integrations_advance_controls', array($this, 'customizer_advance_controls'));
        add_filter('mo_optin_form_integrations_default', [$this, 'customizer_advance_controls_defaults']);

        add_filter('mo_connections_with_advance_settings_support', function ($val) {
            $val[] = self::$connectionName;

            return $val;
        });

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
     * Register CleverReach Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('CleverReach', 'mailoptin');

        return $connections;
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['CleverReachConnect_lead_tags'] = apply_filters('mailoptin_customizer_optin_campaign_CleverReachConnect_lead_tags', '');

        return $settings;
    }

    public function customizer_advance_controls_defaults($defaults)
    {
        $defaults['CleverReachConnect_first_name_field_key'] = '';
        $defaults['CleverReachConnect_last_name_field_key']  = '';

        return $defaults;
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
            'field'   => 'select',
            'name'    => 'CleverReachConnect_first_name_field_key',
            'choices' => ['' => '––––––'] + $this->get_optin_fields(),
            'label'   => __('First Name Field', 'mailoptin')
        ];

        $controls[] = [
            'field'   => 'select',
            'name'    => 'CleverReachConnect_last_name_field_key',
            'choices' => ['' => '––––––'] + $this->get_optin_fields(),
            'label'   => __('Last Name Field', 'mailoptin')
        ];

        return $controls;
    }

    /**
     * @param $controls
     * @param $optin_campaign_id
     * @param $index
     * @param $saved_values
     *
     * @return array
     */
    public function integration_customizer_controls($controls)
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'text',
                'name'        => 'CleverReachConnect_lead_tags',
                'label'       => __('Tags', 'mailoptin'),
                'description' => __('Enter a comma-separated list of tags to assign to subscribers.', 'mailoptin'),
            ];

            $controls[] = [
                'name'    => 'CleverReachConnect_map_name_field_notice',
                'field'   => 'custom_content',
                'content' => '<div style="border-left: 4px solid #D54E21;padding-left: 4px;">'. esc_html__('Click the "Advanced" link below to map the first and last name fields.').'</div>'
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to map custom fields and assign tags to leads", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cleverreach_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'CleverReachConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }


    /**
     * @return mixed
     */
    public function get_tags()
    {
        if ( ! self::is_connected()) return [];

        try {

            return parent::cleverreachInstance()->getTags();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'cleverreach');

            return [];
        }
    }

    public function replace_placeholder_tags($content, $type = 'html')
    {
        $search = [
            '{{webversion}}',
            '{{unsubscribe}}'
        ];

        $replace = [
            '{ONLINE_VERSION}',
            '{UNSUBSCRIBE}',
        ];

        $content = str_replace($search, $replace, $content);

        // search and replace this if this operation is for text content.
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
        try {

            return $this->cleverreachInstance()->getGroupList();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'cleverreach');
        }
    }

    /**
     * {@inherit_doc}
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '')
    {
        $custom_fields = [];

        try {

            $fields = $this->cleverreachInstance()->get_custom_fields($list_id);

            // check for global attributes
            if (isset($fields['global_attributes']) && is_array($fields['global_attributes']) && ! empty($fields['global_attributes'])) {

                foreach ($fields['global_attributes'] as $key => $label) {
                    $custom_fields['global_attr_' . $key] = $label;
                }
            }

            //check for the list attributes
            if (isset($fields['attributes']) && is_array($fields['attributes']) && ! empty($fields['attributes'])) {

                foreach ($fields['attributes'] as $key => $label) {
                    $custom_fields['attr_' . $key] = $label;
                }
            }

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'cleverreach');
        }

        return $custom_fields;
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