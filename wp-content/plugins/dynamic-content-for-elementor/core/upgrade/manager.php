<?php

namespace DynamicContentForElementor\Core\Upgrade;

use Elementor\Core\Upgrade\Manager as Upgrades_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Manager extends Upgrades_Manager
{
    public function get_action()
    {
        return 'dce_updater';
    }
    public function get_plugin_name()
    {
        return DCE_SLUG;
    }
    public function get_plugin_label()
    {
        return DCE_PRODUCT_NAME_LONG;
    }
    public function get_updater_label()
    {
        return DCE_PRODUCT_NAME_LONG . ' ' . esc_html__('Data Updater', 'dynamic-content-for-elementor');
    }
    // Suffixes like -beta1 or -dev should not influence upgrade logic.
    public function get_clean_version($version)
    {
        if (!$version) {
            return \false;
        }
        \preg_match('/^([\\d.]+)/', $version, $matches);
        return $matches[1];
    }
    public function get_new_version()
    {
        return $this->get_clean_version(DCE_VERSION);
    }
    public function get_current_version()
    {
        if (null === $this->current_version) {
            $this->current_version = $this->get_clean_version(parent::get_current_version());
        }
        return $this->current_version;
    }
    public function get_version_option_name()
    {
        return 'dce_version';
    }
    public function get_upgrades_class()
    {
        return 'DynamicContentForElementor\\Core\\Upgrade\\Upgrades';
    }
}
