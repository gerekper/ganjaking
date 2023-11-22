<?php

namespace DynamicContentForElementor\Widgets;

use DynamicContentForElementor\Helper;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class MetaBoxGoogleMaps extends \DynamicContentForElementor\Widgets\DynamicGoogleMaps
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-metabox-google-maps';
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('map_data_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'metabox_google_maps']);
    }
}
