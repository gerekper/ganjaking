<?php

namespace DynamicContentForElementor;

use Elementor\Rollback as ElementorRollback;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Make a Rollback.
 *
 * @copyright Elementor
 * @license GPLv3
 */
class Rollback extends ElementorRollback
{
    /**
     * Print inline style.
     *
     * Add an inline CSS to the rollback page.
     *
     * @since 1.5.0
     * @access private
     */
    private function print_inline_style()
    {
        ?>
		<style>
			.wrap {
				overflow: hidden;
				max-width: 850px;
				margin: auto;
			}

			h1 {
				background: #E52600;
				text-align: center;
				color: #fff !important;
				padding: 70px !important;
				text-transform: uppercase;
				letter-spacing: 1px;
			}

			h1 img {
				max-width: 100px;
				display: block;
				margin: auto auto 50px;
			}
		</style>
		<?php 
    }
    /**
     * Apply package.
     *
     * Change the plugin data when WordPress checks for updates. This method
     * modifies package data to update the plugin from a specific URL containing
     * the version package.
     *
     * @since 1.5.0
     * @access protected
     */
    protected function apply_package()
    {
        $update_plugins = get_site_transient('update_plugins');
        if (!\is_object($update_plugins)) {
            $update_plugins = new \stdClass();
        }
        $plugin_info = new \stdClass();
        $plugin_info->new_version = $this->version;
        $plugin_info->slug = $this->plugin_slug;
        $plugin_info->package = $this->package_url;
        $plugin_info->url = DCE_LICENSE_URL;
        $update_plugins->response[$this->plugin_name] = $plugin_info;
        // Remove filters from PUC to avoid update to latest version
        remove_all_filters('site_transient_update_plugins');
        set_site_transient('update_plugins', $update_plugins);
    }
    /**
     * Upgrade.
     *
     * Run WordPress upgrade to rollback Dynamic.ooo to previous version.
     *
     * @since 1.5.0
     * @access protected
     */
    protected function upgrade()
    {
        $license_system = \DynamicContentForElementor\Plugin::instance()->license_system;
        if (!$license_system->is_license_active(\true)) {
            echo esc_html__('Cannot rollback without an active license. Please activate it.', 'dynamic-content-for-elementor');
            die;
        }
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        $logo_url = DCE_URL . '/assets/media/dce-negative.png';
        $upgrader_args = ['url' => 'update.php?action=upgrade-plugin&plugin=' . \rawurlencode($this->plugin_name), 'plugin' => $this->plugin_name, 'nonce' => 'upgrade-plugin_' . $this->plugin_name, 'title' => '<img src="' . $logo_url . '" alt="Dynamic.ooo - Dynamic Content for Elementor">' . esc_html__('Rollback to Previous Version', 'dynamic-content-for-elementor')];
        $this->print_inline_style();
        $upgrader = new \Plugin_Upgrader(new \Plugin_Upgrader_Skin($upgrader_args));
        $upgrader->upgrade($this->plugin_name);
    }
    /**
     * Run.
     *
     * Rollback Dynamic.ooo to previous versions.
     *
     * @since 1.5.0
     * @access public
     */
    public function run()
    {
        $this->apply_package();
        $this->upgrade();
    }
}
