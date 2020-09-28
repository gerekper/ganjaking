<?php

namespace MailOptin\JiltConnect;

use MailOptin\Core\Connections\ConnectionInterface;

if (strpos(__FILE__, 'mailoptin' . DIRECTORY_SEPARATOR . 'src') !== false) {
    // production url path to assets folder.
    define('MAILOPTIN_JILT_CONNECT_ASSETS_URL', MAILOPTIN_URL . 'src/connections/JiltConnect/assets/');
} else {
    // dev url path to assets folder.
    define('MAILOPTIN_JILT_CONNECT_ASSETS_URL', MAILOPTIN_URL . '../' . dirname(substr(__FILE__, strpos(__FILE__, 'mailoptin'))) . '/assets/');
}


class Connect extends AbstractJiltConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'JiltConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'), 10, 3);
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'), 10, 4);

        add_action('wp_ajax_mailoptin_customizer_fetch_shop_lists', [$this, 'customizer_fetch_shop_lists']);
        add_action('mo_optin_integration_control_enqueue', function () {
            wp_enqueue_script(
                'mailoptin-jilt-optin',
                MAILOPTIN_JILT_CONNECT_ASSETS_URL . 'jilt.js',
                array('jquery', 'customize-controls'),
                MAILOPTIN_VERSION_NUMBER
            );
        });

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
     * Register Jilt Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Jilt', 'mailoptin');

        return $connections;
    }

    /**
     * Fetch Lists in a shop.
     */
    public function customizer_fetch_shop_lists()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        \MailOptin\Core\current_user_has_privilege() || exit;

        $shop_id = sanitize_text_field($_REQUEST['shop_id']);

        $default = ['' => __('Select...', 'mailoptin')];

        $shop_lists = array_replace($default, $this->get_shop_lists($shop_id));

        if (is_array($shop_lists) && ! empty($shop_lists)) {
            foreach ($shop_lists as $key => $value) {
                echo '<option value="' . esc_attr($key) . '">' . $value . '</option>';
            }

            wp_send_json_success(ob_get_clean());
        }

        wp_send_json_error();
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['JiltConnect_shop_lists'] = '';
        $settings['JiltConnect_lead_tags']  = '';

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
    public function integration_customizer_controls($controls, $optin_campaign_id, $index, $saved_values)
    {
        $shop_lists = ['' => __('Select...', 'mailoptin')];

        if (isset($index)) {
            $shop_id    = isset($saved_values[$index]['connection_email_list']) ? $saved_values[$index]['connection_email_list'] : '';
            $shop_lists = array_replace($shop_lists, $this->get_shop_lists($shop_id));
        }

        $controls[] = [
            'field'       => 'select',
            'name'        => 'JiltConnect_shop_lists',
            'choices'     => $shop_lists,
            'label'       => __('Select List (Optional)', 'mailoptin'),
            'description' => __('Select a list to add subscribers to.', 'mailoptin'),
        ];

        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            $controls[] = [
                'field'       => 'textarea',
                'name'        => 'JiltConnect_lead_tags',
                'label'       => __('Tags (Optional)', 'mailoptin'),
                'placeholder' => 'tag1, tag2',
                'description' => __('Comma-separated list of tags to assign to subscribers.', 'mailoptin'),
            ];
        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to apply tags to leads and get access to loads of conversion features.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=jilt_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'JiltConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

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

            return $this->jiltInstance()->getStoreList();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'jilt');

            return [];
        }
    }

    /**
     * {@inherit_doc}
     *
     * @return mixed
     */
    public function get_shop_lists($shop_id)
    {
        if ( ! $shop_id) return [];

        try {

            return $this->jiltInstance()->getShopLists($shop_id);

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'jilt');

            return [];
        }
    }

    /**
     * {@inherit_doc}
     *
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '')
    {
        return [
            'phone' => esc_html__('Phone Number', 'mailoptin'),
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