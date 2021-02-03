<?php

namespace MailOptin\ZohoCRMConnect;

class ConnectSettingsPage extends AbstractZohoCRMConnect
{
    public function __construct()
    {
        parent::__construct();

        add_filter('mailoptin_connections_settings_page', array($this, 'connection_settings'));

        add_filter('wp_cspa_santized_data', [$this, 'remove_access_token_persistence'], 10, 2);
        add_action('wp_cspa_settings_after_title', array($this, 'output_error_log_link'), 10, 2);

        add_action('mailoptin_before_connections_settings_page', [$this, 'handle_access_token_persistence']);
        add_action('mailoptin_before_connections_settings_page', [$this, 'handle_integration_disconnection']);
    }

    /**
     * Build the settings metabox
     *
     * @param array $arg
     *
     * @return array Because AWeber requires external authentication, you will need to register our application with AWeber before you can proceed.
     */
    public function connection_settings($arg)
    {
        $disconnect_integration = '';
        if (self::is_connected()) {
            $status                 = sprintf('<span style="color:#008000">(%s)</span>', __('Connected', 'mailoptin'));
            $button_text            = __('RE-AUTHORIZE', 'mailoptin');
            $button_color           = 'mobtnGreen';
            $description            = sprintf(__('Only re-authorize if you want to connect another Zoho account.', 'mailoptin'));
            $disconnect_integration = sprintf(
                '<div style="text-align:center;font-size:14px;"><a onclick="return confirm(\'%s\')" href="%s">%s</a></div>',
                __('Are you sure you want to disconnect?', 'mailoptin'),
                wp_nonce_url(
                    add_query_arg('mo-integration-disconnect', 'zohocrm', MAILOPTIN_CONNECTIONS_SETTINGS_PAGE),
                    'mo_disconnect_integration'
                ),
                __('Disconnect Integration', 'mailoptin')
            );
        } else {
            $status       = sprintf('<span style="color:#FF0000">(%s)</span>', __('Not Connected', 'mailoptin'));
            $button_text  = __('AUTHORIZE', 'mailoptin');
            $button_color = 'mobtnPurple';
            $description  = sprintf(__('Authorization is required to grant <strong>%s</strong> access to interact with your Zoho CRM account.', 'mailoptin'), 'MailOptin');
        }

        $settingsArg[] = array(
            'section_title_without_status' => __('Zoho CRM', 'mailoptin'),
            'section_title'                => __('Zoho CRM', 'mailoptin') . " $status",
            'type'                         => self::EMAIL_MARKETING_TYPE,
            'zohocrm_auth'                 => array(
                'type'        => 'arbitrary',
                'data'        => sprintf(
                    '<div class="moBtncontainer"><a href="%s" class="mobutton mobtnPush %s">%s</a></div>%s',
                    $this->get_oauth_url('zohocrm'),
                    $button_color,
                    $button_text,
                    $disconnect_integration
                ),
                'description' => '<p class="description" style="text-align:center">' . $description . '</p>',
            ),
            'disable_submit_button'        => true
        );

        return array_merge($arg, $settingsArg);
    }

    /**
     * Prevent access token from being overridden when settings page is saved.
     *
     * @param array $sanitized_data
     * @param string $option_name
     *
     * @return mixed
     */
    public function remove_access_token_persistence($sanitized_data, $option_name)
    {
        // remove the access token from being overridden on save of settings.
        if ($option_name == MAILOPTIN_CONNECTIONS_DB_OPTION_NAME) {
            unset($sanitized_data['zohocrm_access_token']);
            unset($sanitized_data['zohocrm_refresh_token']);
            unset($sanitized_data['zohocrm_api_domain']);
            unset($sanitized_data['zohocrm_accounts_server']);
            unset($sanitized_data['zohocrm_location']);
            unset($sanitized_data['zohocrm_expires_at']);
        }

        return $sanitized_data;
    }

    public function handle_integration_disconnection($option_name)
    {
        if ( ! isset($_GET['mo-integration-disconnect']) || $_GET['mo-integration-disconnect'] != 'zohocrm' || ! check_admin_referer('mo_disconnect_integration')) return;

        $old_data = get_option($option_name, []);
        unset($old_data['zohocrm_access_token']);
        unset($old_data['zohocrm_refresh_token']);
        unset($old_data['zohocrm_api_domain']);
        unset($old_data['zohocrm_accounts_server']);
        unset($old_data['zohocrm_location']);
        unset($old_data['zohocrm_expires_at']);

        update_option($option_name, $old_data);

        $connection = Connect::$connectionName;

        // delete connection cache
        delete_transient("_mo_connection_cache_$connection");

        wp_safe_redirect(MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);
        exit;
    }

    /**
     * Persist access token.
     *
     * @param string $option_name DB wp_option key for saving connection settings.
     */
    public function handle_access_token_persistence($option_name)
    {
        if ( ! empty($_GET['mo-save-oauth-provider']) && $_GET['mo-save-oauth-provider'] == 'zohocrm' && ! empty($_GET['access_token'])) {

            check_admin_referer('mo_save_oauth_credentials', 'moconnect_nonce');

            $expires_at = $this->oauth_expires_at_transform($_GET['expires_at']);

            $old_data = get_option($option_name, []);
            $new_data = array_map('rawurldecode', [
                'zohocrm_access_token'    => $_GET['access_token'],
                'zohocrm_refresh_token'   => $_GET['refresh_token'],
                'zohocrm_expires_at'      => $expires_at,
                'zohocrm_api_domain'      => $_GET['api_domain'],
                'zohocrm_location'        => $_GET['location'],
                'zohocrm_accounts_server' => $_GET['accounts_server']
            ]);

            $new_data = array_filter($new_data, [$this, 'data_filter']);

            update_option($option_name, array_merge($old_data, $new_data));

            $connection = Connect::$connectionName;

            // delete connection cache
            delete_transient("_mo_connection_cache_$connection");

            wp_safe_redirect(MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);
            exit;
        }
    }

    public function output_error_log_link($option, $args)
    {
        if (MAILOPTIN_CONNECTIONS_DB_OPTION_NAME !== $option || ! isset($args['zohocrm_auth'])) {
            return;
        }

        //Output error log link if  there is one
        echo self::get_optin_error_log_link('zohocrm');
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}