<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
}
class Updates
{
    public function __construct()
    {
        if (isset($_GET['action']) && 'dce-rollback' === $_GET['action']) {
            return;
        }
        $this->build_update_checker();
    }
    /**
     * Check updates from server
     *
     * @return void
     */
    public function build_update_checker()
    {
        \Puc_v4_Factory::buildUpdateChecker($this->check_for_updates_url(), DCE__FILE__, DCE_SLUG);
    }
    /**
     * Returns the url where to check for updates
     *
     * @return string
     */
    public function check_for_updates_url()
    {
        $info = DCE_LICENSE_URL . '/info.php?s=' . Plugin::instance()->license_system->get_current_domain() . '&v=' . DCE_VERSION;
        if (Plugin::instance()->license_system->get_license_key()) {
            $info .= '&k=' . Plugin::instance()->license_system->get_license_key();
        }
        if (get_option('dce_beta', \false)) {
            $info .= '&beta=true';
        }
        return $info;
    }
}
