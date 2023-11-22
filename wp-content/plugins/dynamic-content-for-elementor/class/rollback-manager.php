<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class RollbackManager
{
    public function __construct()
    {
        add_action('admin_post_dce_rollback', [$this, 'post_rollback']);
    }
    /**
     * Function fired by 'admin_post_dce_rollback' action
     *
     * @return void
     */
    public function post_rollback()
    {
        if (!wp_verify_nonce($_POST['dce-settings-page'], 'dce-settings-page')) {
            wp_die(__('Nonce verification error.', 'dynamic-content-for-elementor'));
        }
        $rollback_versions = $this->get_rollback_versions();
        if (empty($_POST['version']) || !\in_array($_POST['version'], $rollback_versions, \true)) {
            wp_die(esc_html__('Error occurred, the version selected is invalid. Try selecting different version.', 'dynamic-content-for-elementor'));
        }
        $package_url = $this->get_plugin_package_url($_POST['version']);
        if (is_wp_error($package_url)) {
            wp_die($package_url);
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        $rollback = new \DynamicContentForElementor\Rollback(['version' => sanitize_text_field($_POST['version']), 'plugin_name' => DCE_PLUGIN_BASE, 'plugin_slug' => DCE_SLUG, 'package_url' => $package_url]);
        $rollback->run();
        wp_die('', esc_html__('Rollback to Previous Version', 'dynamic-content-for-elementor'), ['response' => 200]);
    }
    /**
     * Retrieve all previous versions so you can make a rollback
     *
     * @copyright Elementor
     * @license GPLv3
     */
    public function get_rollback_versions()
    {
        $rollback_versions = get_transient('dce_rollback_versions_' . DCE_VERSION);
        if (\false === $rollback_versions) {
            $max_versions = 30;
            $versions = $this->get_versions();
            if (is_wp_error($versions)) {
                return [];
            }
            $rollback_versions = [];
            $current_index = 0;
            foreach ($versions as $version) {
                if ($max_versions <= $current_index) {
                    break;
                }
                $lowercase_version = \strtolower($version);
                $is_valid_rollback_version = !\preg_match('/(trunk|beta|rc|dev)/i', $lowercase_version);
                if (!$is_valid_rollback_version) {
                    continue;
                }
                if (\version_compare($version, DCE_VERSION, '>=')) {
                    continue;
                }
                $current_index++;
                $rollback_versions[] = $version;
            }
            set_transient('dce_rollback_versions_' . DCE_VERSION, $rollback_versions, WEEK_IN_SECONDS);
        }
        return $rollback_versions;
    }
    /**
     * Return the URL to download a specific version
     *
     * @copyright Elementor
     * @license GPLv3
     */
    protected function get_plugin_package_url($version)
    {
        $url = DCE_LICENSE_URL . '/versions.php';
        $body_args = ['item_name' => DCE_SLUG, 'version' => $version, 'license' => \DynamicContentForElementor\Plugin::instance()->license_system->get_license_key(), 'url' => \DynamicContentForElementor\Plugin::instance()->license_system->get_current_domain(), 'action' => 'download'];
        $response = wp_remote_get($url, ['timeout' => 40, 'body' => $body_args]);
        if (is_wp_error($response)) {
            return $response;
        }
        $response_code = (int) wp_remote_retrieve_response_code($response);
        $data = \json_decode(wp_remote_retrieve_body($response), \true);
        if (401 === $response_code) {
            return new \WP_Error($response_code, $data['message']);
        }
        if (200 !== $response_code) {
            return new \WP_Error($response_code, esc_html__('HTTP Error', 'dynamic-content-for-elementor'));
        }
        if (empty($data) || !\is_array($data)) {
            return new \WP_Error('no_json', esc_html__('An error occurred, please try again', 'dynamic-content-for-elementor'));
        }
        return $data['package_url'];
    }
    /**
     * Retrieve all versions of the plugin
     *
     * @copyright Elementor
     * @license GPLv3
     */
    private function get_versions()
    {
        $url = DCE_LICENSE_URL . '/versions.php';
        $body_args = ['item_name' => DCE_SLUG, 'version' => DCE_VERSION, 'license' => \DynamicContentForElementor\Plugin::instance()->license_system->get_license_key(), 'url' => \DynamicContentForElementor\Plugin::instance()->license_system->get_current_domain(), 'action' => 'list'];
        $response = wp_remote_get($url, ['timeout' => 40, 'body' => $body_args]);
        if (is_wp_error($response)) {
            return $response;
        }
        $response_code = (int) wp_remote_retrieve_response_code($response);
        $data = \json_decode(wp_remote_retrieve_body($response), \true);
        if (401 === $response_code) {
            return new \WP_Error($response_code, $data['message']);
        }
        if (200 !== $response_code) {
            return new \WP_Error($response_code, esc_html__('HTTP Error', 'dynamic-content-for-elementor'));
        }
        if (empty($data) || !\is_array($data)) {
            return new \WP_Error('no_json', esc_html__('An error occurred, please try again', 'dynamic-content-for-elementor'));
        }
        return $data;
    }
}
