<?php

namespace Essential_Addons_Elementor\Pro\Classes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Notice
{
    use \Essential_Addons_Elementor\Pro\Traits\Library;

    /**
     * This notice will appear if Elementor is not installed or activated or both
     */
    public function failed_to_load()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        if (did_action('eael/before_init')) {
            return;
        }

        if (get_transient('eael_install_lite')) {
            return;
        }

        $plugin = 'essential-addons-for-elementor-lite/essential_adons_elementor.php';

        if ($this->is_plugin_installed($plugin) && !$this->is_plugin_active($plugin)) {
            $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
            $message = __('<strong>Essential Addons for Elementor - Pro</strong> requires <strong>Essential Addons for Elementor</strong> plugin to be active. Please activate Essential Addons for Elementor to continue.', 'essential-addons-elementor');
            $button_text = __('Activate Essential Addons for Elementor', 'essential-addons-elementor');
        } else if(!$this->is_plugin_installed($plugin)) {
            $activation_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=essential-addons-for-elementor-lite'), 'install-plugin_essential-addons-for-elementor-lite');
            $message = sprintf(__('<strong>Essential Addons for Elementor - Pro</strong> requires <strong>Essential Addons for Elementor</strong> plugin to be installed and activated. Please install Essential Addons for Elementor to continue.', 'essential-addons-elementor'), '<strong>', '</strong>');
            $button_text = __('Install Essential Addons for Elementor', 'essential-addons-elementor');
        }

		if(!empty($activation_url)){
			$button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
			printf('<div class="error"><p>%1$s</p>%2$s</div>', __($message), $button);
		}
    }
}
