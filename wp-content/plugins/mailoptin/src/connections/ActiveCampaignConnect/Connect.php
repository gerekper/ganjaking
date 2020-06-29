<?php

namespace MailOptin\ActiveCampaignConnect;

use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class Connect extends AbstractActiveCampaignConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'ActiveCampaignConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));

        add_action('mailoptin_email_template_before_forge', function ($email_campaign_id) {
            $connect_service = EmailCampaignRepository::get_customizer_value($email_campaign_id, 'connection_service');

            if ($connect_service == 'ActiveCampaignConnect') {
                add_filter('mailoptin_email_template_footer_description', [$this, 'replace_template_footer_description']);
            } else {
                remove_filter('mailoptin_email_template_footer_description', [$this, 'replace_template_footer_description']);
            }
        });

        parent::__construct();
    }

    /**
     * Necessary to prevent default ActiveCampaign footer
     *
     * @see https://help.activecampaign.com/hc/en-us/articles/220436467-How-do-I-change-the-default-footer-
     * @return string
     */
    public function replace_template_footer_description()
    {
        return '%SENDER-INFO%';
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
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['ActiveCampaignConnect_lead_tags'] = apply_filters('mailoptin_customizer_optin_campaign_ActiveCampaignConnect_lead_tags', '');

        $settings['ActiveCampaignConnect_form'] = apply_filters('mailoptin_customizer_optin_campaign_ActiveCampaignConnect_form', '');

        return $settings;
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
                'name'        => 'ActiveCampaignConnect_lead_tags',
                'label'       => __('Lead Tags', 'mailoptin'),
                'placeholder' => 'tag1, tag2',
                'description' => __('Enter comma-separated list of tags to assign to subscribers who opt-in via this campaign.', 'mailoptin'),
            ];

            $controls[] = [
                'field'       => 'select',
                'name'        => 'ActiveCampaignConnect_form',
                'choices'     => $this->get_forms(),
                'label'       => __('Assign Subscription Form', 'mailoptin'),
                'description' => sprintf(
                    __('Choose the %sActiveCampaign Form%s to assign to all leads. Useful if you want to enable automation and double opt-in.', 'mailoptin'),
                    '<strong>',
                    '</strong>'
                )
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to select %sActiveCampaign form and tags%s that will apply subscribers.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=activecampaign_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'ActiveCampaignConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

    /**
     * Register ActiveCampaign Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('ActiveCampaign', 'mailoptin');

        return $connections;
    }

    /**
     * Replace placeholder tags with actual ActiveCampaign tags.
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
            '%WEBCOPY%',
            '%UNSUBSCRIBELINK%'
        ];

        $content = str_replace($search, $replace, $content);

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
            $response = $this->activecampaign_instance()->api('list/list?ids=all');

            if (is_object($response)) {
                if ($response->http_code === 200 && 1 === $response->result_code) {

                    // an array with list id as key and name as value.
                    $lists_array = array();

                    foreach ((array)$response as $list) {
                        if (is_object($list) && isset($list->id)) {
                            $lists_array[$list->id] = $list->name;
                        }
                    }

                    return $lists_array;
                }

                self::save_optin_error_log(json_encode($response), 'activecampaign');

                return $response->result_message;
            }

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'activecampaign');

            return [];
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $custom_fields_array = [
                'phone'   => esc_html__('Phone Number', 'mailoptin'),
                'orgname' => esc_html__('Organization Name', 'mailoptin')
            ];

            $response = $this->activecampaign_instance()->api('list/field/view?ids=all');

            if (self::is_http_code_success($response->http_code) && 1 === $response->result_code) {

                $response = (array)$response;

                foreach ($response as $custom_field) {
                    if (is_object($custom_field) && isset($custom_field->id)) {
                        $custom_fields_array[$custom_field->id] = $custom_field->title;
                    }
                }

                return $custom_fields_array;
            }

            self::save_optin_error_log($response->result_message, 'activecampaign');

            return $custom_fields_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'activecampaign');
        }
    }

    /**
     * Fetch activecampaign forms belonging to saved account.
     *
     * @return array|mixed
     */
    public function get_forms()
    {
        try {
            $forms_array = get_transient('mo_activecampaign_forms');

            if (empty($forms_array) || false === $forms_array) {

                $forms = parent::activecampaign_instance()->api('form/getforms');

                if (is_object($forms) && $forms->http_code === 200 && 1 === $forms->result_code) {

                    // an array with list id as key and name as value.
                    $forms_array = ['' => __('Select...', 'mailoptin')];

                    foreach ((array)$forms as $form) {
                        if (is_object($form) && isset($form->id)) {
                            $forms_array[$form->id] = $form->name;
                        }
                    }

                    set_transient('mo_activecampaign_forms', $forms_array, HOUR_IN_SECONDS);
                }
            }

            return $forms_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'activecampaign');
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