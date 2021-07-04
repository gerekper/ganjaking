<?php

class NinjaTableUpdateChecker
{
    /**
     * The configuration array.
     *
     * @var array
     */
    private $vars;

    function __construct($vars)
    {
        $this->vars = $vars;

        add_action('admin_init', array($this, 'register_option'));

        add_action('wp_ajax_' . $this->get_var('option_group') . '_activate_license', array($this, 'activate_license'));

        add_action('wp_ajax_' . $this->get_var('option_group') . '_deactivate_license', array($this, 'deactivate_license'));
        add_action('wp_ajax_' . $this->get_var('option_group') . '_get_license_info', array($this, 'getLicenseInfo'));

        add_action('admin_init', array($this, 'check_license'));
        add_action('admin_init', array($this, 'init'));

        add_action('admin_init', array($this, 'sl_updater'), 0);
    }

    public function isLocal()
    {
        $ip_address = '';
        if (array_key_exists('SERVER_ADDR', $_SERVER)) {
            $ip_address = $_SERVER['SERVER_ADDR'];
        } else if (array_key_exists('LOCAL_ADDR', $_SERVER)) {
            $ip_address = $_SERVER['LOCAL_ADDR'];
        }
        return in_array($ip_address, array("127.0.0.1", "::1"));
    }

    /**
     * Get the configuration from the array using key.
     *
     * @param  string $var
     * @return bool|mixed
     */
    function get_var($var)
    {
        if (isset($this->vars[$var])) {
            return $this->vars[$var];
        }
        return false;
    }

    public function registerLicenseMenu($menus)
    {
        $menus[$this->get_var('menu_slug')] = $this->get_var('menu_title');

        return $menus;
    }

    /**
     * Show an error message that license needs to be activated
     */
    function init()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        $licenseStatus = get_option($this->get_var('license_status'));

        if(!$licenseStatus != 'valid' && is_multisite()) {
            $licenseStatus = get_network_option(get_main_network_id(), $this->get_var('license_status'));
        }

        if ('valid' != $licenseStatus) {
            $key = get_option($this->get_var('license_key'));
            if ($key) {
                $licenseData = get_transient($this->get_var('license_status') . '_checking');
                if ($licenseData && $licenseData->license == 'expired') {
                    $expireMessage = $this->getExpireMessage($licenseData);
                    
                    add_filter('ninja_dashboard_notices', function ($notices) use ($expireMessage) {
                        $notices['license_expire'] = array(
                            'type' => 'error',
                            'message' => $expireMessage,
                            'closable' => false
                        );
                        return $notices;
                    });

                    if ( $this->willShowExpirationNotice() ) {
                        add_action('admin_notices', function () use ($expireMessage) {
                            if (!defined('NINJA_TABLES_DIR_URL')) {
                                return;
                            }
                            echo '<div class="error">'.$expireMessage.'</div>';
                        });
                    }
                } else if ($licenseData && $licenseData->license == 'valid') {
                    update_option($this->get_var('license_status'), 'valid');
                } else {
                    add_action('admin_notices', function () {
                        if (!defined('NINJA_TABLES_DIR_URL')) {
                            return;
                        }
                        echo '<div class="error error_notice' . $this->get_var('option_group') . '"><p>' .
                            sprintf(__('The %s license needs to be activated. %sActivate Now%s', 'ninja-tables-pro'),
                                $this->get_var('plugin_title'), '<a href="' . $this->get_var('activate_url') . '">',
                                '</a>') .
                            '</p></div>';
                    });
                }
            } else {
                add_action('admin_notices', function () {
                    if (!defined('NINJA_TABLES_DIR_URL')) {
                        return;
                    }
                    echo '<div class="error error_notice' . $this->get_var('option_group') . '"><p>' .
                        sprintf(__('The %s license needs to be activated. %sActivate Now%s', 'ninja-tables-pro'),
                            $this->get_var('plugin_title'), '<a href="' . $this->get_var('activate_url') . '">',
                            '</a>') .
                        '</p></div>';
                });
            }
        }
    }

    function willShowExpirationNotice()
    {
        if(!function_exists('ninja_table_admin_role') || !ninja_table_admin_role()) {
            return false;
        }
        global $pagenow;
        $showablePages = ['index.php', 'plugins.php'];
        if(in_array($pagenow, $showablePages)) {
            return true;
        }
        return false;
    }

    function getExpiremEssage($licenseData)
    {
        $renewUrl = admin_url('admin.php?page=ninja_tables#/tools/licensing');

        return '<p>Your Ninja Tables Pro license has been <b>expired at ' . date('d M Y', strtotime($licenseData->expires)) . '</b>, Please ' .
            '<a href="' . $renewUrl . '"><b>Click Here to Renew Your License</b></a>'.'</p>';
    }

    function sl_updater()
    {
        // retrieve our license key from the DB
        if(is_multisite()) {
            $license_key = trim(get_network_option(get_main_network_id(), $this->get_var('license_key')));
            $license_status = get_option(get_main_network_id(), $this->get_var('license_status'));
        } else {
            $license_key = trim(get_option($this->get_var('license_key')));
            $license_status = get_option($this->get_var('license_status'));
        }

        // setup the updater
        new NinjaTableUpdater(
            $this->get_var('store_url'),
            $this->get_var('plugin_file'),
            array(
                'version'   => $this->get_var('version'),
                'license'   => $license_key,
                'item_name' => $this->get_var('item_name'),
                'item_id'   => $this->get_var('item_id'),
                'author'    => $this->get_var('author')
            ),
            array(
                'license_status' => $license_status,
                'admin_page_url' => $this->get_var('activate_url'),
                'purchase_url'   => $this->get_var('purchase_url'),
                'plugin_title'   => $this->get_var('plugin_title')
            )
        );
    }

    function register_option()
    {
        // creates our settings in the options table
        register_setting($this->get_var('option_group'), $this->get_var('license_key'),
            array($this, 'sanitize_license'));
    }

    function sanitize_license($new)
    {
        return $new;
    }

    private function isNetworkMainSite()
    {
        return is_multisite() && is_main_network(get_current_network_id());
    }

    function activate_license()
    {
        $license = trim($_POST[$this->get_var('license_key')]);

        if (!$license) {
            wp_send_json_error(array(
                'message'       => 'Please Provide a license key',
                'human_message' => 'Please Provide a license key',
                'is_error'      => true
            ), 423);
        }

        $isNetworkMainSite = is_multisite();

        if ($isNetworkMainSite) {
            // data to send in our API request
            $api_params = array(
                'edd_action' => 'activate_license',
                'license'    => $license,
                'item_name'  => urlencode($this->get_var('item_name')), // the name of our product in EDD
                'item_id'    => $this->get_var('item_id'),
                'url'        => network_site_url()
            );
        } else {
            // data to send in our API request
            $api_params = array(
                'edd_action' => 'activate_license',
                'license'    => $license,
                'item_name'  => urlencode($this->get_var('item_name')), // the name of our product in EDD
                'item_id'    => $this->get_var('item_id'),
                'url'        => home_url()
            );
        }


        // Call the custom API.
        $response = wp_remote_get(
            $this->get_var('store_url'),
            array('timeout' => 15, 'sslverify' => false, 'body' => $api_params)
        );

        // make sure the response came back okay
        if (is_wp_error($response)) {
            $license_data = file_get_contents($this->get_var('store_url') . '?' . http_build_query($api_params));
            if (!$license_data) {
                $license_data = $this->urlGetContentFallBack($this->get_var('store_url') . '?' . http_build_query($api_params));
            }
            if (!$license_data) {
                wp_send_json_error(array(
                    'message'       => 'Error when contacting with license server. Please check that your server have curl installed',
                    'human_message' => 'Error when contacting with license server. Please check that your server have curl installed <br />',
                    'response'      => $response,
                    'is_error'      => true
                ), 423);
                die();
            }
            $license_data = json_decode($license_data);
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));
        }

        // $license_data->license will be either "valid" or "invalid"
        if ($license_data->license) {
            if ($isNetworkMainSite) {
                update_network_option( get_main_network_id(), $this->get_var('license_status'), $license_data->license);
            } else {
                update_option($this->get_var('license_status'), $license_data->license);
            }
        }

        if ('valid' == $license_data->license) {
            if ($isNetworkMainSite) {
                update_network_option(  get_main_network_id(), $this->get_var('license_key'), $license);
            } else {
                update_option($this->get_var('license_key'), $license);
            }
            // save the license key to the database
            wp_send_json_success(array(
                'message'  => 'Congratulation! ' . $this->get_var('plugin_title') . ' is successfully activated',
                'response' => $license_data
            ), 200);
            die();
        }

        $errorMessage = $this->getErrorMessage($license_data, $license);
        wp_send_json_error(array(
            'message'       => $errorMessage,
            'human_message' => $errorMessage,
            'response'      => $license_data
        ), 423);

    }

    function urlGetContentFallBack($url)
    {
        $parts = parse_url($url);
        $host = $parts['host'];
        $result = false;
        if (!function_exists('curl_init')) {
            $ch = curl_init();
            $header = array('GET /1575051 HTTP/1.1',
                "Host: {$host}",
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language:en-US,en;q=0.8',
                'Cache-Control:max-age=0',
                'Connection:keep-alive',
                'Host:adfoc.us',
                'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
            );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        if (!$result && function_exists('fopen') && function_exists('stream_get_contents')) {
            $handle = fopen($url, "r");
            $result = stream_get_contents($handle);
        }
        return $result;
    }

    function deactivate_license()
    {
        if (function_exists('ninja_table_admin_role') && !current_user_can(ninja_table_admin_role())) {
            wp_send_json_error(array(
                'message'       => 'Sorry, You do not have permission to deactivate the license',
                'human_message' => 'Sorry, You do not have permission to deactivate the license',
                'is_error'      => true
            ), 423);
        }

        if(is_multisite() && !is_super_admin()) {
            wp_send_json_error(array(
                'message'       => 'Sorry, You do not have permission to deactivate the license, Only network admin can deactivate the licese',
                'human_message' => 'Sorry, You do not have permission to deactivate the license, Only network admin can deactivate the licese',
                'is_error'      => true
            ), 423);
        }

        // retrieve the license from the database


        if(is_multisite()) {
            $license = trim(get_network_option(get_main_network_id(), $this->get_var('license_key')));
            $url = network_site_url();
        } else {
            $license = trim(get_option($this->get_var('license_key')));
            $url = home_url();
        }

        if(is_multisite()) {
            delete_network_option(get_main_network_id(), $this->get_var('license_status'));
            delete_network_option(get_main_network_id(), $this->get_var('license_key'));
        }

        delete_option($this->get_var('license_status'));
        delete_option($this->get_var('license_key'));

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $license,
            'item_name'  => urlencode($this->get_var('item_name')), // the name of our product in EDD
            'item_id'    => $this->get_var('item_id'),
            'url'        => $url
        );

        // Call the custom API.
        $response = wp_remote_post(
            $this->get_var('store_url'),
            array('timeout' => 15, 'sslverify' => false, 'body' => $api_params)
        );

        // make sure the response came back okay
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => 'There was an error deactivating the license, please try again or contact support.'
            ), 423);

            die();
        }

        // decode the license data
        $license_data = json_decode(wp_remote_retrieve_body($response));

        // $license_data->license will be either "deactivated" or "failed"
        if ('deactivated' != $license_data->license) {
            wp_send_json_error(array(
                'message'      => 'There was an error deactivating the license, please try again or contact support.',
                'license_data' => $license_data
            ), 423);
            die();
        }

        if(is_multisite()) {
            delete_network_option(get_main_network_id(), $this->get_var('license_status'));
            delete_network_option(get_main_network_id(), $this->get_var('license_key'));
        }

        delete_transient($this->get_var('license_status') . '_checking');
        delete_option($this->get_var('license_status'));
        delete_option($this->get_var('license_key'));

        wp_send_json_success(array(
            'message'      => 'License deactivated',
            'license_data' => $license_data
        ), 200);

        die();
    }

    function getLicenseInfo()
    {
        $license_data = $this->getRemoteLicense();
        if (is_wp_error($license_data) || !$license_data) {
            wp_send_json_error($license_data, 423);
            return false;
        }

        $status = $license_data->license;

        if ($status) {
            if(is_multisite()) {
                update_network_option(get_main_network_id(), $this->get_var('license_status'), $status);
            } else {
                update_option($this->get_var('license_status'), $status);
            }
        }

        $expiredDate = '';
        $expireDate = '';

        if(is_multisite()) {
            $licenseKey = trim(get_network_option(get_main_network_id(), $this->get_var('license_key')));
        } else {
            $licenseKey = trim(get_option($this->get_var('license_key')));
        }

        $renewUrl = $this->getRenewUrl($licenseKey);
        $renewHTML = '';
        if ($status == 'expired') {
            $expiredDate = date('d M Y', strtotime($license_data->expires));
            $renewHTML = '<p>Your license has been expired at <b>' . $expiredDate . '</b></p>';
            $renewHTML .= '<p><a target="_blank" href="' . $renewUrl . '">click here to renew your license</a></p>';
        } else if ($status == 'valid') {
            if ($license_data->expires != 'lifetime') {
                $expireDate = date('d M Y', strtotime($license_data->expires));
                $interval = strtotime($license_data->expires) - time();
                $intervalDays = intval($interval / (60 * 60 * 24));
                if ($intervalDays < 30) {
                    $renewHTML = '<p>Your license will be expired in ' . $intervalDays . ' days</p>';
                    $renewHTML .= '<p>Please <a target="_blank" href="' . $renewUrl . '">click here to renew your license</a></p>';
                }
            }
        }
        wp_send_json_success(array(
            'status'      => $status,
            'expiredDate' => $expiredDate,
            'expireDate'  => $expireDate,
            'renewUrl'    => $renewUrl,
            'renewHtml'   => $renewHTML,
        ), 200);
    }

    function check_license()
    {
        if (get_transient($this->get_var('license_status') . '_checking')) {
            return;
        }

        $license_data = $this->getRemoteLicense();

        if (is_wp_error($license_data) || !$license_data) {
            return false;
        }

        if ($license_data && $license_data->license) {
            update_option($this->get_var('license_status'), $license_data->license);
        }

        // Set to check again in sometime later.
        set_transient(
            $this->get_var('license_status') . '_checking',
            $license_data,
            $this->get_var('cache_time')
        );
    }

    private function getRemoteLicense()
    {
        $license = trim(get_option($this->get_var('license_key')));
        if(!$license && is_multisite()) {
            $license = get_network_option(get_main_network_id(), $this->get_var('license_key'));
        }

        if (!$license) {
            return false;
        }

        if(is_multisite()) {
            $api_params = array(
                'edd_action' => 'check_license',
                'license'    => $license,
                'item_name'  => urlencode($this->get_var('item_name')),
                'item_id'    => $this->get_var('item_id'),
                'url'        => network_site_url()
            );
        } else {
            $api_params = array(
                'edd_action' => 'check_license',
                'license'    => $license,
                'item_name'  => urlencode($this->get_var('item_name')),
                'item_id'    => $this->get_var('item_id'),
                'url'        => home_url()
            );
        }

        // Call the custom API.
        $response = wp_remote_get(
            $this->get_var('store_url'),
            array(
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params
            )
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $license_data = json_decode(
            wp_remote_retrieve_body($response)
        );

        return $license_data;
    }

    private function getErrorMessage($licenseData, $licenseKey = false)
    {
        $errorMessage = 'There was an error activating the license, please verify your license is correct and try again or contact support.';

        if ($licenseData->error == 'expired') {
            $renewUrl = $this->getRenewUrl($licenseKey);
            $errorMessage = 'Your license has been expired at ' . $licenseData->expires . ' . Please <a target="_blank" href="' . $renewUrl . '">click here</a> to renew your license';
        } else if ($licenseData->error == 'no_activations_left') {
            $errorMessage = 'No Activation Site left: You have activated all the sites that your license offer. Please go to wpmanageninja.com account and review your sites. You may deactivate your unused sites from wpmanageninja account or you can purchase another license. <a target="_blank" href="' . $this->get_var('purchase_url') . '">Click Here to purchase another license</a>';
        } else if ($licenseData->error == 'missing') {
            $errorMessage = 'The given license key is not valid. Please verify that your license is correct. You may login to wpmanageninja.com account and get your valid license key for your purchase.';
        }

        return $errorMessage;
    }

    private function getRenewUrl($licenseKey = false)
    {
        if (!$licenseKey) {
            $licenseKey = get_option($this->get_var('license_key'));
        }
        if ($licenseKey) {
            $renewUrl = $this->get_var('store_url') . '/checkout/?edd_license_key=' . $licenseKey . '&download_id=' . $this->get_var('item_id');
        } else {
            $renewUrl = $this->get_var('purchase_url');
        }

        return $renewUrl;
    }
}
