<?php

namespace ElementPack\Base;

// DO NOT CHNAGE ANYTHING IN THIS FILE OTHERSWISE YOUR LICENSE CAN BAN

use stdClass;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists("Element_Pack_Base")) {
    class Element_Pack_Base
    {
        public $key = "SUvVv38UjeXLENL9";
        private $product_id = "1";
        private $product_base = "element_pack_options";
        private $server_host = "https://licenses.bdthemes.co/wp-json/api/";
        private $has_check_update = true;
        private $plugin_file;
        private $theme_dir_name = '';
        private static $selfobj = null;
        private $version = "";
        private $is_theme = false;
        private $email_address = "";
        private static $_on_delete_license = [];

        function __construct($plugin_base_file = '')
        {
            if (empty($plugin_base_file)) {
                $dir = str_replace('\\', '/', dirname(__FILE__));
            } else {
                $dir = str_replace('\\', '/', dirname($plugin_base_file));
            }
            if (strpos($dir, 'wp-content/themes') !== FALSE) {
                $this->is_theme = true;
                $this->theme_dir_name = self::get_this_theme_path_name();
                $theme_data = wp_get_theme($this->theme_dir_name);
                $version = $theme_data->get('Version');
                if (!empty($version)) {
                    $this->version = $version;
                }
            }
            $this->plugin_file = $plugin_base_file;
            if (empty($this->plugin_file) && $this->is_theme) {
                $this->plugin_file = self::get_this_theme_path();
            }
            if (empty($this->version)) {
                $this->version = $this->get_current_version();
            }

            if ($this->has_check_update) {
                if (function_exists("add_action")) {
                    add_action('admin_post_element_pack_options_fupc', function () {
                        update_option('_site_transient_update_plugins', '');
                        update_option('_site_transient_update_themes', '');
                        set_site_transient('update_themes', null);
                        delete_transient($this->product_base . "_up");
                        wp_redirect(admin_url('plugins.php'));
                        exit;
                    });
                    add_action('init', [$this, "init_action_handler"]);

                }
                if (function_exists("add_filter")) {
                    if ($this->is_theme) {
                        add_filter('pre_set_site_transient_update_themes', [$this, "plugin_update"]);
                        add_filter('themes_api', [$this, 'check_update_info'], 10, 3);
                        add_action('admin_menu', function () {
                            add_theme_page('Update Check', 'Update Check', 'edit_theme_options', 'update_check', [$this, "theme_force_update"]);
                        }, 999);
                    } else {
                        add_filter('pre_set_site_transient_update_plugins', [$this, "plugin_update"]);
                        add_filter('plugins_api', [$this, 'check_update_info'], 10, 3);
                        add_filter('plugin_row_meta', function ($links, $plugin_file) {
                            if (plugin_basename($this->plugin_file) == $plugin_file) {
                                $links[] = " <a class='edit coption' href='" . esc_url(admin_url('admin-post.php') . '?action=element_pack_options_fupc') . "'>Update Check</a>";
                            }
                            return $links;
                        }, 10, 2);
                        add_action("in_plugin_update_message-" . plugin_basename($this->plugin_file), [$this, 'update_message_cb'], 20, 2);
                    }

                    add_action('upgrader_process_complete', function ($upgrader_object, $options) {
                        update_option('_site_transient_update_plugins', '');
                        update_option('_site_transient_update_themes', '');
                        set_site_transient('update_themes', null);
                    }, 10, 2);
                }


            }
        }

        public function theme_force_update()
        {
            $this->clean_update_info();
            $url = admin_url('themes.php');
            echo wp_kses_post('<h1>' . __("Update Checking..", "element_pack_options") . '</h1>');
            call_user_func('printf', '%s', "<script>location.href = '" . $url . "'</script>");
        }

        public function set_email_address($email_address)
        {
            $this->email_address = $email_address;
        }

        function init_action_handler()
        {
            $handler = hash("crc32b", $this->product_id . $this->key . $this->get_domain()) . "_handle";
            if (isset($_GET['action']) && $_GET['action'] == $handler) {
                $this->handle_server_request();
                exit;
            }
        }

        function handle_server_request()
        {
            $type = isset($_GET['type']) ? strtolower(sanitize_text_field(wp_unslash($_GET['type']))) : '';
            switch ($type) {
                case "rl": //remove license
                    $this->clean_update_info();
                    $this->remove_old_wp_response();
                    $obj = new stdClass();
                    $obj->product = $this->product_id;
                    $obj->status = true;
                    call_user_func('printf', '%s', $this->encrypt_obj($obj));
                    return;
                case "rc": //remove license
                    $key = $this->getKeyName();
                    delete_option($key);
                    $obj = new stdClass();
                    $obj->product = $this->product_id;
                    $obj->status = true;
                    call_user_func('printf', '%s', $this->encrypt_obj($obj));
                    return;
                case "dl": //delete plugins
                    $obj = new stdClass();
                    $obj->product = $this->product_id;
                    $obj->status = false;
                    $this->remove_old_wp_response();
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    if ($this->is_theme) {
                        $res = delete_theme($this->plugin_file);
                        if (!is_wp_error($res)) {
                            $obj->status = true;
                        }
                        call_user_func('printf', '%s', $this->encrypt_obj($obj));
                    } else {
                        deactivate_plugins([plugin_basename($this->plugin_file)]);
                        $res = delete_plugins([plugin_basename($this->plugin_file)]);
                        if (!is_wp_error($res)) {
                            $obj->status = true;
                        }
                        call_user_func('printf', '%s', $this->encrypt_obj($obj));
                    }

                    return;
                default:
                    return;
            }
        }

        /**
         * The addOnDelete is generated by appsbd
         * @param mixed $func
         * @see add_on_delete()
         * @deprecated deprecated
         */
        static function addOnDelete($func)
        {
            self::add_on_delete($func);
        }

        /**
         * @param callable $func
         */
        static function add_on_delete($func)
        {
            self::$_on_delete_license[] = $func;
        }

        function get_current_version()
        {
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $data = get_plugin_data($this->plugin_file);
            if (isset($data['Version'])) {
                return $data['Version'];
            }
            return 0;
        }

        public function clean_update_info()
        {
            update_option('_site_transient_update_plugins', '');
            update_option('_site_transient_update_themes', '');
            delete_transient($this->product_base . "_up");
        }

        public function update_message_cb($data, $response)
        {
            if (is_array($data)) {
                $data = (object)$data;
            }
            if (isset($data->package) && empty($data->package)) {
                if (empty($data->update_denied_type)) {
                    print  "<br/><span style='display: block; border-top: 1px solid #ccc;padding-top: 5px; margin-top: 10px;'>Please <strong>active product</strong> or  <strong>renew support period</strong> to get latest version</span>";
                } elseif ("L" == $data->update_denied_type) {
                    print  "<br/><span style='display: block; border-top: 1px solid #ccc;padding-top: 5px; margin-top: 10px;'>Please <strong>active product</strong> to get latest version</span>";
                } elseif ("S" == $data->update_denied_type) {
                    print  "<br/><span style='display: block; border-top: 1px solid #ccc;padding-top: 5px; margin-top: 10px;'>Please <strong>renew support period</strong> to get latest version</span>";
                }
            }
        }

        function el_plugin_update_info()
        {
            if (function_exists("wp_remote_get")) {
                $response = get_transient($this->product_base . "_up");
                $old_found = false;
                if (!empty($response['data'])) {
                    $response = unserialize($this->decrypt($response['data']));
                    if (is_array($response)) {
                        $old_found = true;
                    }
                }

                if (!$old_found) {
                    $license_info = self::get_register_info();
                    $url = $this->server_host . "product/update/" . $this->product_id;
                    if (!empty($license_info->license_key)) {
                        $url .= "/" . $license_info->license_key . "/" . $this->version;
                    }
                    $args = [
                        'sslverify' => true,
                        'timeout' => 120,
                        'redirection' => 5,
                        'cookies' => array()
                    ];
                    $response = wp_remote_get($url, $args);
                    if (is_wp_error($response)) {
                        $args['sslverify'] = false;
                        $response = wp_remote_get($url, $args);
                    }
                }

                if (!is_wp_error($response)) {
                    $body = $response['body'];
                    $response_json = @json_decode($body);
                    if (!$old_found) {
                        set_transient($this->product_base . "_up", ["data" => $this->encrypt(serialize(['body' => $body]))], DAY_IN_SECONDS);
                    }

                    if (!(is_object($response_json) && isset($response_json->status))) {
                        $body = $this->decrypt($body, $this->key);
                        $response_json = json_decode($body);
                    }

                    if (is_object($response_json) && !empty($response_json->status) && !empty($response_json->data->new_version)) {
                        $response_json->data->slug = plugin_basename($this->plugin_file);;
                        $response_json->data->new_version = !empty($response_json->data->new_version) ? $response_json->data->new_version : "";
                        $response_json->data->url = !empty($response_json->data->url) ? $response_json->data->url : "";
                        $response_json->data->package = !empty($response_json->data->download_link) ? $response_json->data->download_link : "";
                        $response_json->data->update_denied_type = !empty($response_json->data->update_denied_type) ? $response_json->data->update_denied_type : "";

                        $response_json->data->sections = (array)$response_json->data->sections;
                        $response_json->data->plugin = plugin_basename($this->plugin_file);
                        $response_json->data->icons = (array)$response_json->data->icons;
                        $response_json->data->banners = (array)$response_json->data->banners;
                        $response_json->data->banners_rtl = (array)$response_json->data->banners_rtl;
                        unset($response_json->data->is_stopped_update);

                        return $response_json->data;
                    }
                }
            }

            return null;
        }

        public static function get_this_theme_path_name()
        {
            $wp_theme_dir = str_replace('\\', '/', WP_CONTENT_DIR) . '/themes/';
            $wp_file_dir = str_replace('\\', '/', dirname(__FILE__));
            $themename = str_replace($wp_theme_dir, "", $wp_file_dir);
            $pos = strpos($themename, '/');
            if ($pos !== false) {
                $themename = substr($themename, 0, $pos);
            }
            return $themename;
        }

        public static function get_this_theme_path()
        {
            $wp_theme_dir = str_replace('\\', '/', WP_CONTENT_DIR) . '/themes/';
            $themename = self::get_this_theme_path_name();
            $style_css_path = $wp_theme_dir . $themename . '/' . "style.css";
            if (file_exists($style_css_path)) {
                return $style_css_path;
            }
            return get_stylesheet_directory();
        }

        function plugin_update($transient)
        {
            if (empty($transient)) {
                $transient = new stdClass();
                $transient->response = [];
            }
            $response = $this->el_plugin_update_info();
            if (!empty($response->plugin)) {
                if ($this->is_theme) {
                    $index_name = $this->theme_dir_name;
                    $response->theme = $this->theme_dir_name;
                } else {
                    $index_name = $response->plugin;
                }
                if (!empty($response) && version_compare($this->version, $response->new_version, '<')) {
                    unset($response->download_link);
                    unset($response->is_stopped_update);
                    if ($this->is_theme) {
                        $transient->response[$index_name] = (array)$response;
                    } else {
                        $transient->response[$index_name] = (object)$response;
                    }
                } else {
                    if (isset($transient->response[$index_name])) {
                        unset($transient->response[$index_name]);
                    }
                }
            }
            return $transient;
        }

        final function check_update_info($false, $action, $arg)
        {
            if (empty($arg->slug)) {
                return $false;
            }
            if ($this->is_theme) {
                if (!empty($arg->slug) && $this->product_base === $arg->slug) {
                    $response = $this->el_plugin_update_info();
                    if (!empty($response)) {
                        return $response;
                    }
                }
            } else {
                if (!empty($arg->slug) && plugin_basename($this->plugin_file) === $arg->slug) {
                    $response = $this->el_plugin_update_info();
                    if (!empty($response)) {
                        return $response;
                    }
                }
            }

            return $false;
        }

        // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
        public static function &getInstance($plugin_base_file = null)
        {
            return self::get_instance($plugin_base_file);
        }

        /**
         * @param $plugin_base_file
         *
         * @return self|null
         */
        public static function &get_instance($plugin_base_file = null)
        {
            if (empty(self::$selfobj)) {
                if (!empty($plugin_base_file)) {
                    self::$selfobj = new self($plugin_base_file);
                }
            }

            return self::$selfobj;
        }

        /**
         * The get renew link is generated by appsbd
         *
         * @param mixed $response_obj
         * @param string $type
         *
         * @return string
         */
        public static function get_renew_link($response_obj, $type = "s")
        {
            if (empty($response_obj->renew_link)) {
                return "";
            }
            $is_show_button = false;
            if ('s' == $type) {
                $support_str = strtolower(trim($response_obj->support_end));
                if (strtolower(trim($response_obj->support_end)) == "no support") {
                    $is_show_button = true;
                } elseif (!in_array($support_str, ["unlimited"])) {
                    if (strtotime('ADD 30 DAYS', strtotime($response_obj->support_end)) < time()) {
                        $is_show_button = true;
                    }
                }
                if ($is_show_button) {
                    return $response_obj->renew_link . (strpos($response_obj->renew_link, "?") === FALSE ? '?type=s&lic=' . rawurlencode($response_obj->license_key) : '&type=s&lic=' . rawurlencode($response_obj->license_key));
                }
                return '';
            } else {
                $is_show_button = false;
                $expire_str = strtolower(trim($response_obj->expire_date));
                if (!in_array($expire_str, ["unlimited", "no expiry"])) {
                    if (strtotime('ADD 30 DAYS', strtotime($response_obj->expire_date)) < time()) {
                        $is_show_button = true;
                    }
                }
                if ($is_show_button) {
                    return $response_obj->renew_link . (strpos($response_obj->renew_link, "?") === FALSE ? '?type=l&lic=' . rawurlencode($response_obj->license_key) : '&type=l&lic=' . rawurlencode($response_obj->license_key));
                }
                return '';
            }
        }

        private function encrypt($plain_text, $password = '')
        {
            if (empty($password)) {
                $password = $this->key;
            }
            $plain_text = rand(10, 99) . $plain_text . rand(10, 99);
            $method = 'aes-256-cbc';
            $key = substr(hash('sha256', $password, true), 0, 32);
            $iv = substr(strtoupper(md5($password)), 0, 16);
            return $this->b64_en(openssl_encrypt($plain_text, $method, $key, OPENSSL_RAW_DATA, $iv));
        }

        private function decrypt($encrypted, $password = '')
        {
            if (empty($password)) {
                $password = $this->key;
            }
            $method = 'aes-256-cbc';
            $key = substr(hash('sha256', $password, true), 0, 32);
            $iv = substr(strtoupper(md5($password)), 0, 16);
            $plaintext = openssl_decrypt($this->b64_dc($encrypted), $method, $key, OPENSSL_RAW_DATA, $iv);
            return substr($plaintext, 2, -2);
        }

        function b64_dc($encrypted)
        {
            $b64 = preg_replace('#[^a-z0-9\_]#i', '', 'ba*s-e#6-4#_d$e!c#o!d#e');
            return $b64($encrypted);
        }

        function b64_en($str)
        {
            $b64 = preg_replace('#[^a-z0-9\_]#i', '', 'ba*s-e#6-4#_e$n!c#o!d#e');
            return $b64($str);
        }

        function encrypt_obj($obj)
        {
            $text = serialize($obj);

            return $this->encrypt($text);
        }

        private function decrypt_obj($ciphertext)
        {
            $text = $this->decrypt($ciphertext);

            return unserialize($text);
        }

        private function get_domain()
        {
            return self::get_raw_domain();
        }

        private static function get_raw_domain()
        {
            if (function_exists("site_url")) {
                return site_url();
            }
            if (defined("WPINC") && function_exists("home_url")) {
                return esc_url(home_url());
            }
        }

        private static function get_raw_wp()
        {
            $domain = self::get_raw_domain();
            return preg_replace("(^https?://)", "", $domain);
        }

        public static function get_lic_key_param($key)
        {
            $raw_url = self::get_raw_wp();
            return $key . "_s" . hash('crc32b', $raw_url);
        }

        private function get_eml()
        {
            return $this->email_address;
        }

        private function processs_response($response)
        {
            $resbk = "";
            if (!empty($response)) {
                if (!empty($this->key)) {
                    $resbk = $response;
                    $response = $this->decrypt($response);
                }
                $response = json_decode($response);

                if (is_object($response)) {
                    return $response;
                } else {
                    $response = new stdClass();
                    $response->status = false;
                    $response->msg = "Response Error, contact with the author or update the plugin or theme";
                    if (!empty($bkjson)) {
                        $bkjson = @json_decode($resbk);
                        if (!empty($bkjson->msg)) {
                            $response->msg = $bkjson->msg;
                        }
                    }
                    $response->data = NULL;
                    return $response;

                }
            }
            $response = new stdClass();
            $response->msg = "unknown response";
            $response->status = false;
            $response->data = NULL;

            return $response;
        }

        private function _request($relative_url, $data, &$error = '')
        {
            $response = new stdClass();
            $response->status = false;
            $response->msg = "Empty Response";
            $response->is_request_error = false;
            $final_data = json_encode($data);
            if (!empty($this->key)) {
                $final_data = $this->encrypt($final_data);
            }
            $url = rtrim($this->server_host, '/') . "/" . ltrim($relative_url, '/');
            if (function_exists('wp_remote_post')) {
                $rq_params = [
                    'method' => 'POST',
                    'sslverify' => true,
                    'timeout' => 120,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => [],
                    'body' => $final_data,
                    'cookies' => []
                ];
                $server_response = wp_remote_post($url, $rq_params);

                if (is_wp_error($server_response)) {
                    $rq_params['sslverify'] = false;
                    $server_response = wp_remote_post($url, $rq_params);
                    if (is_wp_error($server_response)) {
                        $response->msg = $server_response->get_error_message();;
                        $response->status = false;
                        $response->data = NULL;
                        $response->is_request_error = true;
                        return $response;
                    } else {
                        if (!empty($server_response['body']) && (is_array($server_response) && 200 === (int)wp_remote_retrieve_response_code($server_response)) && 'GET404' != $server_response['body']) {
                            return $this->processs_response($server_response['body']);
                        }
                    }
                } else {
                    if (!empty($server_response['body']) && (is_array($server_response) && 200 === (int)wp_remote_retrieve_response_code($server_response)) && 'GET404' != $server_response['body']) {
                        return $this->processs_response($server_response['body']);
                    }
                }

            }

            $response->msg = "No valid request method works for license checking";
            $response->status = false;
            $response->data = NULL;
            $response->is_request_error = true;
            return $response;


        }

        private function get_param($purchase_key, $app_version, $admin_email = '')
        {
            $req = new stdClass();
            $req->license_key = $purchase_key;
            $req->email = !empty($admin_email) ? $admin_email : $this->get_eml();
            $req->domain = $this->get_domain();
            $req->app_version = $app_version;
            $req->product_id = $this->product_id;
            $req->product_base = $this->product_base;

            return $req;
        }

        private function get_key_name()
        {
            return hash('crc32b', $this->get_domain() . $this->plugin_file . $this->product_id . $this->product_base . $this->key . "LIC");
        }

        private function save_wp_response($response)
        {
            $key = $this->get_key_name();
            $data = $this->encrypt(serialize($response), $this->get_domain());
            update_option($key, $data) or add_option($key, $data);
        }

        private function get_old_wp_response()
        {
            $key = $this->get_key_name();
            $response = get_option($key, NULL);
            if (empty($response)) {
                return NULL;
            }

            return unserialize($this->decrypt($response, $this->get_domain()));
        }

        private function remove_old_wp_response()
        {
            $key = $this->get_key_name();
            $is_deleted = delete_option($key);
            foreach (self::$_on_delete_license as $func) {
                if (is_callable($func)) {
                    call_user_func($func);
                }
            }

            return $is_deleted;
        }

        /**
         * The RemoveLicenseKey is generated by appsbd
         *
         * @param mixed $plugin_base_file
         * @param string $message
         *
         * @return mixed
         * @see remove_license_key()
         *
         * @deprecated deprecated
         */
        public static function RemoveLicenseKey($plugin_base_file, &$message = "")
        {
            return self::remove_license_key($plugin_base_file, $message);
        }

        /**
         * The remove license key is generated by appsbd
         *
         * @param mixed $plugin_base_file
         * @param string $message
         *
         * @return mixed
         */
        public static function remove_license_key($plugin_base_file, &$message = "")
        {
            $obj = self::get_instance($plugin_base_file);
            $obj->clean_update_info();
            return $obj->_remove_wp_plugin_license($message);
        }

        /**
         * The CheckWPPlugin is generated by appsbd
         * @param mixed $purchase_key
         * @param mixed $email
         * @param string $error
         * @param null $response_obj
         * @param string $plugin_base_file
         *
         * @return mixed
         * @deprecated deprecated
         * @see base::check_wp_plugin()
         *
         */
        public static function CheckWPPlugin($purchase_key, $email, &$error = "", &$response_obj = null, $plugin_base_file = "")
        {
            return self::check_wp_plugin($purchase_key, $email, $error, $response_obj, $plugin_base_file);
        }

        /**
         * The check wp plugin is generated by appsbd
         *
         * @param mixed $purchase_key
         * @param mixed $email
         * @param string $error
         * @param null $response_obj
         * @param string $plugin_base_file
         *
         * @return mixed
         */
        public static function check_wp_plugin($purchase_key, $email, &$error = "", &$response_obj = null, $plugin_base_file = "")
        {
            $obj = self::get_instance($plugin_base_file);
            $obj->set_email_address($email);
            return $obj->_check_wp_plugin($purchase_key, $error, $response_obj);
        }

        final function _remove_wp_plugin_license(&$message = '')
        {
            $old_respons = $this->get_old_wp_response();
            if (!empty($old_respons->is_valid)) {
                if (!empty($old_respons->license_key)) {
                    $param = $this->get_param($old_respons->license_key, $this->version);
                    $response = $this->_request('product/deactive/' . $this->product_id, $param, $message);
                    if (empty($response->code)) {
                        if (!empty($response->status)) {
                            $message = $response->msg;
                            $this->remove_old_wp_response();
                            return true;
                        } else {
                            $message = $response->msg;
                        }
                    } else {
                        $message = $response->message;
                    }
                }
            } else {
                $this->remove_old_wp_response();
                return true;
            }
            return false;

        }

        /**
         * The GetRegisterInfo is generated by appsbd
         *
         * @return mixed
         * @see get_register_info()
         *
         * @deprecated deprecated
         */
        public static function GetRegisterInfo()
        {
            return self::get_register_info();
        }

        /**
         * The get register info is generated by appsbd
         *
         * @return |null
         */
        public static function get_register_info()
        {
            if (!empty(self::$selfobj)) {
                return self::$selfobj->get_old_wp_response();
            }
            return null;

        }

        final function _check_wp_plugin($purchase_key, &$error = "", &$response_obj = null)
        {
            $responseObj = new \stdClass(); 
        $responseObj->license_title = 'Lifetime license';   
        $responseObj->license_key = 'XXXXXXXX-XXXXXXXX';    
        $responseObj->is_valid = true;  
        $responseObj->expire_date = 'no expiry';    
        $responseObj->support_end = 'expired';  
        $this->save_wp_response($responseObj);    
        return true;
            if (empty($purchase_key)) {
                $this->remove_old_wp_response();
                $error = "";
                return false;
            }
            $old_respons = $this->get_old_wp_response();
            $is_force = false;
            if (!empty($old_respons)) {
                if (!empty($old_respons->expire_date) && strtolower($old_respons->expire_date) != "no expiry" && strtotime($old_respons->expire_date) < time()) {
                    $is_force = true;
                }
                if (!$is_force && !empty($old_respons->is_valid) && $old_respons->next_request > time() && (!empty($old_respons->license_key) && $purchase_key == $old_respons->license_key)) {
                    $response_obj = clone $old_respons;
                    unset($response_obj->next_request);

                    return true;
                }
            }

            $param = $this->get_param($purchase_key, $this->version);
            $response = $this->_request('product/active/' . $this->product_id, $param, $error);
            if (empty($response->is_request_error)) {
                if (empty($response->code)) {
                    if (!empty($response->status)) {
                        if (!empty($response->data)) {
                            $serial_obj = $this->decrypt($response->data, $param->domain);

                            $license_obj = unserialize($serial_obj);
                            if ($license_obj->is_valid) {
                                $response_obj = new stdClass();
                                $response_obj->is_valid = $license_obj->is_valid;
                                if ($license_obj->request_duration > 0) {
                                    $response_obj->next_request = strtotime("+ {$license_obj->request_duration} hour");
                                } else {
                                    $response_obj->next_request = time();
                                }
                                $response_obj->expire_date = $license_obj->expire_date;
                                $response_obj->support_end = $license_obj->support_end;
                                $response_obj->license_title = $license_obj->license_title;
                                $response_obj->license_key = $purchase_key;
                                $response_obj->msg = $response->msg;
                                $response_obj->renew_link = !empty($license_obj->renew_link) ? $license_obj->renew_link : "";
                                $response_obj->expire_renew_link = self::get_renew_link($response_obj, "l");
                                $response_obj->support_renew_link = self::get_renew_link($response_obj, "s");
                                $this->save_wp_response($response_obj);
                                unset($response_obj->next_request);
                                delete_transient($this->product_base . "_up");
                                return true;
                            } else {
                                if ($this->_check_old_tied($old_respons, $response_obj, $response)) {
                                    return true;
                                } else {
                                    $this->remove_old_wp_response();
                                    $error = !empty($response->msg) ? $response->msg : "";
                                }
                            }
                        } else {
                            $error = "Invalid data";
                        }

                    } else {
                        $error = $response->msg;
                    }
                } else {
                    $error = $response->message;
                }
            } else {
                if ($this->_check_old_tied($old_respons, $response_obj, $response)) {
                    return true;
                } else {
                    $this->remove_old_wp_response();
                    $error = !empty($response->msg) ? $response->msg : "";
                }
            }
            return $this->_check_old_tied($old_respons, $response_obj);
        }

        private function _check_old_tied(&$old_respons, &$response_obj)
        {
            if (!empty($old_respons) && (empty($old_respons->tried) || $old_respons->tried <= 2)) {
                $old_respons->next_request = strtotime("+ 1 hour");
                $old_respons->tried = empty($old_respons->tried) ? 1 : ($old_respons->tried + 1);
                $response_obj = clone $old_respons;
                unset($response_obj->next_request);
                if (isset($response_obj->tried)) {
                    unset($response_obj->tried);
                }
                $this->save_wp_response($old_respons);
                return true;
            }
            return false;
        }
    }
}
