<?php

namespace DynamicOOOS\DynamicLicense;

use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
}
if (!\class_exists('DynamicOOOS\\DynamicLicense\\License')) {
    class License
    {
        const LICENSE_STATUS_OPTION = '_license_status';
        const LICENSE_ERROR_OPTION = '_license_error';
        const LICENSE_DOMAIN_OPTION = '_license_domain';
        const LICENSE_KEY_OPTION = '_license_key';
        /**
         * @var array
         */
        private $plugin;
        private $notices;
        /**
         * @var bool
         */
        private $should_attempt_auto_activation = \false;
        /**
         * @var bool
         */
        private $is_staging = \false;
        private $messages;
        public function __construct(array $plugin, $notices, $messages)
        {
            $this->plugin = $plugin;
            $this->notices = $notices;
            $this->messages = $messages;
            $this->activation_advisor();
        }
        public function activation_advisor()
        {
            //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $tab_license = isset($_GET['page']) && $_GET['page'] === $this->plugin['admin_license_page'];
            if (is_admin() && !$tab_license && current_user_can('administrator') && !$this->is_license_active(\false)) {
                // translators: %1$s: Open URL. %2$s: Close URL. %3$s: Open URL. %4$s: Close URL.
                $message = \sprintf($this->messages['buy'], '<a href="' . admin_url() . 'admin.php?page=' . $this->plugin['admin_license_page'] . '">', '</a>', '<a href="' . $this->plugin['pricing_page'] . '" target="blank">', '</a>');
                $this->notices->error($message);
                add_filter('plugin_action_links_' . $this->plugin['plugin_base'], [$this, 'plugin_action_links_license']);
                add_action('in_plugin_update_message-' . $this->plugin['plugin_base'], [$this, 'error_message_update'], 10, 2);
            }
        }
        /**
         * Define the upgrader_pre_download callback
         */
        public function filter_upgrader_pre_download($false, $package, $instance)
        {
            $plugin = \false;
            if (\property_exists($instance, 'skin')) {
                if ($instance->skin) {
                    if (\property_exists($instance->skin, 'plugin')) {
                        // Update from page
                        if ($instance->skin->plugin) {
                            $plugin = \explode('/', $instance->skin->plugin)[0];
                        }
                    }
                    // Update via Ajax
                    if (!$plugin && isset($instance->skin->plugin_info['TextDomain'])) {
                        $plugin = $instance->skin->plugin_info['TextDomain'];
                    }
                }
            }
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            if ($this->plugin['slug'] === $plugin || isset($_POST[$this->plugin['prefix'] . '_version'])) {
                return $this->upgrader_pre_download($package, $instance);
            }
            return $false;
        }
        /**
         * @param bool $fresh false gets cache version, true checks remote status
         * @return bool
         */
        public function is_license_active($fresh = \true)
        {
            if ($fresh) {
                $this->refresh_license_status();
            }
            return get_option($this->plugin['prefix'] . self::LICENSE_STATUS_OPTION, '') === 'active';
        }
        /**
         * Summary
         *
         * @param string $status either 'active' or 'inactive'
         * @return void
         */
        private function set_license_status($status)
        {
            update_option(self::LICENSE_STATUS_OPTION, 'active');
        }
        /**
         * Get error message from last failed status check.
         *
         * @return string
         */
        public function get_license_error()
        {
            return get_option($this->plugin['prefix'] . self::LICENSE_ERROR_OPTION, '');
        }
        /**
         * Set license status to inactive and save error message.
         *
         * @param string $error
         */
        private function set_license_error($error)
        {
            $this->set_license_status('inactive');
            update_option($this->plugin['prefix'] . self::LICENSE_ERROR_OPTION, $error);
        }
        /**
         * Set License Key
         *
         * @param string $key
         * @return void
         */
        private function set_license_key($key)
        {
            update_option($this->plugin['prefix'] . self::LICENSE_KEY_OPTION, $key);
        }
        /**
         * Get License Key
         *
         * @return string
         */
        public function get_license_key()
        {
            return get_option($this->plugin['prefix'] . self::LICENSE_KEY_OPTION, '');
        }
        /**
         * Get last 4 digits of License Key
         *
         * @return string
         */
        public function get_license_key_last_4_digits()
        {
            return \substr($this->get_license_key(), -4);
        }
        /**
         * Activate new License Key
         *
         * @param string $key
         * @return array
         */
        public function activate_new_license_key($key)
        {
            // TODO: check if valid.
            $this->set_license_key($key);
            return $this->activate_license();
        }
        /**
         * Get License Domain
         *
         * @return string|bool
         */
        public function get_last_active_domain()
        {
            return get_option($this->plugin['prefix'] . self::LICENSE_DOMAIN_OPTION);
        }
        /**
         * Set License Domain
         *
         * @param string $domain
         * @return void
         */
        public function set_last_active_domain($domain)
        {
            update_option($this->plugin['prefix'] . self::LICENSE_DOMAIN_OPTION, $domain);
        }
        /**
         * Get current domain without protocol
         *
         * @return string
         */
        public function get_current_domain()
        {
            $domain = get_bloginfo('wpurl');
            $domain = \str_replace('https://', '', $domain);
            $domain = \str_replace('http://', '', $domain);
            return $domain;
        }
        /**
         * Update the license system options and variables based on the server response.
         *
         * @param array $response
         * @return void
         */
        public function handle_status_check_response($response, $domain)
        {
            $this->should_attempt_auto_activation = \false;
            $this->is_staging = \false;
            if (!$response) {
                // trouble contacting the server. No changes:
                return;
            }
            if (($response['staging'] ?? '') === 'yes') {
                $this->is_staging = \true;
            }
            $status_code = $response['status_code'] ?? '';
            if ('e002' === $status_code) {
                // key is invalid:
                $this->set_license_error($response['message']);
                return;
            }
            if (\in_array($status_code, ['s203', 'e204'], \true)) {
                // key is not active for current domain, we should not attempt activation:
                $this->set_license_error($response['message'] . " (domain: {$domain})");
                return;
            }
            if (\in_array($status_code, ['s205', 's215'], \true)) {
                // if license is valid and active for domain:
                if (($response['license_status'] ?? '') === 'expired') {
                    // But expired:
                    $this->set_license_error($response['message']);
                    $this->should_attempt_auto_activation = \true;
                    return;
                }
                $this->set_license_status('active');
                $this->set_last_active_domain($this->get_current_domain());
                return;
            }
            // other cases, just set the error with message:
            $this->set_license_error($response['message'] ?? esc_html__('Unknown', 'dynamic-content-for-elementor'));
        }
        /**
         * @return bool|array
         */
        public function remote_status_check($domain)
        {
            return $this->call_api('status-check', $this->get_license_key(), $domain);
        }
        /**
         * Refresh license status.
         *
         * @return void
         */
        public function refresh_license_status()
        {
            if (!$this->get_license_key()) {
                $this->set_license_error(esc_html__('No license present', 'dynamic-content-for-elementor'));
                return;
            }
            $domain = $this->get_current_domain();
            $response = $this->remote_status_check($domain);
            $this->handle_status_check_response($response, $domain);
        }
        /**
         * Refresh license status. If license was not deliberately deactivated try
         * to reactivate the license for this domain.
         *
         * @return void
         */
        public function refresh_and_repair_license_status()
        {
            $this->refresh_license_status();
            if ($this->should_attempt_auto_activation) {
                $this->activate_license();
                // TODO: refresh again?
            }
        }
        /**
         * Ask to the server to activate the license
         *
         * @return string activation message
         */
        private function activate_license_request()
        {
            $response = $this->call_api('activate', $this->get_license_key(), $this->get_current_domain());
            if ($response) {
                return $response['message'];
            }
            return esc_html__('Problem contacting the server, try again in a few minutes.', 'dynamic-content-for-elementor');
        }
        /**
         * Ask the server to deactivate the license
         *
         * @return string activation message
         */
        private function deactivate_license_request()
        {
            $response = $this->call_api('deactivate', $this->get_license_key(), $this->get_current_domain());
            if ($response) {
                return $response['message'];
            }
            return esc_html__('Problem contacting the server, try again in a few minutes.', 'dynamic-content-for-elementor');
        }
        /**
         * Ask the server to deactivate the license. Refresh license status.
         * Delete the key for staging sites.
         *
         * @return array [success, msg]
         */
        public function deactivate_license()
        {
            $msg = $this->deactivate_license_request();
            $success = !$this->is_license_active(\true);
            if ($this->is_staging) {
                $this->set_license_key('');
                $this->refresh_license_status();
                return [\true, esc_html__('Success', 'dynamic-content-for-elementor')];
            }
            return [$success, $msg];
        }
        /**
         * Ask the server to activate the license. Refresh license status.
         *
         * @return array [success, msg]
         */
        public function activate_license()
        {
            $msg = $this->activate_license_request();
            $success = $this->is_license_active(\true);
            return [$success, $msg];
        }
        /**
         * Active beta releases
         *
         * @return void
         */
        public function activate_beta_releases()
        {
            update_option($this->plugin['beta_option'], \true);
        }
        /**
         * Deactivate beta releases
         *
         * @return void
         */
        public function deactivate_beta_releases()
        {
            update_option($this->plugin['beta_option'], \false);
        }
        /**
         * Check if beta releases are activated
         *
         * @return boolean
         */
        public function is_beta_releases_activated()
        {
            return get_option($this->plugin['beta_option']);
        }
        /**
         * Make a request to license server to activate, deactivate or check the status of the license
         *
         * @param string $action
         * @param string $license_key
         * @param string $domain
         * @return bool|array
         */
        public function call_api(string $action, string $license_key, string $domain)
        {
            global $wp_version;
            $args = ['woo_sl_action' => $action, 'licence_key' => $license_key, 'product_unique_id' => $this->plugin['product_unique_id'], 'domain' => $domain, 'api_version' => '1.1', 'wp-version' => $wp_version, 'version' => $this->plugin['version'], 'is_multisite' => is_multisite(), 'php' => \PHP_VERSION];
            $request_uri = $this->plugin['license_url'] . '/api.php?' . \http_build_query($args);
            $data = wp_remote_get($request_uri);
            if (is_wp_error($data)) {
                return \false;
            }
            if ($data['response']['code'] !== 200) {
                return \false;
            }
            $body = \json_decode($data['body'], \true);
            if (\is_array($body)) {
                return \reset($body);
            }
            return \false;
        }
        public function plugin_action_links_license($links)
        {
            $links['license'] = '<a style="color:brown;" title="' . esc_html__('Activate license', 'dynamic-content-for-elementor') . '" href="' . admin_url() . 'admin.php?page=' . $this->plugin['admin_license_page'] . '"><b>' . esc_html__('License', 'dynamic-content-for-elementor') . '</b></a>';
            return $links;
        }
        /**
         * Error Message on Update
         *
         * @param array<mixed> $plugin_data
         * @param object $response
         * @return void
         */
        public function error_message_update($plugin_data, $response)
        {
            \printf('&nbsp;<strong>%1$s</strong>', esc_html__('The license is not active.', 'dynamic-content-for-elementor'));
        }
        public function domain_mismatch_check()
        {
            if ($this->get_license_key() && !$this->is_license_active() && $this->get_last_active_domain() && $this->get_last_active_domain() !== $this->get_current_domain()) {
                $this->notices->warning(\sprintf(esc_html__('License Mismatch. Your license key doesn\'t match your current domain. This is likely due to a change in the domain URL. You can reactivate your license now. Remember to deactivate the one for the old domain from your license area on Dynamic.ooo\'s site', 'dynamic-content-for-elementor'), '<a class="btn button" href="' . admin_url() . 'admin.php?page=' . $this->plugin['admin_license_page'] . '">', '</a>'));
            }
        }
    }
}
