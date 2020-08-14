<?php

namespace MailOptin\EmmaConnect;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractEmmaConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'EmmaConnect';

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


    public function get_signup_forms()
    {
        if ( ! self::is_connected()) return;

        try {

            $cache_key = 'emma_forms';

            $sequence_array = get_transient($cache_key);

            if (empty($result) || false === $result) {

                $response = parent::emma_instance()->make_request('signup_forms');

                if (self::is_http_code_not_success($response['status_code'])) {
                    return self::save_optin_error_log(json_encode($response->error), 'emma');
                }

                $body = $response['body'];

                $forms_array = array('' => esc_html__('Select...', 'mailoptin'));

                foreach ($body as $form) {
                    $forms_array[$form->id] = $form->name;
                }

                set_transient($cache_key, $forms_array, MINUTE_IN_SECONDS);
            }

            return $sequence_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'emma');
        }
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['EmmaConnect_signup_form']                = '';
        $settings['EmmaConnect_disable_confirmation_email'] = false;

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
                'field'       => 'select',
                'name'        => 'EmmaConnect_signup_form',
                'choices'     => $this->get_signup_forms(),
                'label'       => __('Assigned Form', 'mailoptin'),
                'description' => __('Select Emma form that indicate subscribers signed up through it. This is important if you have a workflow triggered by signup.', 'mailoptin')
            ];

            $controls[] = [
                'field'       => 'toggle',
                'name'        => 'EmmaConnect_disable_confirmation_email',
                'label'       => __('Disable Confirmation Email', 'mailoptin'),
                'description' => __("Activate to prevent the confirmation email sent after users subscribe from going out.", 'mailoptin')
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to disable email confirmation and assign Emma form to subscribers.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=emma_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'EmmaConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

    /**
     * Register Emma Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Emma', 'mailoptin');

        return $connections;
    }

    /**
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
        try {
            $response = $this->emma_instance()->make_request('groups');

            if (self::is_http_code_not_success($response['status_code'])) {
                return self::save_optin_error_log(json_encode($response['body']->error), 'emma');
            }

            $body = $response['body'];

            $lists_array = array();

            foreach ($body as $list) {
                $lists_array[$list->member_group_id] = $list->group_name;
            }

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'emma');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {
            $response = $this->emma_instance()->make_request('fields');

            if (self::is_http_code_not_success($response['status_code'])) {
                return self::save_optin_error_log(json_encode($response['body']->error), 'emma');
            }

            $body = $response['body'];

            $fields_array = array();

            foreach ($body as $field) {
                if (in_array($field->shortcut_name, ['first_name', 'last_name'])) continue;

                $fields_array[$field->shortcut_name] = $field->display_name;
            }

            return $fields_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'emma');

            return [];
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